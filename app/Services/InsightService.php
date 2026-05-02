<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Carbon;

class InsightService
{
    public function analyze(Task $task, int $inactiveDays = 3, int $gapDays = 2): array
    {
        $insights = [];
        $now = Carbon::now();
        $followUps = $task->followUps->sortBy('created_at')->values();
        $followUpCount = $followUps->count();

        $lastFollowUp = $followUps->last();
        $lastActivityAt = $lastFollowUp?->created_at ?? $task->created_at;

        if ($lastActivityAt && $lastActivityAt->diffInDays($now) >= $inactiveDays) {
            $insights[] = [
                'type' => 'info',
                'message' => "Task ini tidak ada aktivitas dalam {$inactiveDays} hari",
            ];
        }

        if ($followUpCount > 1) {
            $maxGap = 0;

            for ($index = 1; $index < $followUpCount; $index++) {
                $previous = $followUps[$index - 1]?->created_at;
                $current = $followUps[$index]?->created_at;

                if (! $previous || ! $current) {
                    continue;
                }

                $gap = $previous->diffInDays($current);
                if ($gap > $maxGap) {
                    $maxGap = $gap;
                }
            }

            if ($maxGap > $gapDays) {
                $insights[] = [
                    'type' => 'warning',
                    'message' => 'Jeda follow up terlalu lama',
                ];
            }
        }

        $missedCount = $followUps->where('status', 'missed')->count();
        if ($missedCount > 0) {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Beberapa follow up terlewat',
            ];
        }

        if ($task->due_date && $task->due_date->lt($now) && $task->status !== 'done') {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Task sudah melewati deadline',
            ];
        }

        if ($followUpCount >= 5 && $task->status !== 'done') {
            $insights[] = [
                'type' => 'info',
                'message' => 'Task memiliki banyak aktivitas tapi belum selesai',
            ];
        }

        $healthScore = $this->calculateScore($task, $followUps, $now);
        $insights[] = [
            'type' => $healthScore < 60 ? 'warning' : 'info',
            'message' => "Skor kesehatan task: {$healthScore}/100",
        ];

        if ($insights === []) {
            $insights[] = [
                'type' => 'info',
                'message' => 'Task berjalan baik dan aktivitas follow up masih sehat',
            ];
        }

        return $insights;
    }

    private function calculateScore(Task $task, $followUps, Carbon $now): int
    {
        $score = 100;

        if ($task->due_date && $task->due_date->lt($now) && $task->status !== 'done') {
            $score -= 30;
        }

        $missedCount = $followUps->where('status', 'missed')->count();
        $score -= min(30, $missedCount * 10);

        $lastActivityAt = $followUps->last()?->created_at ?? $task->created_at;
        if ($lastActivityAt) {
            $inactiveDays = $lastActivityAt->diffInDays($now);
            if ($inactiveDays >= 3) {
                $score -= min(25, ($inactiveDays - 2) * 5);
            }
        }

        if ($task->status === 'done') {
            $score = min(100, $score + 10);
        }

        return max(0, min(100, $score));
    }
}
