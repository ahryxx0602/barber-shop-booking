@php
    $unreadNotifications = auth()->user()->notifications()
        ->where('is_read', false)
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();
    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
@endphp

<div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
    <button @click="notifOpen = !notifOpen"
        class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        title="Thông báo">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C10.9 2 10 2.9 10 4C10 4.11 10.01 4.22 10.03 4.33C7.12 5.14 5 7.82 5 11V17L3 19V20H21V19L19 17V11C19 7.82 16.88 5.14 13.97 4.33C13.99 4.22 14 4.11 14 4C14 2.9 13.1 2 12 2ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="notifOpen" x-transition x-cloak
        class="absolute right-0 mt-4 w-[340px] rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
            <span class="text-sm font-semibold text-gray-800 dark:text-white">Thông báo</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        Đánh dấu tất cả đã đọc
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-[360px] overflow-y-auto">
            @forelse($unreadNotifications as $notification)
                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-800 bg-blue-50/50 dark:bg-blue-900/10">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $notification->message }}</p>
                    <span class="text-[11px] text-gray-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto mb-2 fill-gray-300 dark:fill-gray-600" width="32" height="32" viewBox="0 0 24 24"><path d="M12 2C10.9 2 10 2.9 10 4C10 4.11 10.01 4.22 10.03 4.33C7.12 5.14 5 7.82 5 11V17L3 19V20H21V19L19 17V11C19 7.82 16.88 5.14 13.97 4.33C13.99 4.22 14 4.11 14 4C14 2.9 13.1 2 12 2ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z"/></svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Không có thông báo mới</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
