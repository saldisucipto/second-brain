@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: {{ ($errors->has('title') || $errors->has('content') || $errors->has('type') || $errors->has('color')) ? 'true' : 'false' }} }">

  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-sm font-medium text-brand-500 dark:text-brand-400">Knowledge System</p>
        <h1 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">Notes</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Simpan catatan penting dalam card yang rapi, mudah dicari, dan bisa dipin.
        </p>
      </div>
      <button
        type="button"
        @click="showCreateModal = true"
        class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Create Note
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
      {{ session('success') }}
    </div>
  @endif

  <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Total Notes</p>
      <p class="mt-2 text-3xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Pinned</p>
      <p class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pinned'] ?? 0 }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Ideas</p>
      <p class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['idea'] ?? 0 }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm text-gray-500 dark:text-gray-400">Technical</p>
      <p class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">{{ $stats['technical'] ?? 0 }}</p>
    </div>
  </div>

  <div
    x-show="showCreateModal"
    x-transition.opacity
    @click="showCreateModal = false"
    @keydown.escape.window="showCreateModal = false"
    class="fixed inset-0 z-[120] flex items-center justify-center bg-gray-900/50 px-4 py-6"
    style="display: none;"
  >
    <div
      @click.stop
      class="w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
    >
      <div class="mb-4 flex items-start justify-between gap-3">
        <div>
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Create Note</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambah catatan cepat tanpa mengganggu layout halaman.</p>
        </div>
        <button type="button" @click="showCreateModal = false" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form method="POST" action="{{ route('notes.store') }}" class="space-y-3">
        @csrf
        <div>
          <input
            type="text"
            name="title"
            value="{{ old('title') }}"
            placeholder="Judul note"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required
          >
        </div>
        <div>
          <textarea
            name="content"
            rows="4"
            placeholder="Tulis isi catatan penting..."
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required
          >{{ old('content') }}</textarea>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <select
            name="type"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          >
            <option value="general" @selected(old('type', 'general') === 'general')>General</option>
            <option value="idea" @selected(old('type') === 'idea')>Idea</option>
            <option value="meeting" @selected(old('type') === 'meeting')>Meeting</option>
            <option value="technical" @selected(old('type') === 'technical')>Technical</option>
          </select>
          <select
            name="color"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          >
            <option value="yellow" @selected(old('color') === 'yellow')>Yellow Card</option>
            <option value="blue" @selected(old('color') === 'blue')>Blue Card</option>
            <option value="green" @selected(old('color') === 'green')>Green Card</option>
            <option value="gray" @selected(old('color') === 'gray')>Gray Card</option>
          </select>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
          <input type="checkbox" name="is_pinned" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30" @checked(old('is_pinned'))>
          Pin note ini
        </label>

        <div class="mt-4 flex items-center justify-end gap-2">
          <button type="button" @click="showCreateModal = false" class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
            Cancel
          </button>
          <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
            Create Note
          </button>
        </div>
      </form>
    </div>
  </div>

  <form method="GET" action="{{ route('notes.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
    <div class="relative flex-1">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
        <path d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      <input
        type="text"
        name="search"
        value="{{ $filter['search'] ?? '' }}"
        placeholder="Cari judul atau isi note..."
        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-4 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
      >
    </div>
    <select
      name="type"
      class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
    >
      <option value="">Semua Type</option>
      <option value="general" @selected(($filter['type'] ?? '') === 'general')>General</option>
      <option value="idea" @selected(($filter['type'] ?? '') === 'idea')>Idea</option>
      <option value="meeting" @selected(($filter['type'] ?? '') === 'meeting')>Meeting</option>
      <option value="technical" @selected(($filter['type'] ?? '') === 'technical')>Technical</option>
    </select>
    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
      Filter
    </button>
    @if(!empty($filter['search']) || !empty($filter['type']))
      <a href="{{ route('notes.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 transition">
        Reset
      </a>
    @endif
  </form>

  @php
    $colorMap = [
      'yellow' => 'bg-yellow-100 border-yellow-200 dark:bg-yellow-500/10 dark:border-yellow-500/20',
      'blue' => 'bg-blue-100 border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20',
      'green' => 'bg-green-100 border-green-200 dark:bg-green-500/10 dark:border-green-500/20',
      'gray' => 'bg-gray-100 border-gray-200 dark:bg-white/[0.03] dark:border-gray-700',
    ];

    $typeIcon = [
      'general' => 'M7.75 4.75C6.64543 4.75 5.75 5.64543 5.75 6.75V17.25C5.75 18.3546 6.64543 19.25 7.75 19.25H16.25C17.3546 19.25 18.25 18.3546 18.25 17.25V9.56066C18.25 9.03022 18.0393 8.52152 17.6642 8.14645L14.8536 5.33579C14.4785 4.96071 13.9698 4.75 13.4393 4.75H7.75Z',
      'idea' => 'M12 4.75V3.25M12 20.75V19.25M17.833 6.167L16.772 7.228M7.228 16.772L6.167 17.833M19.25 12H20.75M3.25 12H4.75M17.833 17.833L16.772 16.772M7.228 7.228L6.167 6.167M12 16.25A4.25 4.25 0 1 0 12 7.75a4.25 4.25 0 0 0 0 8.5Z',
      'meeting' => 'M7.75 4.75C6.64543 4.75 5.75 5.64543 5.75 6.75V17.25C5.75 18.3546 6.64543 19.25 7.75 19.25H16.25C17.3546 19.25 18.25 18.3546 18.25 17.25V9.56066C18.25 9.03022 18.0393 8.52152 17.6642 8.14645L14.8536 5.33579C14.4785 4.96071 13.9698 4.75 13.4393 4.75H7.75Z M8.75 12H15.25 M8.75 15.25H13.25',
      'technical' => 'M9.75 4.75L8.75 7.75L5.75 8.75L8.75 9.75L9.75 12.75L10.75 9.75L13.75 8.75L10.75 7.75L9.75 4.75ZM14.75 11.75L14 14L11.75 14.75L14 15.5L14.75 17.75L15.5 15.5L17.75 14.75L15.5 14L14.75 11.75Z',
    ];
  @endphp

  @if($notes->isEmpty())
    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-20 text-center dark:border-gray-700 dark:bg-white/[0.03]">
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada notes yang cocok.</p>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba buat note pertama dari quick create di atas.</p>
    </div>
  @else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      @foreach($notes as $note)
        @php
          $cardColor = $colorMap[$note->color ?? 'gray'] ?? $colorMap['gray'];
          $type = $note->type ?? 'general';
        @endphp
        <div class="group flex h-full flex-col rounded-xl border p-4 shadow-theme-xs transition hover:-translate-y-0.5 hover:shadow-md {{ $cardColor }}">
          <div class="mb-3 flex items-start justify-between gap-3">
            <div class="flex min-w-0 items-center gap-2">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/60 text-gray-700 dark:bg-white/10 dark:text-gray-200">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="{{ $typeIcon[$type] ?? $typeIcon['general'] }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="truncate text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">{{ $type }}</span>
            </div>

            <form method="POST" action="{{ route('notes.update', $note) }}">
              @csrf
              @method('PUT')
              <input type="hidden" name="is_pinned" value="{{ $note->is_pinned ? 0 : 1 }}">
              <button type="submit" class="inline-flex h-7 items-center rounded-md border border-gray-300/80 bg-white/70 px-2 text-xs font-medium text-gray-700 hover:bg-white dark:border-gray-600 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20">
                {{ $note->is_pinned ? 'Unpin' : 'Pin' }}
              </button>
            </form>
          </div>

          <h3 class="line-clamp-2 text-base font-semibold text-gray-800 dark:text-white/90">{{ $note->title }}</h3>
          <p class="mt-2 line-clamp-5 text-sm leading-6 text-gray-700/90 dark:text-gray-300">{{ $note->content }}</p>

          <div class="mt-4 flex items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $note->created_at->format('d M Y H:i') }}</span>
            @if($note->is_pinned)
              <span class="inline-flex items-center rounded-full bg-yellow-200/80 px-2 py-0.5 text-[11px] font-semibold text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300">Pinned</span>
            @endif
          </div>

          <details class="mt-3 rounded-lg border border-gray-300/70 bg-white/70 p-3 dark:border-gray-700 dark:bg-white/[0.04]">
            <summary class="cursor-pointer text-xs font-medium text-gray-700 dark:text-gray-300">Edit Note</summary>
            <form method="POST" action="{{ route('notes.update', $note) }}" class="mt-3 space-y-2">
              @csrf
              @method('PUT')
              <input type="hidden" name="is_pinned" value="0">
              <input type="text" name="title" value="{{ $note->title }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
              <textarea name="content" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>{{ $note->content }}</textarea>
              <div class="grid grid-cols-2 gap-2">
                <select name="type" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                  <option value="general" @selected(($note->type ?? 'general') === 'general')>General</option>
                  <option value="idea" @selected($note->type === 'idea')>Idea</option>
                  <option value="meeting" @selected($note->type === 'meeting')>Meeting</option>
                  <option value="technical" @selected($note->type === 'technical')>Technical</option>
                </select>
                <select name="color" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                  <option value="yellow" @selected($note->color === 'yellow')>Yellow</option>
                  <option value="blue" @selected($note->color === 'blue')>Blue</option>
                  <option value="green" @selected($note->color === 'green')>Green</option>
                  <option value="gray" @selected(($note->color ?? 'gray') === 'gray')>Gray</option>
                </select>
              </div>
              <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="is_pinned" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30" @checked($note->is_pinned)>
                Keep pinned
              </label>
              <div class="flex gap-2">
                <button type="submit" class="inline-flex h-8 items-center justify-center rounded-lg bg-brand-500 px-3 text-xs font-medium text-white hover:bg-brand-600 transition">Save</button>
            </form>
                <form method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('Hapus note ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex h-8 items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 transition">Delete</button>
                </form>
              </div>
          </details>
        </div>
      @endforeach
    </div>

    @if($notes->hasPages())
      <div class="flex justify-center">
        {{ $notes->links() }}
      </div>
    @endif
  @endif

</div>
@endsection
