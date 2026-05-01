<?php

namespace App\Services;

use App\Models\FollowUp;
use App\Models\Task;
use Illuminate\Support\Carbon;

class DailyBriefService
{
    public function get(): array
    {
        $today = Carbon::today();

        $tasksPerCategory = Task::query()
            ->with('group:id,name')
            ->get()
            ->groupBy(fn($task) => $task->group?->name ?? 'Tanpa Kategori')
            ->map(fn($items, $category) => [
                'category' => $category,
                'total' => $items->count(),
            ])
            ->values()
            ->all();

        return [
            'total_tasks_today' => Task::query()->whereDate('created_at', $today)->count(),
            'overdue_tasks' => Task::query()
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->where('due_date', '<', Carbon::now())
                ->count(),
            'follow_ups_today' => FollowUp::query()->whereDate('reminder_at', $today)->count(),
            'tasks_per_category' => $tasksPerCategory,
        ];
    }
}
