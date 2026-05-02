<?php

namespace App\Services;

use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;

class TaskService
{
    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'todo';
        $data['priority'] = $data['priority'] ?? 'medium';

        $task = Task::create($data);

        ActivityLog::create([
            'task_id' => $task->id,
            'type' => 'created',
            'description' => 'Task dibuat'
        ]);

        return $task;
    }

    public function quickCapture(array $data): Task
    {
        $data['status'] = 'todo';
        $data['priority'] = 'medium';
        $data['due_date'] = $this->parseDueDateFromTitle($data['title'] ?? null);

        return $this->create($data);
    }

    private function parseDueDateFromTitle(?string $title): ?Carbon
    {
        if (! $title) {
            return null;
        }

        if (preg_match('/\\bbesok\\s+jam\\s+(\\d{1,2})(?::(\\d{2}))?\\b/i', $title, $matches) === 1) {
            $hour = min(23, max(0, (int) ($matches[1] ?? 0)));
            $minute = min(59, max(0, (int) ($matches[2] ?? 0)));

            return Carbon::now()->addDay()->setTime($hour, $minute, 0);
        }

        return null;
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);

        ActivityLog::create([
            'task_id' => $task->id,
            'type' => 'updated',
            'description' => 'Task diperbarui'
        ]);

        return $task;
    }

    public function complete(Task $task)
    {
        $task->update(['status' => 'done']);

        ActivityLog::create([
            'task_id' => $task->id,
            'type' => 'completed',
            'description' => 'Task selesai'
        ]);
    }
}
