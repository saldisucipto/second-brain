@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-sm font-medium text-brand-500 dark:text-brand-400">Second Brain</p>
        <h1 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">Minutes of Meeting</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Catat hasil rapat, tetapkan action item, dan pantau progres PIC secara terstruktur.
        </p>
      </div>
      <a href="{{ route('moms.create') }}"
         class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Buat MOM Baru
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
    {{ session('success') }}
  </div>
  @endif

  {{-- Stat Cards --}}
  <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
    @php
      $statCards = [
        ['label' => 'Total MOM',  'value' => $stats['total'],   'color' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',    'status' => ''],
        ['label' => 'Draft',      'value' => $stats['draft'],   'color' => 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300',       'status' => 'draft'],
        ['label' => 'Ongoing',    'value' => $stats['ongoing'], 'color' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-yellow-400', 'status' => 'ongoing'],
        ['label' => 'Closed',     'value' => $stats['closed'],  'color' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400',  'status' => 'closed'],
      ];
    @endphp
    @foreach($statCards as $card)
    <a href="{{ route('moms.index', array_merge($filter, $card['status'] ? ['status' => $card['status']] : [])) }}"
       class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] {{ ($filter['status'] ?? '') === $card['status'] ? 'ring-2 ring-brand-500' : '' }}">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
          <p class="mt-2 text-3xl font-semibold text-gray-800 dark:text-white/90">{{ $card['value'] }}</p>
        </div>
        <div class="{{ $card['color'] }} flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M7.75 4.75C6.64543 4.75 5.75 5.64543 5.75 6.75V17.25C5.75 18.3546 6.64543 19.25 7.75 19.25H16.25C17.3546 19.25 18.25 18.3546 18.25 17.25V9.56066C18.25 9.03022 18.0393 8.52152 17.6642 8.14645L14.8536 5.33579C14.4785 4.96071 13.9698 4.75 13.4393 4.75H7.75Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8.75 12H15.25M8.75 15.25H13.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
    </a>
    @endforeach
  </div>

  {{-- Filter & Search --}}
  <form method="GET" action="{{ route('moms.index') }}"
        class="flex flex-col gap-3 sm:flex-row sm:items-center">
    <div class="relative flex-1">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
        <path d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      <input type="text" name="search" value="{{ $filter['search'] ?? '' }}"
        placeholder="Cari judul MOM..."
        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-9 pr-4 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
    </div>
    <select name="status"
      class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
      <option value="">Semua Status</option>
      <option value="draft"   @selected(($filter['status'] ?? '') === 'draft')>Draft</option>
      <option value="ongoing" @selected(($filter['status'] ?? '') === 'ongoing')>Ongoing</option>
      <option value="closed"  @selected(($filter['status'] ?? '') === 'closed')>Closed</option>
    </select>
    <button type="submit"
      class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
      Filter
    </button>
    @if(!empty($filter['status']) || !empty($filter['search']))
    <a href="{{ route('moms.index') }}"
       class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/5 transition">
      Reset
    </a>
    @endif
  </form>

  {{-- MOM Cards Grid --}}
  @if($moms->isEmpty())
  <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-20 text-center dark:border-gray-700 dark:bg-white/[0.03]">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mx-auto mb-4 text-gray-300 dark:text-gray-600">
      <path d="M7.75 4.75C6.64543 4.75 5.75 5.64543 5.75 6.75V17.25C5.75 18.3546 6.64543 19.25 7.75 19.25H16.25C17.3546 19.25 18.25 18.3546 18.25 17.25V9.56066C18.25 9.03022 18.0393 8.52152 17.6642 8.14645L14.8536 5.33579C14.4785 4.96071 13.9698 4.75 13.4393 4.75H7.75Z" stroke="currentColor" stroke-width="1.5"/>
      <path d="M8.75 12H15.25M8.75 15.25H13.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ (!empty($filter['status']) || !empty($filter['search'])) ? 'Tidak ada MOM yang cocok dengan filter.' : 'Belum ada MOM.' }}
    </p>
    @if(empty($filter['status']) && empty($filter['search']))
    <a href="{{ route('moms.create') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Buat MOM pertama
    </a>
    @endif
  </div>
  @else
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($moms as $mom)
    @php
      $statusMap = [
        'draft'   => ['label' => 'Draft',   'bg' => 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300'],
        'ongoing' => ['label' => 'Ongoing', 'bg' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400'],
        'closed'  => ['label' => 'Closed',  'bg' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400'],
      ];
      $s = $statusMap[$mom->status] ?? $statusMap['draft'];

      $itemPending  = $mom->items->where('status', 'pending')->count();
      $itemProgress = $mom->items->where('status', 'progress')->count();
      $itemDone     = $mom->items->where('status', 'done')->count();
      $itemTotal    = $mom->items->count();
      $pct = $itemTotal > 0 ? round($itemDone / $itemTotal * 100) : 0;
    @endphp
    <div class="flex flex-col rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-md transition">

      {{-- Card Header --}}
      <div class="flex items-start justify-between gap-3 p-5 pb-3">
        <div class="min-w-0 flex-1">
          <a href="{{ route('moms.show', $mom) }}"
             class="block truncate text-base font-semibold text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400 transition">
            {{ $mom->title }}
          </a>
          <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              {{ $mom->meeting_date->format('d M Y') }}
            </span>
            @if($mom->location)
            <span class="flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 21C12 21 5 14.5 5 9a7 7 0 0 1 14 0c0 5.5-7 12-7 12Z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="9" r="2" stroke="currentColor" stroke-width="1.5"/></svg>
              {{ $mom->location }}
            </span>
            @endif
          </div>
        </div>
        <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $s['bg'] }}">
          {{ $s['label'] }}
        </span>
      </div>

      {{-- Task Group --}}
      @if($mom->taskGroup)
      <div class="px-5 pb-3">
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none"><path d="M4.75 6.75C4.75 5.64543 5.64543 4.75 6.75 4.75H9.25C10.3546 4.75 11.25 5.64543 11.25 6.75V9.25C11.25 10.3546 10.3546 11.25 9.25 11.25H6.75C5.64543 11.25 4.75 10.3546 4.75 9.25V6.75Z" stroke="currentColor" stroke-width="1.5"/><path d="M12.75 6.75C12.75 5.64543 13.6454 4.75 14.75 4.75H17.25C18.3546 4.75 19.25 5.64543 19.25 6.75V9.25C19.25 10.3546 18.3546 11.25 17.25 11.25H14.75C13.6454 11.25 12.75 10.3546 12.75 9.25V6.75Z" stroke="currentColor" stroke-width="1.5"/></svg>
          {{ $mom->taskGroup->name }}
        </span>
      </div>
      @endif

      {{-- Action Item Progress --}}
      <div class="mx-5 mb-4 rounded-xl border border-gray-100 bg-gray-50/60 p-3 dark:border-gray-700 dark:bg-white/[0.02]">
        <div class="mb-2 flex items-center justify-between">
          <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Action Items</p>
          <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $pct }}%</p>
        </div>
        <div class="mb-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
          <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex items-center gap-3 text-xs">
          <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
            <span class="h-2 w-2 rounded-full bg-yellow-400"></span>
            {{ $itemPending }} pending
          </span>
          <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            {{ $itemProgress }} progress
          </span>
          <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
            <span class="h-2 w-2 rounded-full bg-green-400"></span>
            {{ $itemDone }} done
          </span>
        </div>
      </div>

      {{-- Footer --}}
      <div class="mt-auto flex items-center justify-between gap-2 border-t border-gray-100 px-5 py-3 dark:border-gray-800">
        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $mom->meeting_date->format('H:i') }} WIB</p>
        <div class="flex items-center gap-2">
          <a href="{{ route('moms.show', $mom) }}"
             class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 px-3 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
            Detail
          </a>
          <a href="{{ route('moms.edit', $mom) }}"
             class="inline-flex h-8 items-center justify-center rounded-lg bg-brand-50 px-3 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition">
            Edit
          </a>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  @if($moms->hasPages())
  <div class="flex justify-center">
    {{ $moms->links() }}
  </div>
  @endif
  @endif

</div>
@endsection
