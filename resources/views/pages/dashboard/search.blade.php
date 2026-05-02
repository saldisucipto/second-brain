@extends('layouts.app')

@section('content')
  <div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-sm font-medium text-brand-500 dark:text-brand-400">Search</p>
          <h1 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
            Hasil untuk &ldquo;{{ $q }}&rdquo;
          </h1>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $tasks->count() + $moms->count() + $assets->count() }} hasil ditemukan di Tasks, MOMs, dan Assets.
          </p>
        </div>
        <form method="GET" action="{{ route('search') }}" class="flex gap-2">
          <input
            type="text"
            name="q"
            value="{{ $q }}"
            placeholder="Cari lagi..."
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 w-64"
            autofocus
          >
          <button
            type="submit"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition"
          >
            Cari
          </button>
        </form>
      </div>
    </div>

    @if ($tasks->isEmpty() && $moms->isEmpty() && $assets->isEmpty())
      <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <svg class="mx-auto mb-4 text-gray-300 dark:text-gray-600" width="48" height="48" viewBox="0 0 24 24" fill="none">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z" fill="currentColor"/>
        </svg>
        <p class="text-base font-medium text-gray-700 dark:text-gray-300">Tidak ada hasil ditemukan.</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba gunakan kata kunci yang berbeda.</p>
      </div>
    @else
      <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Tasks --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M8 12.25L10.5 14.75L16 9.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M4.75 5.75C4.75 5.19772 5.19772 4.75 5.75 4.75H18.25C18.8023 4.75 19.25 5.19772 19.25 5.75V18.25C19.25 18.8023 18.8023 19.25 18.25 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V5.75Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Tasks</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tasks->count() }} hasil</p>
              </div>
            </div>
            <a href="{{ route('tasks.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat semua</a>
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($tasks as $task)
              <a href="{{ route('tasks.show', $task) }}" class="block px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                  {!! preg_replace('/(' . preg_quote($q, '/') . ')/i', '<mark class="bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300 rounded px-0.5">$1</mark>', e($task->title)) !!}
                </p>
                <div class="mt-1.5 flex items-center gap-2">
                  @php
                    $statusColor = ['todo' => 'bg-gray-100 text-gray-600', 'progress' => 'bg-yellow-100 text-yellow-700', 'done' => 'bg-green-100 text-green-700', 'cancel' => 'bg-red-100 text-red-700'][$task->status] ?? 'bg-gray-100 text-gray-600';
                    $priorityColor = ['low' => 'bg-blue-100 text-blue-700', 'medium' => 'bg-orange-100 text-orange-700', 'high' => 'bg-red-100 text-red-700'][$task->priority] ?? 'bg-gray-100 text-gray-600';
                  @endphp
                  <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColor }}">{{ $task->status }}</span>
                  <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $priorityColor }}">{{ $task->priority }}</span>
                  @if ($task->due_date)
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task->due_date->format('d M Y') }}</span>
                  @endif
                </div>
              </a>
            @empty
              <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada task yang cocok.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- MOMs --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/15 dark:text-teal-400">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Minutes of Meeting</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $moms->count() }} hasil</p>
              </div>
            </div>
            <a href="{{ route('moms.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat semua</a>
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($moms as $mom)
              <a href="{{ route('moms.show', $mom) }}" class="block px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                  {!! preg_replace('/(' . preg_quote($q, '/') . ')/i', '<mark class="bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300 rounded px-0.5">$1</mark>', e($mom->title)) !!}
                </p>
                <div class="mt-1.5 flex items-center gap-2">
                  @php
                    $momStatusColor = ['draft' => 'bg-gray-100 text-gray-600', 'ongoing' => 'bg-yellow-100 text-yellow-700', 'closed' => 'bg-green-100 text-green-700'][$mom->status] ?? 'bg-gray-100 text-gray-600';
                  @endphp
                  <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $momStatusColor }}">{{ $mom->status }}</span>
                  <span class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($mom->meeting_date)->format('d M Y') }}</span>
                  @if ($mom->location)
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $mom->location }}</span>
                  @endif
                </div>
              </a>
            @empty
              <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada MOM yang cocok.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- Assets --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-400">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">Assets</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assets->count() }} hasil</p>
              </div>
            </div>
            <a href="{{ route('assets.index') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">Lihat semua</a>
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($assets as $asset)
              <a href="{{ route('assets.show', $asset) }}" class="block px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                  {!! preg_replace('/(' . preg_quote($q, '/') . ')/i', '<mark class="bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300 rounded px-0.5">$1</mark>', e($asset->name)) !!}
                </p>
                <div class="mt-1.5 flex items-center gap-2">
                  @if ($asset->category)
                    <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">{{ $asset->category }}</span>
                  @endif
                  @if ($asset->note)
                    <span class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ Str::limit($asset->note, 40) }}</span>
                  @endif
                </div>
              </a>
            @empty
              <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada aset yang cocok.</p>
              </div>
            @endforelse
          </div>
        </div>

      </div>
    @endif

  </div>
@endsection
