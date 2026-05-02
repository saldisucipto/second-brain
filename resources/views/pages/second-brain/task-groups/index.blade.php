@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Task Categories" />

    @php
        $colorOptions = [
            'red' => 'error',
            'green' => 'success',
            'blue' => 'primary',
            'yellow' => 'warning',
        ];
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

        <x-common.component-card title="Tambah Kategori Task" desc="Kelompokkan task berdasarkan area kerja atau konteks.">
            <form method="POST" action="{{ route('task-groups.store') }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_220px_auto] lg:items-end">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Pekerjaan Kantor"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        required
                    >
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Slug</label>
                    <input
                        type="text"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="Auto dari name"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    >
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Color</label>
                    <select
                        name="color"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                        <option value="">No color</option>
                        @foreach ($colorOptions as $color => $badgeColor)
                            <option value="{{ $color }}" @selected(old('color') === $color)>{{ ucfirst($color) }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition">
                    Tambah
                </button>
            </form>
        </x-common.component-card>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kategori Task</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        CRUD kategori yang dipakai saat membuat task.
                    </p>
                </div>

                <x-ui.badge color="primary">{{ $groups->total() }} kategori</x-ui.badge>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Name</p></th>
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Slug</p></th>
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Color</p></th>
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tasks</p></th>
                            <th class="px-6 py-3 text-right"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Action</p></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($groups as $group)
                            <tr x-data="{ editing: false }">
                                <td class="px-6 py-3.5">
                                    <form id="update-task-group-{{ $group->id }}" method="POST" action="{{ route('task-groups.update', $group) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input
                                            x-show="editing"
                                            type="text"
                                            name="name"
                                            value="{{ $group->name }}"
                                            class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-48 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                            required
                                        >
                                        <p x-show="!editing" class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $group->name }}</p>
                                    </form>
                                </td>
                                <td class="px-6 py-3.5">
                                    <input
                                        x-show="editing"
                                        form="update-task-group-{{ $group->id }}"
                                        type="text"
                                        name="slug"
                                        value="{{ $group->slug }}"
                                        class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-48 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    >
                                    <p x-show="!editing" class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $group->slug }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <select
                                        x-show="editing"
                                        form="update-task-group-{{ $group->id }}"
                                        name="color"
                                        class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-32 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    >
                                        <option value="">No color</option>
                                        @foreach ($colorOptions as $color => $badgeColor)
                                            <option value="{{ $color }}" @selected($group->color === $color)>{{ ucfirst($color) }}</option>
                                        @endforeach
                                    </select>

                                    <span x-show="!editing">
                                        @if ($group->color)
                                            <x-ui.badge color="{{ $colorOptions[$group->color] ?? 'light' }}">
                                                {{ $group->color }}
                                            </x-ui.badge>
                                        @else
                                            <span class="text-gray-500 text-theme-sm dark:text-gray-400">-</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $group->tasks_count }} task</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-show="!editing" type="button" @click="editing = true" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Edit
                                        </button>
                                        <button x-show="editing" type="submit" form="update-task-group-{{ $group->id }}" class="inline-flex items-center rounded-lg bg-brand-500 px-3 py-2 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                                            Save
                                        </button>
                                        <button x-show="editing" type="button" @click="editing = false" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Cancel
                                        </button>
                                        <form method="POST" action="{{ route('task-groups.destroy', $group) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-error-300 bg-white px-3 py-2 text-theme-sm font-medium text-error-600 shadow-theme-xs hover:bg-error-50 dark:border-error-500/30 dark:bg-gray-800 dark:text-error-400">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada kategori.</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan kategori pertama dari form di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($groups->hasPages())
                <div class="border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
