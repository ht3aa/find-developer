<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\DeveloperStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SuspendCauserRequest;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity log entries.
     */
    public function index(Request $request): Response
    {
        $modelClass = config('activitylog.activity_model');
        $query = $modelClass::query()
            ->with(['causer', 'subject'])
            ->orderByDesc('created_at');

        $search = $request->query('search');
        if (is_string($search) && trim($search) !== '') {
            $term = '%'.addcslashes(trim($search), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhere('subject_type', 'like', $term)
                    ->orWhere('log_name', 'like', $term)
                    ->orWhere('event', 'like', $term)
                    ->orWhereHasMorph('causer', [User::class, Developer::class], function ($sub) use ($term) {
                        $sub->where('email', 'like', $term);
                    });
            });
        }

        $logName = $request->query('log_name');
        if (is_string($logName) && $logName !== '') {
            $query->where('log_name', $logName);
        }

        $groupBy = $request->query('group_by');
        if ($groupBy === 'causer') {
            $query->orderBy('causer_type')->orderBy('causer_id')->orderByDesc('created_at');
        }

        $activities = $query->paginate(20)->withQueryString()->through(function ($activity) {
            $developer = null;
            if ($activity->causer_type === User::class && $activity->causer_id) {
                $developer = Developer::withoutGlobalScopes()
                    ->where('user_id', $activity->causer_id)
                    ->first();
            } elseif ($activity->causer_type === Developer::class && $activity->causer_id) {
                $developer = Developer::withoutGlobalScopes()->find($activity->causer_id);
            }
            $causerAlreadySuspended = $developer?->status === DeveloperStatus::SUSPENDED;

            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'causer_type' => $activity->causer_type ? class_basename($activity->causer_type) : null,
                'causer_type_full' => $activity->causer_type,
                'causer_id' => $activity->causer_id,
                'causer_name' => $activity->causer?->name ?? $activity->causer?->email ?? null,
                'causer_email' => isset($activity->causer->email) ? (string) $activity->causer->email : null,
                'causer_already_suspended' => $causerAlreadySuspended,
                'event' => $activity->event,
                'created_at' => $activity->created_at->toIso8601String(),
            ];
        });

        return Inertia::render('ActivityLog/Index', [
            'activities' => $activities,
            'filters' => [
                'search' => is_string($search) ? trim($search) : '',
                'log_name' => is_string($logName) ? $logName : '',
                'group_by' => $groupBy === 'causer' ? 'causer' : '',
            ],
            'suspendCauserUrl' => route('dashboard.activity-log.suspend-causer'),
        ]);
    }

    /**
     * Suspend the causer's developer profile.
     */
    public function suspendCauser(SuspendCauserRequest $request): RedirectResponse|JsonResponse
    {
        $causerType = $request->validated('causer_type');
        $causerId = $request->validated('causer_id');
        $developer = null;

        if ($causerType === User::class) {
            $user = User::find($causerId);
            if ($user?->developer) {
                $developer = $user->developer;
            }
        } elseif ($causerType === Developer::class) {
            $developer = Developer::withoutGlobalScopes()->find($causerId);
        }

        if (! $developer) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No developer profile found for this causer.'], 422);
            }

            return back()->withErrors(['causer' => 'No developer profile found for this causer.']);
        }

        $developer->update(['status' => DeveloperStatus::SUSPENDED]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Developer profile suspended.']);
        }

        return back()->with('success', 'Developer profile suspended.');
    }

    /**
     * Display the specified activity log entry.
     */
    public function show(int $id): Response
    {
        $modelClass = config('activitylog.activity_model');
        $activity = $modelClass::with(['causer', 'subject'])->findOrFail($id);

        $subjectLabel = null;
        if ($activity->subject) {
            $subject = $activity->subject;
            if (method_exists($subject, 'getActivityLogSubjectLabel')) {
                $subjectLabel = $subject->getActivityLogSubjectLabel();
            } elseif (isset($subject->name)) {
                $subjectLabel = (string) $subject->name;
            } elseif (isset($subject->title)) {
                $subjectLabel = (string) $subject->title;
            } else {
                $subjectLabel = (string) $activity->subject_id;
            }
        }

        return Inertia::render('ActivityLog/Show', [
            'activity' => [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_type_short' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'subject_label' => $subjectLabel,
                'causer_type' => $activity->causer_type,
                'causer_type_short' => $activity->causer_type ? class_basename($activity->causer_type) : null,
                'causer_id' => $activity->causer_id,
                'causer_name' => $activity->causer?->name ?? $activity->causer?->email ?? null,
                'event' => $activity->event,
                'batch_uuid' => $activity->batch_uuid ?? null,
                'properties' => $activity->properties?->toArray() ?? [],
                'created_at' => $activity->created_at->toIso8601String(),
                'updated_at' => $activity->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Return the properties (attributes / old values) for an activity log entry.
     */
    public function properties(int $id): JsonResponse
    {
        $modelClass = config('activitylog.activity_model');
        $activity = $modelClass::find($id);

        if (! $activity) {
            abort(404);
        }

        return response()->json([
            'properties' => $activity->properties?->toArray() ?? [],
        ]);
    }
}
