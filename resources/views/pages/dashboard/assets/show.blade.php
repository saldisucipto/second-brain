@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="flex items-start gap-3">
        <a href="{{ route('assets.index') }}" class="mt-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition shrink-0">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M5 12l7-7M5 12l7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <div>
          <p class="text-sm font-medium text-brand-500 dark:text-brand-400">{{ ucfirst($asset->category) }}</p>
          <h1 class="mt-0.5 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $asset->name }}</h1>
          @if($asset->note)
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $asset->note }}</p>
          @endif
        </div>
      </div>
      <div class="flex shrink-0 gap-2">
        <a href="{{ route('assets.edit', $asset) }}"
          class="inline-flex h-9 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
          Edit
        </a>
        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Hapus asset ini?');">
          @csrf @method('DELETE')
          <button type="submit"
            class="inline-flex h-9 items-center justify-center rounded-lg border border-red-200 px-4 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10 transition">
            Hapus
          </button>
        </form>
      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400">
    {{ session('success') }}
  </div>
  @endif

  {{-- Stats Bar --}}
  <div class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal Aktif</p>
      <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $asset->schedules->count() }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Maintenance History</p>
      <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">{{ $asset->histories->count() }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</p>
      <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white/90">Rp {{ number_format($asset->total_maintenance_cost, 0, ',', '.') }}</p>
    </div>
  </div>

  {{-- Active Schedules --}}
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800 px-6 py-4">
      <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Jadwal Perawatan ({{ $asset->schedules->count() }})</h2>
      <a href="{{ route('assets.schedule.create', $asset) }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50 dark:border-brand-600 dark:text-brand-400 dark:hover:bg-brand-500/10 transition">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Buat Jadwal
      </a>
    </div>

    @if($asset->schedules->isEmpty())
    <div class="py-12 text-center text-sm text-gray-400">Belum ada jadwal perawatan.</div>
    @else
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
      @foreach($asset->schedules as $schedule)
      @php
        $isOverdue = $schedule->isOverdue();
        $shouldRemind = $schedule->shouldRemind();
      @endphp
      <div class="flex items-start justify-between gap-4 px-6 py-4">
        <div class="min-w-0 flex-1">
          <p class="font-medium text-gray-800 dark:text-white/90">{{ $schedule->title }}</p>
          <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
            <span>🔄 {{ ucfirst($schedule->repeat_type) }} ({{ $schedule->repeat_interval }}x)</span>
            <span>📅 {{ $schedule->next_due_at->format('d M Y') }}</span>
            @if($schedule->last_done_at)
            <span>✅ Last: {{ $schedule->last_done_at->format('d M Y') }}</span>
            @endif
          </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          @if($isOverdue)
          <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-400">
            🔴 Overdue
          </span>
          @elseif($shouldRemind)
          <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400">
            ⏰ Reminder
          </span>
          @else
          <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400">
            ✓ On Schedule
          </span>
          @endif

          <div class="relative group">
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="1" fill="currentColor"/><circle cx="19" cy="12" r="1" fill="currentColor"/><circle cx="5" cy="12" r="1" fill="currentColor"/></svg>
            </button>
            <div class="absolute right-0 top-full mt-1 hidden rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 group-hover:block z-10">
              <button type="button" onclick="openCompleteModal({{ $schedule->id }})" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 rounded-t-lg">
                Tandai Selesai
              </button>
              <a href="{{ route('assets.schedule.edit', $schedule) }}" class="block px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">
                Edit
              </a>
              <form action="{{ route('assets.schedule.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal ini?');">
                @csrf @method('DELETE')
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-b-lg">
                  Hapus
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Maintenance History --}}
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-800 px-6 py-4">
      <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Riwayat Perawatan ({{ $asset->histories->count() }})</h2>
      <button type="button" onclick="openHistoryModal()"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Catat Manual
      </button>
    </div>

    @if($asset->histories->isEmpty())
    <div class="py-12 text-center text-sm text-gray-400">Belum ada riwayat perawatan.</div>
    @else
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
      @foreach($asset->histories as $history)
      <div class="flex items-start justify-between gap-4 px-6 py-4">
        <div class="min-w-0 flex-1">
          <p class="font-medium text-gray-800 dark:text-white/90">{{ $history->title }}</p>
          @if($history->description)
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $history->description }}</p>
          @endif
          <div class="mt-2 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
            <span>📅 {{ $history->done_at->format('d M Y H:i') }}</span>
            @if($history->schedule)
            <span>🔄 Jadwal: {{ $history->schedule->title }}</span>
            @endif
            @if($history->cost)
            <span>💰 Rp {{ number_format($history->cost, 0, ',', '.') }}</span>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

</div>

{{-- Modal: Complete Schedule --}}
<div id="complete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
  <div class="flex min-h-screen items-center justify-center bg-black/50 px-4 py-6">
    <div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tandai Perawatan Selesai</h2>
        <button type="button" onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form id="complete-form" method="POST" class="space-y-4 p-6">
        @csrf @method('POST')

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai <span class="text-error-500">*</span></label>
          <input type="date" name="done_at" required
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
          <textarea name="description" rows="2"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            placeholder="Catatan tentang perawatan..."></textarea>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya (Rp)</label>
          <input type="number" name="cost" step="0.01" placeholder="0"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
          <button type="button" onclick="closeCompleteModal()"
            class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
            Batal
          </button>
          <button type="submit"
            class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal: Manual History --}}
<div id="history-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
  <div class="flex min-h-screen items-center justify-center bg-black/50 px-4 py-6">
    <div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Catat Manual Perawatan</h2>
        <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>

      <form action="{{ route('assets.history.store', $asset) }}" method="POST" class="space-y-4 p-6">
        @csrf

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul <span class="text-error-500">*</span></label>
          <input type="text" name="title" required placeholder="Contoh: Ganti Ban"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
          <textarea name="description" rows="2"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            placeholder="Detail perawatan..."></textarea>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal <span class="text-error-500">*</span></label>
          <input type="date" name="done_at" required
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya (Rp)</label>
          <input type="number" name="cost" step="0.01" placeholder="0"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
          <button type="button" onclick="closeHistoryModal()"
            class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5 transition">
            Batal
          </button>
          <button type="submit"
            class="inline-flex h-10 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentScheduleId = null;

function openCompleteModal(scheduleId) {
  currentScheduleId = scheduleId;
  document.getElementById('complete-form').action = `/assets/schedule/${scheduleId}/complete`;
  document.getElementById('complete-modal').classList.remove('hidden');
}

function closeCompleteModal() {
  document.getElementById('complete-modal').classList.add('hidden');
  currentScheduleId = null;
}

function openHistoryModal() {
  document.getElementById('history-modal').classList.remove('hidden');
}

function closeHistoryModal() {
  document.getElementById('history-modal').classList.add('hidden');
}

// Close on outside click
document.getElementById('complete-modal')?.addEventListener('click', (e) => {
  if (e.target === document.getElementById('complete-modal')) closeCompleteModal();
});

document.getElementById('history-modal')?.addEventListener('click', (e) => {
  if (e.target === document.getElementById('history-modal')) closeHistoryModal();
});
</script>
@endsection
