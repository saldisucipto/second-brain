@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center gap-3">
      <a href="{{ route('moms.show', $mom) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12l7-7M5 12l7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <div>
        <p class="text-sm font-medium text-brand-500 dark:text-brand-400">MOM</p>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Edit MOM</h1>
      </div>
    </div>
  </div>

  <form action="{{ route('moms.update', $mom) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Info Meeting --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Informasi Meeting</h2>
      <div class="grid gap-5 sm:grid-cols-2">

        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Meeting <span class="text-error-500">*</span></label>
          <input type="text" name="title" value="{{ old('title', $mom->title) }}"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('title') border-error-500 @enderror">
          @error('title')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal & Waktu Meeting <span class="text-error-500">*</span></label>
          <input type="datetime-local" name="meeting_date"
            value="{{ old('meeting_date', $mom->meeting_date->format('Y-m-d\TH:i')) }}"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('meeting_date') border-error-500 @enderror">
          @error('meeting_date')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
          <input type="text" name="location" value="{{ old('location', $mom->location) }}"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Task Group</label>
          <select name="task_group_id"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">-- Pilih Task Group --</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}" @selected(old('task_group_id', $mom->task_group_id) == $group->id)>{{ $group->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
          <select name="status"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="draft" @selected(old('status', $mom->status)=='draft')>Draft</option>
            <option value="ongoing" @selected(old('status', $mom->status)=='ongoing')>Ongoing</option>
            <option value="closed" @selected(old('status', $mom->status)=='closed')>Closed</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Ringkasan / Deskripsi</label>
          <textarea name="description" rows="4"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description', $mom->description) }}</textarea>
        </div>

      </div>
    </div>

    {{-- Action Items --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="mb-5 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Action Items <span class="text-error-500">*</span></h2>
        <button type="button" id="add-item"
          class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:border-brand-600 dark:text-brand-400 dark:hover:bg-brand-500/10 transition">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Tambah Item
        </button>
      </div>

      @error('items')<p class="mb-3 text-xs text-error-500">{{ $message }}</p>@enderror

      <div id="items-container" class="space-y-4">
        @forelse($mom->items as $i => $item)
        <div class="item-row rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-white/[0.02]">
          <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="sm:col-span-2 lg:col-span-3">
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Action <span class="text-error-500">*</span></label>
              <input type="text" name="items[{{ $i }}][action]" value="{{ old('items.'.$i.'.action', $item->action) }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">PIC</label>
              <input type="text" name="items[{{ $i }}][pic]" value="{{ old('items.'.$i.'.pic', $item->pic) }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Status</label>
              <select name="items[{{ $i }}][status]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="pending" @selected(old('items.'.$i.'.status', $item->status)=='pending')>Pending</option>
                <option value="progress" @selected(old('items.'.$i.'.status', $item->status)=='progress')>In Progress</option>
                <option value="done" @selected(old('items.'.$i.'.status', $item->status)=='done')>Done</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Due Date</label>
              <input type="datetime-local" name="items[{{ $i }}][due_date]"
                value="{{ old('items.'.$i.'.due_date', $item->due_date?->format('Y-m-d\TH:i')) }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Catatan</label>
              <input type="text" name="items[{{ $i }}][note]" value="{{ old('items.'.$i.'.note', $item->note) }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
          </div>
          @if($loop->index > 0)
          <div class="mt-2 flex justify-end">
            <button type="button" class="remove-item text-xs text-error-500 hover:text-error-600">Hapus item ini</button>
          </div>
          @endif
        </div>
        @empty
        <div class="item-row rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-white/[0.02]">
          <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="sm:col-span-2 lg:col-span-3">
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Action <span class="text-error-500">*</span></label>
              <input type="text" name="items[0][action]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">PIC</label>
              <input type="text" name="items[0][pic]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Status</label>
              <select name="items[0][status]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="pending">Pending</option>
                <option value="progress">In Progress</option>
                <option value="done">Done</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Due Date</label>
              <input type="datetime-local" name="items[0][due_date]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
              <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Catatan</label>
              <input type="text" name="items[0][note]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>
          </div>
        </div>
        @endforelse
      </div>
    </div>

    {{-- Tambah Lampiran Baru --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <h2 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Tambah Lampiran Baru</h2>
      <label class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 p-6 text-center hover:border-brand-300 dark:border-gray-700 dark:hover:border-brand-600 transition">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Klik untuk upload file</p>
        <p class="mt-1 text-xs text-gray-400">JPG, PNG, PDF, DOC, XLS, ZIP — maks 10MB</p>
        <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" class="hidden">
      </label>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-end gap-3">
      <a href="{{ route('moms.show', $mom) }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-200 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
        Batal
      </a>
      <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        Update MOM
      </button>
    </div>

  </form>
</div>

<script>
(function () {
  let idx = {{ $mom->items->count() ?: 1 }};

  document.getElementById('add-item').addEventListener('click', function () {
    const tpl = document.querySelector('.item-row').cloneNode(true);
    tpl.querySelectorAll('input, select, textarea').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, '[' + idx + ']');
      el.value = '';
    });
    const removeBtn = document.createElement('div');
    removeBtn.className = 'mt-2 flex justify-end';
    removeBtn.innerHTML = '<button type="button" class="remove-item text-xs text-error-500 hover:text-error-600">Hapus item ini</button>';
    tpl.appendChild(removeBtn);
    document.getElementById('items-container').appendChild(tpl);
    idx++;
  });

  document.getElementById('items-container').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-item')) {
      e.target.closest('.item-row').remove();
    }
  });
})();
</script>
@endsection
