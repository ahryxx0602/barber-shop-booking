@php
    $unreadNotifications = auth()->user()->notifications()
        ->where('is_read', false)
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();
    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
@endphp

<div class="relative" 
    x-data="{ 
        notifOpen: false, 
        unreadCount: {{ $unreadCount }},
        init() {
            setInterval(() => {
                fetch('{{ route('notifications.poll') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.unreadCount = data.count;
                        this.$refs.notifList.innerHTML = data.html;
                    })
                    .catch(e => console.error('Lỗi lấy thông báo:', e));
            }, 5000); 
        }
    }" 
    @click.outside="notifOpen = false">
    
    <button @click="notifOpen = !notifOpen"
        class="relative flex h-14 w-14 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white shadow-sm"
        title="Thông báo">
        <svg class="fill-current" width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C10.9 2 10 2.9 10 4C10 4.11 10.01 4.22 10.03 4.33C7.12 5.14 5 7.82 5 11V17L3 19V20H21V19L19 17V11C19 7.82 16.88 5.14 13.97 4.33C13.99 4.22 14 4.11 14 4C14 2.9 13.1 2 12 2ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z" />
        </svg>
        <template x-if="unreadCount > 0">
            <span class="absolute top-0 right-0 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white shadow-sm" x-text="unreadCount > 9 ? '9+' : unreadCount">
            </span>
        </template>
    </button>

    <!-- Notification Dropdown -->
    <div x-show="notifOpen" x-transition x-cloak
        class="absolute right-0 mt-4 rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 z-50 overflow-hidden"
        style="width: 380px; max-width: 90vw;">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
            <span class="text-lg font-semibold text-gray-800 dark:text-white shrink-0">Thông báo</span>
            <template x-if="unreadCount > 0">
                <form method="POST" action="{{ route('notifications.read-all') }}" class="inline ml-2 text-right">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium transition-colors whitespace-nowrap">
                        Đánh dấu tất cả đã đọc
                    </button>
                </form>
            </template>
        </div>

        <div class="overflow-y-auto" style="max-height: 400px;" x-ref="notifList">
            @include('partials.notification-items', ['unreadNotifications' => $unreadNotifications])
        </div>
    </div>
</div>
