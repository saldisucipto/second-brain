<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateRecurringTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-recurring-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new tasks from recurring task templates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();

        $templates = Task::query()
            ->where('is_recurring', true)
            ->whereNotNull('repeat_type')
            ->where('repeat_interval', '>', 0)
            ->get();

        $generated = 0;

        foreach ($templates as $template) {
            $lastGeneratedAt = $template->last_generated_at;
            $reference = $lastGeneratedAt ?? $template->created_at ?? $now;

            $nextGenerationAt = match ($template->repeat_type) {
                'daily' => $reference->copy()->addDays((int) $template->repeat_interval),
                'weekly' => $reference->copy()->addWeeks((int) $template->repeat_interval),
                'monthly' => $reference->copy()->addMonths((int) $template->repeat_interval),
                default => null,
            };

            if (! $nextGenerationAt || $nextGenerationAt->gt($now)) {
                continue;
            }

            $newDueDate = $template->due_date
                ? $this->nextDueDate($template->due_date, $template->repeat_type, (int) $template->repeat_interval)
                : null;

            Task::query()->create([
                'title' => $template->title,
                'task_group_id' => $template->task_group_id,
                'priority' => $template->priority,
                'status' => 'todo',
                'due_date' => $newDueDate,
                'is_recurring' => false,
                'repeat_type' => null,
                'repeat_interval' => 1,
                'last_generated_at' => null,
            ]);

            $template->update([
                'last_generated_at' => $now,
            ]);

            $generated++;
        }

        $this->info("Generated {$generated} recurring task(s).");

        return self::SUCCESS;
    }

    private function nextDueDate(Carbon $dueDate, ?string $repeatType, int $interval): ?Carbon
    {
        return match ($repeatType) {
            'daily' => $dueDate->copy()->addDays($interval),
            'weekly' => $dueDate->copy()->addWeeks($interval),
            'monthly' => $dueDate->copy()->addMonths($interval),
            default => null,
        };
    }
}
