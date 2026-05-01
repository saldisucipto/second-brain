@extends('layouts.app')

@section('content')
  @php
    $statusCards = [
      [
        'label' => 'Total Tasks',
        'value' => $taskStats['total'] ?? 0,
        'hint' => 'Semua task di Second Brain',
        'color' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
      ],
      [
        'label' => 'To Do',
        'value' => $taskStats['todo'] ?? 0,
        'hint' => 'Belum mulai',
        'color' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80',
      ],
      [
        'label' => 'In Progress',
        'value' => $taskStats['progress'] ?? 0,
        'hint' => 'Sedang dikerjakan',
        'color' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400',
      ],
      [
        'label' => 'Done',
        'value' => $taskStats['done'] ?? 0,
        'hint' => 'Sudah selesai',
        'color' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500',
      ],
      [
        'label' => 'Need Attention',
        'value' => $attentionTasks ?? 0,
        'hint' => 'Task dengan warning insight',
        'color' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400',
      ],
    ];

    $statusColors = [
      'todo' => 'light',
      'progress' => 'warning',
      'done' => 'success',
      'cancel' => 'error',
    ];

    $priorityColors = [
      'low' => 'info',
      'medium' => 'warning',
      'high' => 'error',
    ];

    $groupBadgeColors = [
      'red' => 'error',
      'green' => 'success',
      'blue' => 'primary',
      'yellow' => 'warning',
    ];
  @endphp

  <div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-sm font-medium text-brand-500 dark:text-brand-400">Second Brain</p>
          <h1 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
            Task Management Dashboard
          </h1>
          <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Pantau pekerjaan aktif, prioritas penting, dan follow-up yang perlu kamu tangani berikutnya.
          </p>
        </div>

        <a
          href="{{ route('tasks.index') }}"
          class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition"
        >
          Kelola Tasks
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
      @foreach ($statusCards as $card)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
              <h3 class="mt-2 text-3xl font-semibold text-gray-800 dark:text-white/90">
                {{ $card['value'] }}
              </h3>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
            </div>
            <div class="{{ $card['color'] }} flex h-11 w-11 items-center justify-center rounded-xl">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M8 12.25L10.5 14.75L16 9.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.75 5.75C4.75 5.19772 5.19772 4.75 5.75 4.75H18.25C18.8023 4.75 19.25 5.19772 19.25 5.75V18.25C19.25 18.8023 18.8023 19.25 18.25 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V5.75Z" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="mb-4">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Daily Brief</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan otomatis untuk memulai hari kerja.</p>
      </div>

      <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Task Hari Ini</p>
          <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $brief['total_tasks_today'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
          <p class="text-xs text-gray-500 dark:text-gray-400">Overdue Task</p>
          <p class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ $brief['overdue_tasks'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
          <p class="text-xs text-gray-500 dark:text-gray-400">Follow Up Hari Ini</p>
          <p class="mt-1 text-2xl font-semibold text-brand-500 dark:text-brand-400">{{ $brief['follow_ups_today'] ?? 0 }}</p>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        @forelse ($brief['tasks_per_category'] ?? [] as $item)
          <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ $item['category'] ?? 'Tanpa Kategori' }}: {{ $item['total'] ?? 0 }}
          </span>
        @empty
          <span class="text-xs text-gray-500 dark:text-gray-400">Belum ada data kategori.</span>
        @endforelse
      </div>
    </div>

    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <x-common.component-card title="Quick Capture" desc="Input cepat task + kategori tanpa keluar dari dashboard.">
          <form method="POST" action="{{ route('tasks.quick-capture') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_220px_auto]">
            @csrf
            <input
              type="text"
              name="title"
              placeholder="Tulis task baru"
              class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              required
            >
            <select
              name="task_group_id"
              class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            >
              <option value="">Pilih kategori</option>
              @foreach ($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
              @endforeach
            </select>
            <button
              type="submit"
              class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition"
            >
              Tambah
            </button>
          </form>
        </x-common.component-card>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="flex items-center justify-between gap-3 px-6 py-5">
            <div>
              <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Recent Tasks</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Task terbaru yang masuk ke workflow kamu.</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">
              View all
            </a>
          </div>

          <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="min-w-full">
              <thead>
                <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                  <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Task</p></th>
                  <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                  <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Kategori</p></th>
                  <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Priority</p></th>
                  <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Follow Up</p></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                @forelse ($recentTasks as $task)
                  <tr>
                    <td class="px-6 py-3.5">
                      <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $task->title }}</p>
                      <p class="mt-1 text-gray-500 text-theme-xs dark:text-gray-400">{{ $task->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-3.5">
                      <x-ui.badge color="{{ $statusColors[$task->status] ?? 'light' }}">{{ $task->status }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-3.5">
                      @if ($task->group)
                        <x-ui.badge color="{{ $groupBadgeColors[$task->group->color] ?? 'light' }}">{{ $task->group->name }}</x-ui.badge>
                      @else
                        <span class="text-gray-500 text-theme-sm dark:text-gray-400">-</span>
                      @endif
                    </td>
                    <td class="px-6 py-3.5">
                      <x-ui.badge color="{{ $priorityColors[$task->priority] ?? 'light' }}">{{ $task->priority }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-3.5">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $task->follow_ups_count }} item</p>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-6 py-10 text-center">
                      <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada task.</p>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan task pertama dari Quick Capture.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-span-12 space-y-6 xl:col-span-5">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-5 flex items-start justify-between gap-4">
            <div>
              <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Focus Snapshot</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hal yang paling perlu dilihat hari ini.</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
              <p class="text-sm text-gray-500 dark:text-gray-400">High Priority</p>
              <p class="mt-2 text-2xl font-semibold text-error-600 dark:text-error-500">{{ $taskStats['highPriority'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
              <p class="text-sm text-gray-500 dark:text-gray-400">Follow Up Today</p>
              <p class="mt-2 text-2xl font-semibold text-brand-500 dark:text-brand-400">{{ $followUpStats['today'] ?? 0 }}</p>
            </div>
            <div class="col-span-2 rounded-xl border border-gray-100 p-4 dark:border-gray-800">
              <div class="flex items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Follow Up</p>
                <x-ui.badge color="warning">{{ $followUpStats['pending'] ?? 0 }}</x-ui.badge>
              </div>
            </div>
            <div class="col-span-2 rounded-xl border border-gray-100 p-4 dark:border-gray-800">
              <div class="flex items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Missed Reminder</p>
                <x-ui.badge color="error">{{ $followUpStats['missed'] ?? 0 }}</x-ui.badge>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-5">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Insight Otomatis</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deteksi task bermasalah dari aktivitas follow up terbaru.</p>
          </div>

          <div class="space-y-3">
            @forelse ($recentTasks as $task)
              @php
                $taskInsights = $insightsByTask[$task->id] ?? [];
                $topInsights = collect($taskInsights)->take(2);
              @endphp

              <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $task->title }}</p>

                <div class="mt-2 space-y-2">
                  @forelse ($topInsights as $insight)
                    <div class="rounded-lg px-3 py-2 text-xs font-medium {{ ($insight['type'] ?? 'info') === 'warning'
                            ? 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300'
                            : 'bg-blue-100 text-blue-800 dark:bg-blue-500/15 dark:text-blue-300' }}">
                      {{ $insight['message'] ?? '-' }}
                    </div>
                  @empty
                    <div class="rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-800 dark:bg-blue-500/15 dark:text-blue-300">
                      Belum ada insight untuk task ini.
                    </div>
                  @endforelse
                </div>
              </div>
            @empty
              <div class="rounded-xl border border-dashed border-gray-200 px-4 py-8 text-center dark:border-gray-800">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada task untuk dianalisis.</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Insight otomatis akan muncul setelah task dan follow up tersedia.</p>
              </div>
            @endforelse
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-5">
            <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Upcoming Follow Up</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Reminder terdekat dari task aktif.</p>
          </div>

          <div class="space-y-3">
            @forelse ($upcomingFollowUps as $followUp)
              <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $followUp->title }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $followUp->task?->title ?? 'Tanpa task' }}</p>
                  </div>
                  <x-ui.badge color="warning">{{ $followUp->status }}</x-ui.badge>
                </div>
                <p class="mt-3 text-sm font-medium text-brand-500 dark:text-brand-400">
                  {{ $followUp->reminder_at->format('d M Y, H:i') }}
                </p>
              </div>
            @empty
              <div class="rounded-xl border border-dashed border-gray-200 px-4 py-8 text-center dark:border-gray-800">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tidak ada follow-up pending.</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Saat ada reminder, daftar terdekat muncul di sini.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
