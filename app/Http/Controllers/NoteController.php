<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'search' => trim((string) $request->query('search', '')),
            'type' => $request->query('type', ''),
        ];

        $query = Note::query()
            ->where('created_by', auth()->id())
            ->when($filter['search'], function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($filter['type'], fn($q, $type) => $q->where('type', $type))
            ->orderByDesc('is_pinned')
            ->latest();

        $notes = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => Note::where('created_by', auth()->id())->count(),
            'pinned' => Note::where('created_by', auth()->id())->where('is_pinned', true)->count(),
            'idea' => Note::where('created_by', auth()->id())->where('type', 'idea')->count(),
            'technical' => Note::where('created_by', auth()->id())->where('type', 'technical')->count(),
        ];

        return view('pages.dashboard.notes.index', compact('notes', 'filter', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['nullable', 'in:general,idea,meeting,technical'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_pinned' => ['nullable', 'boolean'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'mom_id' => ['nullable', 'exists:moms,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_pinned'] = (bool) ($validated['is_pinned'] ?? false);

        Note::create($validated);

        return redirect()->route('notes.index')->with('success', 'Note berhasil dibuat.');
    }

    public function update(Request $request, Note $note)
    {
        abort_if((int) $note->created_by !== (int) auth()->id(), 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'type' => ['nullable', 'in:general,idea,meeting,technical'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_pinned' => ['nullable', 'boolean'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'mom_id' => ['nullable', 'exists:moms,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
        ]);

        if ($request->has('is_pinned')) {
            $validated['is_pinned'] = (bool) $request->boolean('is_pinned');
        }

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Note berhasil diupdate.');
    }

    public function destroy(Note $note)
    {
        abort_if((int) $note->created_by !== (int) auth()->id(), 403);

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note berhasil dihapus.');
    }
}
