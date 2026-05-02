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
  </div>

  {{-- Form --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <form action="{{ route('assets.update', $asset) }}" method="POST" class="space-y-6">
      @csrf @method('PATCH')

      <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Asset <span class="text-error-500">*</span></label>
        <input type="text" name="name" required placeholder="Contoh: Motor Beat 2023"
          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
          @error('name') border-error-500 dark:border-error-500 @enderror"
          value="{{ old('name', $asset->name) }}">
        @error('name')
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-error-500">*</span></label>
        <select name="category" required
          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
          @error('category') border-error-500 dark:border-error-500 @enderror">
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $key => $label)
          <option value="{{ $key }}" {{ old('category', $asset->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('category')
        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
        <textarea name="note" rows="3" placeholder="Informasi tambahan tentang asset..."
          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('note', $asset->note) }}</textarea>
      </div>

      <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <a href="{{ route('assets.show', $asset) }}"
          class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-6 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
          Batal
        </a>
        <button type="submit"
          class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-6 text-sm font-medium text-white hover:bg-brand-600 transition">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
