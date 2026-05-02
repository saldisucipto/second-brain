@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Asset Management</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola asset dan jadwal perawatan Anda</p>
      </div>
      <a href="{{ route('assets.create') }}"
        class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
        + Tambah Asset
      </a>
    </div>
  </div>

  {{-- Filters --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div class="flex-1">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Cari asset</label>
        <input type="text" name="search" value="{{ $filter['search'] ?? '' }}"
          placeholder="Cari berdasarkan nama..."
          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
      </div>
      <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
        <select name="category"
          class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
          <option value="">Semua Kategori</option>
          @foreach($categories as $cat)
          <option value="{{ $cat }}" {{ ($filter['category'] ?? '') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit"
        class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-300 dark:hover:bg-white/20 transition">
        Filter
      </button>
      @if($filter)
      <a href="{{ route('assets.index') }}"
        class="h-10 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
        Reset
      </a>
      @endif
    </form>
  </div>

  {{-- Assets Grid --}}
  @if($assets->isEmpty())
  <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/[0.03]">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mx-auto mb-3 text-gray-300 dark:text-gray-700">
      <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" stroke-width="1.5"/>
      <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="1.5"/>
    </svg>
    <p class="text-gray-500 dark:text-gray-400">Belum ada asset. Mulai dengan menambah asset baru.</p>
  </div>
  @else
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($assets as $asset)
    <a href="{{ route('assets.show', $asset) }}"
      class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:border-brand-200 hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-600 dark:hover:bg-white/[0.05]">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-gray-800 group-hover:text-brand-600 dark:text-white/90 dark:group-hover:text-brand-400 transition">{{ $asset->name }}</h3>
          <p class="mt-1 text-xs font-medium text-gray-400 uppercase">{{ ucfirst($asset->category) }}</p>
        </div>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="shrink-0 text-gray-300 group-hover:text-brand-600 dark:text-gray-700 dark:group-hover:text-brand-400 transition">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </div>

      {{-- Stats --}}
      <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 dark:border-gray-800">
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500 dark:text-gray-400">Jadwal Aktif:</span>
          <span class="font-medium text-gray-800 dark:text-white/90">{{ $asset->schedules->count() }}</span>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500 dark:text-gray-400">Maintenance:</span>
          <span class="font-medium text-gray-800 dark:text-white/90">{{ $asset->histories->count() }}</span>
        </div>
      </div>

      {{-- Next Maintenance --}}
      @php
        $nextMaintenance = $asset->schedules->sortBy('next_due_at')->first();
      @endphp
      @if($nextMaintenance)
      <div class="mt-4 rounded-lg {{ $nextMaintenance->isOverdue() ? 'bg-red-50 dark:bg-red-500/10' : 'bg-blue-50 dark:bg-blue-500/10' }} px-3 py-2">
        <p class="text-xs font-medium {{ $nextMaintenance->isOverdue() ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}">
          {{ $nextMaintenance->isOverdue() ? '🔴 Overdue' : '📅 Next' }}: {{ $nextMaintenance->next_due_at->format('d M Y') }}
        </p>
      </div>
      @endif
    </a>
    @endforeach
  </div>

  {{-- Pagination --}}
  <div class="mt-8">
    {{ $assets->links() }}
  </div>
  @endif

</div>
@endsection
