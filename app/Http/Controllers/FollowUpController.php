<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Illuminate\Http\Request;
use App\Models\Task;

class FollowUpController extends Controller
{
    public function addFollowUP(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'reminder_at' => ['required', 'date'],
            'next_follow_up_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'],
        ]);

        $followUp = FollowUp::create([
            'task_id' => $task->id,
            'title' => $validated['title'],
            'note' => $validated['note'] ?? null,
            'description' => $validated['description'] ?? null,
            'reminder_at' => $validated['reminder_at'],
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
        ]);

        foreach ($request->file('files', []) as $file) {
            $followUp->attachments()->create([
                'file_path' => $file->store('followups', 'public'),
                'file_name' => $file->getClientOriginalName(),
            ]);
        }

        // log activity
        $task->logs()->create([
            'type' => 'followup_added',
            'description' => 'Menambahkan follow up: ' . $followUp->title
        ]);

        return back()->with('success', 'Follow up ditambahkan');
    }
}
