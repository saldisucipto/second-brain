<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AssetSchedule;
use App\Models\FollowUp;
use App\Models\MomItem;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActionEngineService
{
    private array $executedActions = [];

    /**
     * Run all action engine triggers
     */
    public function run(): void
    {
        try {
            Log::info('ActionEngine: Starting execution');

            $this->checkOverdueTask();
            $this->checkMissedFollowUp();
            $this->checkInactiveTask();
            $this->checkMomActionItem();
            $this->checkFollowUpLoop();
            $this->checkAssetScheduleReminder();

            Log::info('ActionEngine: Completed', [
                'actions_executed' => count($this->executedActions),
            ]);
        } catch (\Exception $e) {
            Log::error('ActionEngine: Error occurred', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    /**
     * TRIGGER A: OVERDUE TASK
     * If task due_date < now() and status != done → create follow_up
     */
    private function checkOverdueTask(): void
    {
        $overdueTasks = Task::query()
            ->where('due_date', '<', now())
            ->whereIn('status', ['todo', 'progress'])
            ->get();

        foreach ($overdueTasks as $task) {
            // Check if follow_up already created for this task within last hour
            if ($this->hasRecentFollowUp($task, 60)) {
                continue;
            }

            try {
                DB::transaction(function () use ($task) {
                    $followUp = FollowUp::create([
                        'task_id'     => $task->id,
                        'title'       => 'Task overdue - segera follow up',
                        'description' => "Task '{$task->title}' telah melewati due date pada " . $task->due_date->format('d M Y H:i'),
                        'reminder_at' => now(),
                        'status'      => 'pending',
                    ]);

                    ActivityLog::create([
                        'task_id'     => $task->id,
                        'type'        => 'auto_followup_overdue',
                        'description' => 'Follow-up otomatis dibuat untuk task yang overdue',
                    ]);

                    $this->executedActions[] = "OverdueTask: Task #{$task->id} → FollowUp #{$followUp->id}";
                    Log::info("ActionEngine: Overdue task detected - follow up created", [
                        'task_id'     => $task->id,
                        'task_title'  => $task->title,
                        'due_date'    => $task->due_date,
                        'followup_id' => $followUp->id,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking overdue task {$task->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * TRIGGER B: MISSED FOLLOW UP
     * If follow_up status = missed → create new follow_up 2 hours later
     */
    private function checkMissedFollowUp(): void
    {
        $missedFollowUps = FollowUp::query()
            ->where('status', 'missed')
            ->whereNull('next_follow_up_at')
            ->orWhere('next_follow_up_at', '<', now())
            ->get();

        foreach ($missedFollowUps as $followUp) {
            // Check if already has auto-created follow_up
            if (FollowUp::where('task_id', $followUp->task_id)
                ->whereDate('created_at', today())
                ->count() > 0) {
                continue;
            }

            try {
                DB::transaction(function () use ($followUp) {
                    $newFollowUp = FollowUp::create([
                        'task_id'     => $followUp->task_id,
                        'title'       => 'Reschedule follow-up (dari missed)',
                        'description' => "Follow-up sebelumnya (ID: {$followUp->id}) terlewat. Reschedule ke waktu yang lebih sesuai.",
                        'reminder_at' => now()->addHours(2),
                        'status'      => 'pending',
                    ]);

                    ActivityLog::create([
                        'task_id'     => $followUp->task_id,
                        'type'        => 'auto_followup_missed',
                        'description' => 'Follow-up otomatis dibuat untuk menggantikan missed follow-up',
                    ]);

                    $this->executedActions[] = "MissedFollowUp: FollowUp #{$followUp->id} → NewFollowUp #{$newFollowUp->id}";
                    Log::info("ActionEngine: Missed follow-up detected - new follow up created", [
                        'old_followup_id' => $followUp->id,
                        'task_id'         => $followUp->task_id,
                        'new_followup_id' => $newFollowUp->id,
                        'reminder_at'     => $newFollowUp->reminder_at,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking missed follow-up {$followUp->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * TRIGGER C: INACTIVE TASK
     * If no follow_up within 3 days → create follow_up
     */
    private function checkInactiveTask(): void
    {
        $inactiveTasks = Task::query()
            ->whereIn('status', ['todo', 'progress'])
            ->where(function ($query) {
                // Tasks with no follow-ups
                $query->whereDoesntHave('followUps')
                    // OR tasks where last follow-up was 3+ days ago
                    ->orWhereHas('followUps', function ($subquery) {
                        $subquery->where('created_at', '<', now()->subDays(3));
                    });
            })
            ->get();

        foreach ($inactiveTasks as $task) {
            // Check if already has recent follow_up (within last day)
            if ($this->hasRecentFollowUp($task, 1440)) { // 1440 minutes = 24 hours
                continue;
            }

            try {
                DB::transaction(function () use ($task) {
                    $followUp = FollowUp::create([
                        'task_id'     => $task->id,
                        'title'       => 'Task tidak aktif - perlu perhatian',
                        'description' => "Task '{$task->title}' tidak memiliki follow-up selama 3 hari terakhir. Perlu check status kemajuan.",
                        'reminder_at' => now(),
                        'status'      => 'pending',
                    ]);

                    ActivityLog::create([
                        'task_id'     => $task->id,
                        'type'        => 'auto_followup_inactive',
                        'description' => 'Follow-up otomatis dibuat untuk task yang inactive',
                    ]);

                    $this->executedActions[] = "InactiveTask: Task #{$task->id} → FollowUp #{$followUp->id}";
                    Log::info("ActionEngine: Inactive task detected - follow up created", [
                        'task_id'     => $task->id,
                        'task_title'  => $task->title,
                        'followup_id' => $followUp->id,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking inactive task {$task->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * TRIGGER D: MOM ACTION ITEM
     * If mom_item status = pending and due_date <= now() → create follow_up
     */
    private function checkMomActionItem(): void
    {
        $overdueItems = MomItem::query()
            ->where('status', 'pending')
            ->where('due_date', '<=', now())
            ->with('mom')
            ->get();

        foreach ($overdueItems as $item) {
            try {
                DB::transaction(function () use ($item) {
                    $mom = $item->mom;
                    
                    // Create or find related task
                    $task = Task::firstOrCreate([
                        'title' => $item->action,
                        'source' => 'work',
                    ], [
                        'description'  => "MOM Action Item dari: {$mom->title}\n\nAction: {$item->action}",
                        'status'       => 'todo',
                        'priority'     => 'medium',
                        'task_group_id' => $mom->task_group_id,
                        'due_date'     => $item->due_date,
                    ]);

                    // Create follow-up
                    $followUp = FollowUp::create([
                        'task_id'     => $task->id,
                        'title'       => "MOM Action Item: {$item->action}",
                        'description' => "PIC: {$item->pic}\nNote: {$item->note}\nDari MOM: {$mom->title}",
                        'reminder_at' => now(),
                        'status'      => 'pending',
                    ]);

                    ActivityLog::create([
                        'task_id'     => $task->id,
                        'type'        => 'auto_followup_mom_item',
                        'description' => "Follow-up otomatis dari MOM action item: {$item->action}",
                    ]);

                    $this->executedActions[] = "MomItem: Item #{$item->id} → Task #{$task->id} → FollowUp #{$followUp->id}";
                    Log::info("ActionEngine: MOM action item overdue - task and follow-up created", [
                        'mom_item_id'  => $item->id,
                        'mom_id'       => $mom->id,
                        'task_id'      => $task->id,
                        'followup_id'  => $followUp->id,
                        'action'       => $item->action,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking MOM item {$item->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * TRIGGER E: FOLLOW UP LOOP
     * If next_follow_up_at is set and <= now() → auto create follow_up
     */
    private function checkFollowUpLoop(): void
    {
        $loopFollowUps = FollowUp::query()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->get();

        foreach ($loopFollowUps as $followUp) {
            try {
                DB::transaction(function () use ($followUp) {
                    $newFollowUp = FollowUp::create([
                        'task_id'     => $followUp->task_id,
                        'title'       => "Follow-up lanjutan: {$followUp->title}",
                        'description' => "Continuation dari: {$followUp->description}",
                        'reminder_at' => now(),
                        'status'      => 'pending',
                    ]);

                    // Update previous follow-up
                    $followUp->update(['next_follow_up_at' => null]);

                    ActivityLog::create([
                        'task_id'     => $followUp->task_id,
                        'type'        => 'auto_followup_loop',
                        'description' => 'Follow-up otomatis dibuat dari follow-up loop schedule',
                    ]);

                    $this->executedActions[] = "FollowUpLoop: FollowUp #{$followUp->id} → NewFollowUp #{$newFollowUp->id}";
                    Log::info("ActionEngine: Follow-up loop triggered - new follow-up created", [
                        'previous_followup_id' => $followUp->id,
                        'task_id'              => $followUp->task_id,
                        'new_followup_id'      => $newFollowUp->id,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking follow-up loop {$followUp->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * TRIGGER F: ASSET SCHEDULE REMINDER
     * If asset schedule is overdue or needs reminder → create follow_up/task
     */
    private function checkAssetScheduleReminder(): void
    {
        $schedules = AssetSchedule::query()
            ->with('asset')
            ->where('next_due_at', '<=', now()->addDays(30))
            ->get();

        foreach ($schedules as $schedule) {
            if (! $schedule->isOverdue() && ! $schedule->shouldRemind()) {
                continue;
            }

            // Check if already created follow_up today
            if ($this->hasRecentFollowUpForAsset($schedule, 1440)) { // 1440 = 24 hours
                continue;
            }

            try {
                DB::transaction(function () use ($schedule) {
                    $isOverdue = $schedule->isOverdue();
                    $status = 'pending';
                    $title = "[Asset] {$schedule->title}";
                    $marker = "[asset_schedule_id:{$schedule->id}]";

                    // Daily deduplication per schedule
                    $alreadyCreatedToday = Task::query()
                        ->whereDate('created_at', today())
                        ->where('description', 'like', "%{$marker}%")
                        ->exists();

                    if ($alreadyCreatedToday) {
                        return;
                    }

                    // Create task
                    $task = Task::create([
                        'title'       => $title,
                        'description' => "{$marker}\nAsset: {$schedule->asset->name}\nJadwal: {$schedule->title}\nDue: {$schedule->next_due_at->format('d M Y')}",
                        'source'      => 'work',
                        'status'      => 'todo',
                        'priority'    => $isOverdue ? 'high' : 'medium',
                        'due_date'    => $schedule->next_due_at,
                    ]);

                    // Create follow-up
                    $followUp = FollowUp::create([
                        'task_id'     => $task->id,
                        'title'       => $title,
                        'description' => "Asset maintenance reminder for: {$schedule->asset->name}",
                        'reminder_at' => now(),
                        'status'      => $status,
                    ]);

                    ActivityLog::create([
                        'task_id'     => $task->id,
                        'type'        => 'auto_asset_reminder',
                        'description' => "Asset maintenance reminder created: {$schedule->title}",
                    ]);

                    $this->executedActions[] = "AssetSchedule: Schedule #{$schedule->id} → Task #{$task->id} → FollowUp #{$followUp->id}";
                    Log::info("ActionEngine: Asset schedule reminder - task and follow-up created", [
                        'schedule_id' => $schedule->id,
                        'asset_id'    => $schedule->asset_id,
                        'task_id'     => $task->id,
                        'followup_id' => $followUp->id,
                        'is_overdue'  => $isOverdue,
                    ]);
                    logger("Action Engine: follow up dibuat untuk task {$task->id}");
                });
            } catch (\Exception $e) {
                Log::error("ActionEngine: Error checking asset schedule {$schedule->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Helper: Check if asset schedule has recent follow-up
     * 
     * @param AssetSchedule $schedule
     * @param int $minutes - minutes to look back
     * @return bool
     */
    private function hasRecentFollowUpForAsset(AssetSchedule $schedule, int $minutes): bool
    {
        return FollowUp::whereIn('task_id', function ($query) use ($schedule) {
            $query->select('id')
                ->from('tasks')
                ->where('description', 'like', "%[asset_schedule_id:{$schedule->id}]%");
        })
        ->where('created_at', '>', now()->subMinutes($minutes))
        ->exists();
    }

    /**
     * Helper: Check if task has recent follow-up
     * 
     * @param Task $task
     * @param int $minutes - minutes to look back
     * @return bool
     */
    private function hasRecentFollowUp(Task $task, int $minutes): bool
    {
        return $task->followUps()
            ->where('created_at', '>', now()->subMinutes($minutes))
            ->exists();
    }
}
