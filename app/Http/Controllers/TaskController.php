<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskGroup;
use Illuminate\Http\Request;
use App\Services\InsightService;
use App\Services\TaskService;

class TaskController extends Controller
{
    protected $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tasks = Task::with([
            'group',
            'followUps' => fn($query) => $query
                ->with('attachments')
                ->orderBy('created_at', 'asc'),
        ])
            ->latest()
            ->paginate(10);
        $groups = TaskGroup::orderBy('name')->get();
        $insightsByTask = $tasks->getCollection()->mapWithKeys(function ($task) {
            return [$task->id => app(InsightService::class)->analyze($task)];
        })->all();

        return view('pages.second-brain.task.index', compact('tasks', 'groups', 'insightsByTask'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_group_id' => ['nullable', 'exists:task_groups,id'],
        ]);

        $this->service->create($validated);

        return redirect()->back()->with('success', 'Task berhasil dibuat');
    }

    public function quickCapture(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_group_id' => ['nullable', 'exists:task_groups,id'],
        ]);

        $this->service->quickCapture($validated);

        return redirect()->back()->with('success', 'Quick capture berhasil ditambahkan');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'task_group_id' => ['nullable', 'exists:task_groups,id'],
            'description' => ['nullable', 'string'],
            'source' => ['nullable', 'in:work,atresna,personal'],
            'status' => ['nullable', 'in:todo,progress,done,cancel'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $this->service->update($task, $validated);

        return back()->with('success', 'Task diupdate');
    }

    public function complete(Task $task)
    {
        $this->service->complete($task);

        return back()->with('success', 'Task selesai');
    }
}
