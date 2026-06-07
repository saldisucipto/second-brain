<div
    x-show="$store.requestLoading && $store.requestLoading.active"
    x-transition.opacity.duration.150ms
    x-cloak
    class="fixed inset-0 z-999999 flex items-center justify-center bg-white/70 backdrop-blur-sm dark:bg-black/60"
>
    <div class="flex flex-col items-center gap-3 rounded-2xl border border-gray-200 bg-white/95 px-6 py-5 shadow-theme-xl dark:border-gray-700 dark:bg-gray-900/95">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Memproses permintaan...</p>
    </div>
</div>
