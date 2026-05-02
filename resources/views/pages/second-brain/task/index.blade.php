@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Second Brain Tasks" />

    @php
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

        $followUpStatusColors = [
            'pending' => 'warning',
            'done' => 'success',
            'missed' => 'error',
        ];

        $followUpStatusBackgrounds = [
            'pending' => 'border-yellow-200 bg-yellow-100 dark:border-yellow-500/30 dark:bg-yellow-500/10',
            'done' => 'border-green-200 bg-green-100 dark:border-green-500/30 dark:bg-green-500/10',
            'missed' => 'border-red-200 bg-red-100 dark:border-red-500/30 dark:bg-red-500/10',
        ];

        $firstTaskId = $tasks->first()?->id;
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-success-500 bg-success-50 px-5 py-4 dark:border-success-500/30 dark:bg-success-500/15">
                <p class="text-sm font-medium text-success-700 dark:text-success-400">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-error-500 bg-error-50 px-5 py-4 dark:border-error-500/30 dark:bg-error-500/15">
                <p class="text-sm font-medium text-error-700 dark:text-error-400">{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Quick Capture</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan task baru dan langsung kategorikan.</p>
                </div>
                <x-ui.badge color="primary">{{ $tasks->total() }} task</x-ui.badge>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_260px_auto]">
                @csrf
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Judul task"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    required
                >

                <select
                    name="task_group_id"
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                    <option value="">Pilih kategori</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('task_group_id') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition"
                >
                    Tambah
                </button>
            </form>
        </div>

        <div
            x-data="{ selectedTask: @js($firstTaskId) }"
            class="grid min-h-[calc(100vh-260px)] gap-6 xl:grid-cols-[420px_minmax(0,1fr)]"
        >
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Daftar Task</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih task untuk melihat detail dan menambahkan follow-up.</p>
                </div>

                <div class="max-h-[calc(100vh-360px)] overflow-y-auto p-4 custom-scrollbar">
                    <div class="space-y-3">
                        @forelse ($tasks as $task)
                            @php
                                $nextFollowUp = $task->followUps
                                    ->where('status', 'pending')
                                    ->sortBy('reminder_at')
                                    ->first();
                            @endphp

                            <button
                                type="button"
                                @click="selectedTask = {{ $task->id }}; $nextTick(() => window.dispatchEvent(new CustomEvent('task-timeline-changed')))"
                                class="block w-full rounded-xl border p-4 text-left transition hover:border-brand-300 hover:bg-brand-50/50 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/10"
                                :class="selectedTask === {{ $task->id }}
                                    ? 'border-brand-300 bg-brand-50 dark:border-brand-500/40 dark:bg-brand-500/10'
                                    : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-transparent'"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h4 class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">
                                            {{ $task->title }}
                                        </h4>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->created_at->format('d M Y') }}
                                        </p>
                                    </div>

                                    <x-ui.badge color="{{ $statusColors[$task->status] ?? 'light' }}">
                                        {{ $task->status }}
                                    </x-ui.badge>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    @if ($task->group)
                                        <x-ui.badge color="{{ $groupBadgeColors[$task->group->color] ?? 'light' }}">
                                            {{ $task->group->name }}
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge color="light">Tanpa kategori</x-ui.badge>
                                    @endif

                                    <x-ui.badge color="{{ $priorityColors[$task->priority] ?? 'light' }}">
                                        {{ $task->priority }}
                                    </x-ui.badge>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <p>Follow up: {{ $task->followUps->count() }}</p>
                                    <p>Due: {{ $task->due_date?->format('d M') ?? '-' }}</p>
                                    <p class="col-span-2">Next: {{ $nextFollowUp?->reminder_at?->format('d M Y, H:i') ?? '-' }}</p>
                                </div>
                            </button>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-200 px-4 py-10 text-center dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada task.</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan task pertama dari Quick Capture.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($tasks->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>

            <div class="min-w-0">
                @forelse ($tasks as $task)
                    @php
                        $followUpsByDate = $task->followUps->groupBy(fn ($item) => $item->created_at->toDateString());
                        $pendingFollowUps = $task->followUps->where('status', 'pending')->count();
                    @endphp

                    <section
                        x-show="selectedTask === {{ $task->id }}"
                        x-cloak
                        class="space-y-6"
                    >
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                        <x-ui.badge color="{{ $statusColors[$task->status] ?? 'light' }}">{{ $task->status }}</x-ui.badge>
                                        <x-ui.badge color="{{ $priorityColors[$task->priority] ?? 'light' }}">{{ $task->priority }}</x-ui.badge>
                                        @if ($task->group)
                                            <x-ui.badge color="{{ $groupBadgeColors[$task->group->color] ?? 'light' }}">{{ $task->group->name }}</x-ui.badge>
                                        @endif
                                    </div>

                                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $task->title }}</h2>
                                    @if ($task->description)
                                        <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $task->description }}</p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('follow-up-form-{{ $task->id }}')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); document.getElementById('follow-up-title-{{ $task->id }}')?.focus();"
                                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-10 items-center justify-center rounded-lg px-4 text-sm font-medium text-white transition"
                                    >
                                        Tambah Follow Up
                                    </button>

                                    @if ($task->status !== 'done')
                                        <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                                            >
                                                Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-4">
                                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Source</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $task->source }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Task Due</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $task->due_date?->format('d M Y') ?? '-' }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Follow Up</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $task->followUps->count() }} total</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $pendingFollowUps }} item</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-6 2xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                            <div id="follow-up-form-{{ $task->id }}" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                                <div class="mb-5">
                                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Tambah Follow Up</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Action button di atas langsung mengarahkan ke form ini.</p>
                                </div>

                                <form id="follow-up-submit-form-{{ $task->id }}" method="POST" action="{{ route('tasks.follow-up.store', $task) }}" enctype="multipart/form-data" class="space-y-4" data-follow-up-form>
                                    @csrf
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Follow up</label>
                                            <input
                                                id="follow-up-title-{{ $task->id }}"
                                                type="text"
                                                name="title"
                                                placeholder="Follow up"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                required
                                            >
                                        </div>

                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reminder</label>
                                            <input
                                                type="datetime-local"
                                                name="reminder_at"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Keterangan</label>
                                        <textarea
                                            id="follow-up-description-{{ $task->id }}"
                                            data-follow-up-editor
                                            data-follow-up-target="follow-up-submit-form-{{ $task->id }}"
                                            name="description"
                                            rows="3"
                                            placeholder="Deskripsi detail follow up"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                        ></textarea>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tip: Enter untuk simpan cepat, Shift + Enter untuk baris baru.</p>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Next Follow Up</label>
                                            <input
                                                type="datetime-local"
                                                name="next_follow_up_at"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            >
                                        </div>

                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Target Selesai</label>
                                            <input
                                                type="datetime-local"
                                                name="due_at"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            >
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Lampiran</label>
                                        <input
                                            type="file"
                                            name="files[]"
                                            multiple
                                            class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400"
                                        >
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, PDF, DOC, XLS, TXT, ZIP. Maksimal 5MB per file.</p>
                                    </div>

                                    <div class="sticky bottom-0 -mx-5 border-t border-gray-100 bg-white/95 px-5 pt-3 pb-1 shadow-[0_-10px_24px_-22px_rgba(17,24,39,0.85)] backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/95">
                                        <button
                                            type="submit"
                                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition"
                                        >
                                            Simpan Follow Up
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]" id="timeline-card-{{ $task->id }}">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <div>
                                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Timeline Follow Up</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Activity log untuk task ini.</p>
                                    </div>
                                    <x-ui.badge color="primary">{{ $task->followUps->count() }}</x-ui.badge>
                                </div>

                                @php
                                    $insights = $insightsByTask[$task->id] ?? [];
                                @endphp

                                <div class="mb-4 space-y-2">
                                    @foreach ($insights as $insight)
                                        <div class="rounded-lg p-3 text-sm font-medium {{ ($insight['type'] ?? 'info') === 'warning'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-500/15 dark:text-red-300'
                                                : 'bg-blue-100 text-blue-800 dark:bg-blue-500/15 dark:text-blue-300' }}">
                                            ⚠️ {{ $insight['message'] ?? '-' }}
                                        </div>
                                    @endforeach
                                </div>

                                <div
                                    class="max-h-[620px] overflow-y-auto pr-2 custom-scrollbar"
                                    data-follow-up-timeline
                                    data-task-id="{{ $task->id }}"
                                    x-init="$nextTick(() => window.scrollFollowUpTimeline && window.scrollFollowUpTimeline($el))"
                                    x-on:task-timeline-changed.window="$nextTick(() => selectedTask === {{ $task->id }} && window.scrollFollowUpTimeline && window.scrollFollowUpTimeline($el))"
                                >
                                    @forelse ($followUpsByDate as $date => $items)
                                        @php
                                            $dateValue = \Illuminate\Support\Carbon::parse($date);
                                            $dateLabel = $dateValue->isToday()
                                                ? 'Hari Ini'
                                                : ($dateValue->isYesterday() ? 'Kemarin' : $dateValue->format('d M Y'));
                                        @endphp

                                        <div class="sticky top-0 z-10 mb-4 mt-2 flex justify-center">
                                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-500 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                                                {{ $dateLabel }}
                                            </span>
                                        </div>

                                        <div class="space-y-2.5">
                                            @foreach ($items as $followUp)
                                                @php
                                                    $followUpCreatorId = data_get($followUp, 'created_by');
                                                    $isUserBubble = auth()->check() && $followUpCreatorId && (int) $followUpCreatorId === (int) auth()->id();
                                                    $statusBackgroundClass = $followUpStatusBackgrounds[$followUp->status] ?? 'border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800';
                                                    $statusDotClass = $followUp->status === 'done'
                                                        ? 'bg-green-500'
                                                        : ($followUp->status === 'missed' ? 'bg-red-500' : 'bg-yellow-500');
                                                    $statusBadgeClass = $followUp->status === 'done'
                                                        ? 'bg-green-200 text-green-800 dark:bg-green-500/20 dark:text-green-300'
                                                        : ($followUp->status === 'missed'
                                                            ? 'bg-red-200 text-red-800 dark:bg-red-500/20 dark:text-red-300'
                                                            : 'bg-yellow-200 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-300');
                                                @endphp

                                                <div class="grid grid-cols-[62px_minmax(0,1fr)] gap-2 text-sm" data-follow-up-item>
                                                    <div class="pt-1 text-right">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $followUp->created_at?->format('H:i') ?? '-' }}</p>
                                                        <p class="mt-0.5 text-[11px] text-gray-400 dark:text-gray-500">{{ $followUp->created_at?->format('d M') ?? '-' }}</p>
                                                    </div>

                                                    <div class="relative border-l border-gray-200 pb-2 pl-4 dark:border-gray-800">
                                                        <div class="absolute -left-[6px] top-1.5 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900 {{ $statusDotClass }}">
                                                        </div>

                                                        <div class="max-w-[96%] rounded-xl border p-3 shadow-theme-xs transition hover:shadow-theme-sm {{ $statusBackgroundClass }} {{ $isUserBubble ? 'ml-auto' : '' }}">
                                                            <div class="mb-2 flex items-center justify-between gap-2">
                                                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                    {{ $isUserBubble ? 'User' : 'System' }}
                                                                </p>
                                                                <span class="rounded px-2 py-0.5 text-xs font-medium {{ $statusBadgeClass }}">
                                                                    {{ ucfirst($followUp->status ?? 'pending') }}
                                                                </span>
                                                            </div>

                                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                                <div>
                                                                    <p class="text-sm font-semibold leading-5 text-gray-800 dark:text-white/90">{{ $followUp->title ?? '-' }}</p>
                                                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                                                        Reminder: {{ $followUp->reminder_at?->format('d M Y, H:i') ?? '-' }}
                                                                    </p>
                                                                </div>

                                                                <x-ui.badge color="{{ $followUpStatusColors[$followUp->status] ?? 'light' }}">
                                                                    {{ $followUp->status ?? '-' }}
                                                                </x-ui.badge>
                                                            </div>

                                                            @if ($followUp->description || $followUp->note)
                                                                <p class="mt-2 text-sm leading-5 text-gray-700 dark:text-gray-200">
                                                                    {{ $followUp->description ?? $followUp->note }}
                                                                </p>
                                                            @endif

                                                            <div class="mt-2 grid gap-1.5 text-xs text-gray-600 dark:text-gray-300 sm:grid-cols-2">
                                                                <p>Next: {{ $followUp->next_follow_up_at?->format('d M Y, H:i') ?? '-' }}</p>
                                                                <p>Due: {{ $followUp->due_at?->format('d M Y, H:i') ?? '-' }}</p>
                                                            </div>

                                                            @if ($followUp->attachments->isNotEmpty())
                                                                <div class="mt-2 space-y-2 border-t border-gray-200/70 pt-2 dark:border-gray-700/70">
                                                                    @foreach ($followUp->attachments as $attachment)
                                                                        @php
                                                                            $attachmentName = $attachment->file_name ?? 'attachment';
                                                                            $attachmentUrl = $attachment->file_url ?? '#';
                                                                            $extension = strtolower(pathinfo($attachmentName, PATHINFO_EXTENSION));
                                                                            $isImageAttachment = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
                                                                            $isPdfAttachment = $extension === 'pdf';
                                                                        @endphp

                                                                        <a
                                                                            href="{{ $attachmentUrl }}"
                                                                            target="_blank"
                                                                            rel="noopener"
                                                                            class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white/70 px-2 py-1.5 text-xs font-medium text-gray-700 transition hover:border-brand-300 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-300"
                                                                        >
                                                                            @if ($isImageAttachment)
                                                                                <img
                                                                                    src="{{ $attachmentUrl }}"
                                                                                    alt="{{ $attachmentName }}"
                                                                                    class="h-9 w-9 rounded object-cover"
                                                                                    loading="lazy"
                                                                                >
                                                                            @elseif ($isPdfAttachment)
                                                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded bg-red-100 text-[11px] font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">PDF</span>
                                                                            @else
                                                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded bg-gray-200 text-[11px] font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">FILE</span>
                                                                            @endif
                                                                            <span class="truncate">{{ $attachmentName }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @empty
                                        <div class="rounded-lg border border-dashed border-gray-200 px-4 py-10 text-center dark:border-gray-800">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada follow up. Mulai sekarang 🚀</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Klik tombol Tambah Follow Up untuk mulai tracking aktivitas.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-20 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih atau buat task terlebih dahulu.</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detail task dan form follow-up akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        window.scrollFollowUpTimeline = function (timelineElement) {
            if (!timelineElement) {
                return;
            }

            const items = timelineElement.querySelectorAll('[data-follow-up-item]');
            const lastItem = items.length ? items[items.length - 1] : null;

            if (lastItem) {
                lastItem.scrollIntoView({ behavior: 'auto', block: 'end' });
                return;
            }

            timelineElement.scrollTop = timelineElement.scrollHeight;
        };

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-follow-up-timeline]').forEach(function (timelineElement) {
                window.scrollFollowUpTimeline(timelineElement);
            });

            document.querySelectorAll('[data-follow-up-editor]').forEach(function (textarea) {
                textarea.addEventListener('keydown', function (event) {
                    if (event.key !== 'Enter' || event.shiftKey) {
                        return;
                    }

                    event.preventDefault();

                    const formId = textarea.getAttribute('data-follow-up-target');
                    const form = formId ? document.getElementById(formId) : textarea.closest('form');

                    if (form) {
                        form.requestSubmit();
                    }
                });
            });
        });
    </script>
@endsection
