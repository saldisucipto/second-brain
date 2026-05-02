@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-4">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12l7-7M5 12l7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Kembali
    </a>
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Asset: <strong>{{ $asset->name }}</strong></p>
  </div>

  {{-- Form --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <form action="{{ route('assets.schedule.store', $asset) }}" method="POST" class="space-y-6">
      @csrf

      <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Perawatan <span class="text-error-500">*</span></label>
        <input type="text" name="title" required placeholder="Contoh: Ganti Oli"
          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
          @error('title') border-error-500 dark:border-error-500 @enderror"
          value="{{ old('title') }}">
        @error('title')
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Pengulangan <span class="text-error-500">*</span></label>
          <select name="repeat_type" required
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
            @error('repeat_type') border-error-500 dark:border-error-500 @enderror">
            <option value="">-- Pilih Tipe --</option>
            <option value="daily" {{ old('repeat_type') === 'daily' ? 'selected' : '' }}>Harian</option>
            <option value="weekly" {{ old('repeat_type') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
            <option value="monthly" {{ old('repeat_type') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ old('repeat_type') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
          </select>
          @error('repeat_type')
          <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Interval <span class="text-error-500">*</span></label>
          <input type="number" name="repeat_interval" required min="1" placeholder="Contoh: 1"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
            @error('repeat_interval') border-error-500 dark:border-error-500 @enderror"
            value="{{ old('repeat_interval', 1) }}">
          @error('repeat_interval')
          <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Jadwal Pertama <span class="text-error-500">*</span></label>
          <input type="date" name="first_due_at" required
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
            @error('first_due_at') border-error-500 dark:border-error-500 @enderror"
            value="{{ old('first_due_at') }}">
          @error('first_due_at')
          <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Ingatkan Sebelum (hari)</label>
          <input type="number" name="reminder_before_days" min="0" placeholder="1"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
            @error('reminder_before_days') border-error-500 dark:border-error-500 @enderror"
            value="{{ old('reminder_before_days', 1) }}">
          @error('reminder_before_days')
          <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <a href="{{ route('assets.show', $asset) }}"
          class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-6 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
          Batal
        </a>
        <button type="submit"
          class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-6 text-sm font-medium text-white hover:bg-brand-600 transition">
          Buat Jadwal
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
