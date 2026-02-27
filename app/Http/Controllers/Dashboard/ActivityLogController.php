<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity log entries (super admin only).
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

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
                    ->orWhere('event', 'like', $term);
            });
        }

        $logName = $request->query('log_name');
        if (is_string($logName) && $logName !== '') {
            $query->where('log_name', $logName);
        }

        $activities = $query->paginate(20)->withQueryString()->through(function ($activity) {
            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'causer_type' => $activity->causer_type ? class_basename($activity->causer_type) : null,
                'causer_id' => $activity->causer_id,
                'causer_name' => $activity->causer?->name ?? $activity->causer?->email ?? null,
                'causer_email' => isset($activity->causer->email) ? (string) $activity->causer->email : null,
                'event' => $activity->event,
                'created_at' => $activity->created_at->toIso8601String(),
            ];
        });

        return Inertia::render('ActivityLog/Index', [
            'activities' => $activities,
            'filters' => [
                'search' => is_string($search) ? trim($search) : '',
                'log_name' => is_string($logName) ? $logName : '',
            ],
        ]);
    }

    /**
     * Display the specified activity log entry (super admin only).
     */
    public function show(Request $request, int $id): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

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
     * Return the properties (attributes / old values) for an activity log entry (super admin only).
     */
    public function properties(Request $request, int $id): JsonResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

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
