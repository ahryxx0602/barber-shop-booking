@forelse($unreadNotifications as $notification)
    <div class="px-5 py-3.5 border-b border-gray-50 dark:border-gray-800 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-100/50 dark:hover:bg-blue-900/30 transition-colors">
        <p class="text-[15px] text-gray-800 dark:text-gray-200 leading-relaxed">{{ $notification->message }}</p>
        <span class="text-xs text-gray-500 mt-1.5 block">{{ $notification->created_at->diffForHumans() }}</span>
    </div>
@empty
    <div class="px-5 py-10 text-center">
        <svg class="mx-auto mb-3 fill-gray-300 dark:fill-gray-600" width="40" height="40" viewBox="0 0 24 24"><path d="M12 2C10.9 2 10 2.9 10 4C10 4.11 10.01 4.22 10.03 4.33C7.12 5.14 5 7.82 5 11V17L3 19V20H21V19L19 17V11C19 7.82 16.88 5.14 13.97 4.33C13.99 4.22 14 4.11 14 4C14 2.9 13.1 2 12 2ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z"/></svg>
        <p class="text-[15px] text-gray-400 dark:text-gray-500">Không có thông báo mới</p>
    </div>
@endforelse
