<?php

namespace App\Providers;

use App\Models\FollowUp;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.header.notification-dropdown', function ($view): void {
            if (! Auth::check()) {
                $view->with([
                    'headerNotifications' => [],
                    'headerNotificationSummary' => [
                        'total' => 0,
                        'need_action' => 0,
                        'need_follow_up' => 0,
                    ],
                ]);

                return;
            }

            $now = Carbon::now();

            $needActionNotifications = Task::query()
                ->with('group:id,name')
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->where('due_date', '<=', $now)
                ->orderBy('due_date')
                ->take(6)
                ->get()
                ->map(function (Task $task) use ($now) {
                    $dueDate = $task->due_date;

                    return [
                        'id' => 'task-' . $task->id,
                        'type' => 'Need Action',
                        'title' => $task->title,
                        'description' => 'Task overdue dan perlu ditindaklanjuti.',
                        'meta' => $task->group?->name ?? 'Tanpa kategori',
                        'time' => $dueDate?->diffForHumans($now, short: true) ?? '-',
                        'icon' => 'T',
                        'tone' => 'warning',
                        'url' => route('tasks.index'),
                        'timestamp' => $dueDate ?? $task->updated_at ?? $task->created_at,
                    ];
                });

            $needFollowUpNotifications = FollowUp::query()
                ->with('task:id,title')
                ->where('status', 'pending')
                ->whereNotNull('reminder_at')
                ->whereDate('reminder_at', '<=', $now->copy()->toDateString())
                ->orderBy('reminder_at')
                ->take(6)
                ->get()
                ->map(function (FollowUp $followUp) use ($now) {
                    $reminderAt = $followUp->reminder_at;

                    return [
                        'id' => 'follow-up-' . $followUp->id,
                        'type' => 'Need Follow Up',
                        'title' => $followUp->title,
                        'description' => 'Reminder follow up perlu dieksekusi.',
                        'meta' => $followUp->task?->title ?? 'Tanpa task',
                        'time' => $reminderAt?->diffForHumans($now, short: true) ?? '-',
                        'icon' => 'F',
                        'tone' => 'info',
                        'url' => route('tasks.index'),
                        'timestamp' => $reminderAt ?? $followUp->updated_at ?? $followUp->created_at,
                    ];
                });

            $notifications = $needActionNotifications
                ->concat($needFollowUpNotifications)
                ->sortByDesc(function (array $item) {
                    return optional($item['timestamp'])->timestamp ?? 0;
                })
                ->take(8)
                ->values()
                ->all();

            $view->with([
                'headerNotifications' => $notifications,
                'headerNotificationSummary' => [
                    'total' => count($notifications),
                    'need_action' => $needActionNotifications->count(),
                    'need_follow_up' => $needFollowUpNotifications->count(),
                ],
            ]);
        });
    }
}
