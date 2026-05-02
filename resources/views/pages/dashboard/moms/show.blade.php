@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="flex items-start gap-3">
        <a href="{{ route('moms.index') }}" class="mt-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition shrink-0">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12l7-7M5 12l7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <div>
          <p class="text-sm font-medium text-brand-500 dark:text-brand-400">MOM</p>
          <h1 class="mt-0.5 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $mom->title }}</h1>
          <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
            <span>📅 {{ $mom->meeting_date->format('d M Y, H:i') }}</span>
            @if($mom->location)<span>📍 {{ $mom->location }}</span>@endif
            @if($mom->taskGroup)<span>🗂 {{ $mom->taskGroup->name }}</span>@endif
            <span>👤 {{ $mom->creator->name ?? '-' }}</span>
          </div>
        </div>
      </div>
      <div class="flex shrink-0 gap-2">
        @php
          $statusMap = [
            'draft'   => ['label' => 'Draft',   'class' => 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300'],
            'ongoing' => ['label' => 'Ongoing', 'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400'],
            'closed'  => ['label' => 'Closed',  'class' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400'],
          ];
          $s = $statusMap[$mom->status] ?? $statusMap['draft'];
        @endphp
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $s['class'] }}">{{ $s['label'] }}</span>
        <a href="{{ route('moms.edit', $mom) }}"
           class="inline-flex h-9 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
          Edit
        </a>
      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400">
    {{ session('success') }}
  </div>
  @endif

  {{-- Description --}}
  @if($mom->description)
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Ringkasan Meeting</h2>
    <p class="whitespace-pre-line text-sm text-gray-600 dark:text-gray-300">{{ $mom->description }}</p>
  </div>
  @endif

  {{-- Action Items --}}
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800 px-6 py-4">
      <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Action Items ({{ $mom->items->count() }})</h2>
      @if($mom->items->isNotEmpty())
      <button type="button" id="bulk-create-task-btn"
        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:border-brand-600 dark:text-brand-400 dark:hover:bg-brand-500/10 transition">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Buat Task
      </button>
      @endif
    </div>
    @if($mom->items->isEmpty())
    <div class="py-12 text-center text-sm text-gray-400">Tidak ada action item.</div>
    @else
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
      @foreach($mom->items as $item)
      <div class="flex items-start justify-between gap-4 px-6 py-4">
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $item->action }}</p>
          <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
            @if($item->pic)<span>👤 {{ $item->pic }}</span>@endif
            @if($item->due_date)<span>⏰ {{ $item->due_date->format('d M Y') }}</span>@endif
            @if($item->note)<span class="italic">💬 {{ $item->note }}</span>@endif
          </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          @php
            $itemStatus = [
              'pending'  => ['label' => 'Pending',     'class' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400'],
              'progress' => ['label' => 'In Progress',  'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400'],
              'done'     => ['label' => 'Done',          'class' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400'],
            ];
            $is = $itemStatus[$item->status] ?? $itemStatus['pending'];
          @endphp
          <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $is['class'] }}">{{ $is['label'] }}</span>
          @if($item->status !== 'done')
          <button type="button" class="create-task-btn rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10 transition"
            data-item-id="{{ $item->id }}" data-action="{{ $item->action }}" data-pic="{{ $item->pic }}" data-due-date="{{ $item->due_date?->format('Y-m-d') }}" data-note="{{ $item->note }}">
            + Task
          </button>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Attachments --}}
  @if($mom->attachments->isNotEmpty())
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <h2 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Lampiran ({{ $mom->attachments->count() }})</h2>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($mom->attachments as $att)
      <a href="{{ Storage::url($att->file_path) }}" target="_blank"
         class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 hover:bg-gray-100 dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-white/[0.05] transition">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="shrink-0 text-gray-400">
          <path d="M7.75 4.75C6.64543 4.75 5.75 5.64543 5.75 6.75V17.25C5.75 18.3546 6.64543 19.25 7.75 19.25H16.25C17.3546 19.25 18.25 18.3546 18.25 17.25V9.56066C18.25 9.03022 18.0393 8.52152 17.6642 8.14645L14.8536 5.33579C14.4785 4.96071 13.9698 4.75 13.4393 4.75H7.75Z" stroke="currentColor" stroke-width="1.5"/>
        </svg>
        <span class="min-w-0 truncate text-sm text-gray-700 dark:text-gray-300">{{ $att->file_name }}</span>
      </a>
      @endforeach
    </div>
  </div>
  @endif

</div>

{{-- Modal: Buat Task dari Action Item --}}
<div id="task-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
  <div class="flex min-h-screen items-center justify-center bg-black/50 px-4 py-6">
    <div class="relative w-full max-w-2xl rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      {{-- Header --}}
      <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Buat Task dari Action Item</h2>
        <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>

      {{-- Body --}}
      <form action="{{ route('moms.create-task', $mom) }}" method="POST" class="space-y-4 p-6">
        @csrf

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Task Title <span class="text-error-500">*</span></label>
          <input type="text" name="title" id="task-title" required placeholder="Judul task dari action item"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
          <textarea name="description" id="task-description" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            placeholder="Deskripsi detail task..."></textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Task Group</label>
            <select name="task_group_id"
              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
              <option value="">-- Pilih Category --</option>
              @if($mom->task_group_id)
              <option value="{{ $mom->task_group_id }}" selected>{{ $mom->taskGroup?->name ?? 'MOM Group' }}</option>
              @endif
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
            <select name="priority"
              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
            <input type="date" name="due_date" id="task-due-date"
              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">PIC / Assignee</label>
            <input type="text" name="pic" id="task-pic" placeholder="Nama PIC..."
              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
          </div>
        </div>

        <input type="hidden" name="mom_id" value="{{ $mom->id }}">

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
          <button type="button" id="cancel-modal"
            class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
            Batal
          </button>
          <button type="submit"
            class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
            Buat Task
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  const modal = document.getElementById('task-modal');
  const titleInput = document.getElementById('task-title');
  const dueDateInput = document.getElementById('task-due-date');
  const picInput = document.getElementById('task-pic');
  const descInput = document.getElementById('task-description');
  
  const openModal = (btn) => {
    titleInput.value = btn.dataset.action || '';
    dueDateInput.value = btn.dataset.dueDate || '';
    picInput.value = btn.dataset.pic || '';
    descInput.value = btn.dataset.note || '';
    modal.classList.remove('hidden');
    titleInput.focus();
  };
  
  const closeModal = () => {
    modal.classList.add('hidden');
    titleInput.value = '';
    dueDateInput.value = '';
    picInput.value = '';
    descInput.value = '';
  };

  document.querySelectorAll('.create-task-btn').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn));
  });

  document.getElementById('bulk-create-task-btn')?.addEventListener('click', () => {
    titleInput.value = '';
    dueDateInput.value = '';
    picInput.value = '';
    descInput.value = '';
    modal.classList.remove('hidden');
    titleInput.focus();
  });

  document.getElementById('close-modal').addEventListener('click', closeModal);
  document.getElementById('cancel-modal').addEventListener('click', closeModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
})();
</script>
@endsection
