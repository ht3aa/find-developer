<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\HackathonAttendanceIndexRequest;
use App\Http\Requests\Dashboard\UpdateHackathonAttendanceRequest;
use App\Models\Hackathon;
use App\Models\HackathonAttendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HackathonAttendanceController extends Controller
{
    public function index(HackathonAttendanceIndexRequest $request, Hackathon $hackathon): Response
    {
        $hackathon->loadMissing('subscribers.developer:id,name,slug');

        $start = $hackathon->start_date?->toDateString();
        $end = $hackathon->end_date?->toDateString();
        $dates = [];
        if ($start && $end) {
            $current = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            while ($current->lte($endDate)) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        $subscribers = $hackathon->subscribers()
            ->with('developer:id,name,slug')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'developer_id' => $s->developer_id,
                'developer' => $s->developer ? [
                    'id' => $s->developer->id,
                    'name' => $s->developer->name,
                    'slug' => $s->developer->slug,
                ] : null,
            ])
            ->filter(fn ($s) => $s['developer_id'] !== null)
            ->values()
            ->all();

        $attendances = $hackathon->attendances()
            ->get()
            ->groupBy('developer_id')
            ->map(fn ($rows) => $rows->keyBy(fn ($r) => $r->date->format('Y-m-d')))
            ->map(fn ($byDate) => $byDate->map(fn ($r) => $r->attended)->all())
            ->all();

        return Inertia::render('Hackathons/Dashboard/Attendance', [
            'hackathon' => [
                'id' => $hackathon->id,
                'title' => $hackathon->title,
                'start_date' => $hackathon->start_date?->format('Y-m-d'),
                'end_date' => $hackathon->end_date?->format('Y-m-d'),
            ],
            'dates' => $dates,
            'subscribers' => $subscribers,
            'attendances' => $attendances,
            'updateUrl' => route('hackathons.attendance.update', $hackathon),
        ]);
    }

    public function update(UpdateHackathonAttendanceRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $validated = $request->validated();

        HackathonAttendance::updateOrCreate(
            [
                'developer_id' => $validated['developer_id'],
                'hackathon_id' => $hackathon->id,
                'date' => $validated['date'],
            ],
            ['attended' => $validated['attended']]
        );

        return redirect()->route('hackathons.attendance.index', $hackathon)
            ->with('success', 'Attendance updated.');
    }
}
