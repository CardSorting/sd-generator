<?php

namespace App\Http\Controllers;

use App\Services\ActivityFeedService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(
        private ActivityFeedService $activityFeedService
    ) {}

    public function index(Request $request)
    {
        $activities = $this->activityFeedService->getActivities(
            $request->user(),
            $request->get('type'),
            $request->get('search'),
            $request->get('start_date'),
            $request->get('end_date'),
            $request->get('per_page', 10)
        );

        $filters = $this->activityFeedService->getFilters();
        $stats = $this->activityFeedService->getStatistics($request->user());

        // Format statistics for view
        $statistics = [
            'Total Activities' => $stats['total'],
        ];

        foreach ($stats['by_type'] as $type => $count) {
            $statistics[ucfirst(str_replace('_', ' ', $type))] = $count;
        }

        return view('activities.index', [
            'activities' => $activities,
            'filters' => $filters,
            'statistics' => $statistics,
        ]);
    }

    public function recent(Request $request)
    {
        $activities = $this->activityFeedService->getRecentActivities(
            $request->user(),
            $request->get('limit', 5)
        );

        return view('activities.recent', [
            'activities' => $activities,
        ]);
    }
}
