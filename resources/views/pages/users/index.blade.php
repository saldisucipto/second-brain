@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Management" />

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

        <x-common.component-card title="Tambah User" desc="Buat akun baru untuk mengakses Second Brain.">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_1fr_auto] lg:items-end">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama user" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" required>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" required>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                    <input type="password" name="password" placeholder="Password" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" required>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Photo</label>
                    <input type="file" name="photo" accept="image/*" class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400">
                </div>
                <button type="submit" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 items-center justify-center rounded-lg px-5 text-sm font-medium text-white transition">
                    Tambah
                </button>
            </form>
        </x-common.component-card>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Users</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akun yang dapat masuk ke aplikasi.</p>
                </div>
                <x-ui.badge color="primary">{{ $users->total() }} user</x-ui.badge>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">User</p></th>
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Email</p></th>
                            <th class="px-6 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Joined</p></th>
                            <th class="px-6 py-3 text-right"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Action</p></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @foreach ($users as $user)
                            <tr x-data="{ editing: false }">
                                <td class="px-6 py-3.5">
                                    <form id="update-user-{{ $user->id }}" method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <input x-show="editing" type="text" name="name" value="{{ $user->name }}" class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-44 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <div x-show="!editing" class="flex items-center gap-3">
                                            @if ($user->photo_url)
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $user->name }}</p>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-6 py-3.5">
                                    <input x-show="editing" form="update-user-{{ $user->id }}" type="email" name="email" value="{{ $user->email }}" class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-56 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <p x-show="!editing" class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->email }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div x-show="editing" class="space-y-2">
                                        <input form="update-user-{{ $user->id }}" type="password" name="password" placeholder="New password optional" class="dark:bg-dark-900 shadow-theme-xs h-10 w-full min-w-48 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <input form="update-user-{{ $user->id }}" type="file" name="photo" accept="image/*" class="w-full min-w-48 text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-xs file:text-gray-700 dark:text-gray-400 dark:file:bg-white/5 dark:file:text-gray-300">
                                    </div>
                                    <p x-show="!editing" class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <button x-show="!editing" type="button" @click="editing = true" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Edit
                                        </button>
                                        <button x-show="editing" type="submit" form="update-user-{{ $user->id }}" class="inline-flex items-center rounded-lg bg-brand-500 px-3 py-2 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                                            Save
                                        </button>
                                        <button x-show="editing" type="button" @click="editing = false" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Cancel
                                        </button>
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg border border-error-300 bg-white px-3 py-2 text-theme-sm font-medium text-error-600 shadow-theme-xs hover:bg-error-50 dark:border-error-500/30 dark:bg-gray-800 dark:text-error-400">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
