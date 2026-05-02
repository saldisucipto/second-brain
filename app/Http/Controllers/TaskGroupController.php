<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Mom;
use App\Models\Task;
use App\Models\TaskGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskGroupController extends Controller
{
    public function index()
    {
        $groups = TaskGroup::withCount('tasks')
            ->orderBy('name')
            ->paginate(10);

        return view('pages.second-brain.task-groups.index', [
            'title' => 'Task Categories',
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:task_groups,slug'],
            'color' => ['nullable', 'in:red,green,blue,yellow'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        TaskGroup::create($validated);

        return back()->with('success', 'Kategori task berhasil dibuat.');
    }

    public function update(Request $request, TaskGroup $taskGroup)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('task_groups', 'slug')->ignore($taskGroup)],
            'color' => ['nullable', 'in:red,green,blue,yellow'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $taskGroup->update($validated);

        return back()->with('success', 'Kategori task berhasil diupdate.');
    }

    public function destroy(TaskGroup $taskGroup)
    {
        $taskGroup->delete();

        return back()->with('success', 'Kategori task berhasil dihapus.');
    }
}
