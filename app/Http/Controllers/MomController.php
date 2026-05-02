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
use Illuminate\Validation\ValidationException;

class MomController extends Controller
{
    public function index(Request $request)
    {
        $query = Mom::with(['taskGroup', 'items'])
            ->where('created_by', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $moms = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total'   => Mom::where('created_by', auth()->id())->count(),
            'draft'   => Mom::where('created_by', auth()->id())->where('status', 'draft')->count(),
            'ongoing' => Mom::where('created_by', auth()->id())->where('status', 'ongoing')->count(),
            'closed'  => Mom::where('created_by', auth()->id())->where('status', 'closed')->count(),
        ];

        return view('pages.dashboard.moms.index', [
            'title'  => 'Minutes of Meeting',
            'moms'   => $moms,
            'stats'  => $stats,
            'filter' => $request->only(['status', 'search']),
        ]);
    }

    public function create()
    {
        $groups = TaskGroup::orderBy('name')->get();

        return view('pages.dashboard.moms.create', [
            'title' => 'Buat MOM Baru',
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        [$attributes, $items] = $this->validatedPayload($request);

        $mom = DB::transaction(function () use ($request, $attributes, $items) {
            $mom = Mom::create($attributes + [
                'created_by' => auth()->id(),
            ]);

            $this->syncItems($mom, $items);
            $this->storeAttachments($request, $mom);

            return $mom;
        });

        return redirect()->route('moms.index')->with('success', 'MOM berhasil disimpan.');
    }

    public function edit(Mom $mom)
    {
        $groups = TaskGroup::orderBy('name')->get();
        $mom->load('items');

        return view('pages.dashboard.moms.edit', [
            'title'  => 'Edit MOM',
            'mom'    => $mom,
            'groups' => $groups,
        ]);
    }

    public function show(Mom $mom)
    {
        $mom->load(['items', 'attachments', 'taskGroup', 'creator']);

        return view('pages.dashboard.moms.show', [
            'title' => $mom->title,
            'mom'   => $mom,
        ]);
    }

    public function update(Request $request, Mom $mom)
    {
        [$attributes, $items] = $this->validatedPayload($request);

        DB::transaction(function () use ($request, $mom, $attributes, $items) {
            $mom->update($attributes);
            $this->syncItems($mom, $items);
            $this->removeAttachments($request, $mom);
            $this->storeAttachments($request, $mom);
        });

        return redirect()->route('moms.show', $mom)->with('success', 'MOM berhasil diupdate.');
    }

    public function createTask(Request $request, Mom $mom)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'task_group_id' => ['nullable', 'exists:task_groups,id'],
            'priority'      => ['required', 'in:low,medium,high'],
            'due_date'      => ['nullable', 'date'],
            'pic'           => ['nullable', 'string', 'max:255'],
        ]);

        $task = DB::transaction(function () use ($validated, $mom) {
            $task = Task::create([
                'title'         => $validated['title'],
                'description'   => $validated['description'] ? "MOM: {$mom->title}\n\n" . $validated['description'] : "Created from MOM: {$mom->title}",
                'source'        => 'work',
                'status'        => 'todo',
                'priority'      => $validated['priority'],
                'task_group_id' => $validated['task_group_id'] ?? $mom->task_group_id,
                'due_date'      => $validated['due_date'],
            ]);

            // Log activity
            ActivityLog::create([
                'task_id'     => $task->id,
                'type'        => 'created_from_mom',
                'description' => "Task dibuat dari MOM: {$mom->title}",
            ]);

            return $task;
        });

        return redirect()->route('tasks.index')->with('success', "Task '{$task->title}' berhasil dibuat dari MOM.");
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'task_group_id' => ['nullable', 'exists:task_groups,id'],
            'status' => ['required', 'in:draft,ongoing,closed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.action' => ['required', 'string'],
            'items.*.pic' => ['nullable', 'string', 'max:255'],
            'items.*.status' => ['nullable', 'in:pending,progress,done'],
            'items.*.due_date' => ['nullable', 'date'],
            'items.*.note' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'],
            'remove_attachment_ids' => ['nullable', 'array'],
            'remove_attachment_ids.*' => ['integer', 'exists:mom_attachments,id'],
        ]);

        $items = collect($validated['items'] ?? [])
            ->map(function (array $item) {
                return [
                    'action' => trim((string) ($item['action'] ?? '')),
                    'pic' => filled($item['pic'] ?? null) ? trim((string) $item['pic']) : null,
                    'status' => $item['status'] ?? 'pending',
                    'due_date' => $item['due_date'] ?? null,
                    'note' => filled($item['note'] ?? null) ? trim((string) $item['note']) : null,
                ];
            })
            ->filter(fn(array $item) => $item['action'] !== '')
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Minimal satu action item harus diisi.',
            ]);
        }

        return [[
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'meeting_date' => $validated['meeting_date'],
            'location' => $validated['location'] ?? null,
            'task_group_id' => $validated['task_group_id'] ?? null,
            'status' => $validated['status'],
        ], $items];
    }

    private function syncItems(Mom $mom, Collection $items): void
    {
        $mom->items()->delete();

        $mom->items()->createMany(
            $items->map(fn(array $item) => [
                'action' => $item['action'],
                'pic' => $item['pic'],
                'status' => $item['status'],
                'due_date' => $item['due_date'],
                'note' => $item['note'],
            ])->all()
        );
    }

    private function storeAttachments(Request $request, Mom $mom): void
    {
        foreach ($request->file('files', []) as $file) {
            $mom->attachments()->create([
                'file_path' => $file->store('moms', 'public'),
                'file_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    private function removeAttachments(Request $request, Mom $mom): void
    {
        $attachmentIds = collect($request->input('remove_attachment_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter();

        if ($attachmentIds->isEmpty()) {
            return;
        }

        $mom->attachments()->whereIn('id', $attachmentIds)->get()->each(function ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        });
    }

    private function emptyItem(): array
    {
        return [
            'action' => '',
            'pic' => '',
            'status' => 'pending',
            'due_date' => '',
            'note' => '',
        ];
    }
}
