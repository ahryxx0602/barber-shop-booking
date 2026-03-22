@use('App\Enums\BookingStatus')

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        {{-- Time --}}
        <div class="flex-shrink-0 w-20 text-center">
            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</p>
            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2">
                <span class="font-semibold text-gray-800 dark:text-white">{{ $booking->customer->name }}</span>
                {{-- Status Badge --}}
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($booking->status === BookingStatus::Pending) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                    @elseif($booking->status === BookingStatus::Confirmed) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                    @elseif($booking->status === BookingStatus::InProgress) bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                    @elseif($booking->status === BookingStatus::Completed) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                    @elseif($booking->status === BookingStatus::Cancelled) bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                    @endif">
                    {{ $booking->status->label() }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $booking->services->pluck('name')->join(', ') }}
            </p>
            @if($booking->customer->phone)
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $booking->customer->phone }}
                    </span>
                </p>
            @endif
            @if($booking->note)
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1 italic">"{{ $booking->note }}"</p>
            @endif
            @if($booking->cancel_reason)
                <p class="text-sm text-red-500 mt-1">Lý do huỷ: {{ $booking->cancel_reason }}</p>
            @endif
        </div>

        {{-- Price + Code --}}
        <div class="flex-shrink-0 text-right">
            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($booking->total_price, 0, ',', '.') }}d</p>
            <p class="text-xs text-gray-400 font-mono">{{ $booking->booking_code }}</p>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($booking->status !== BookingStatus::Completed && $booking->status !== BookingStatus::Cancelled)
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
            @if($booking->status === BookingStatus::Pending)
                <form method="POST" action="{{ route('barber.bookings.confirm', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Xác nhận
                    </button>
                </form>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-red-500 text-red-600 hover:bg-red-600 hover:text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Từ chối
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute z-10 mt-2 left-0 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                        <form method="POST" action="{{ route('barber.bookings.reject', $booking) }}">
                            @csrf @method('PATCH')
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lý do từ chối</label>
                            <textarea name="cancel_reason" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" placeholder="Nhập lý do..."></textarea>
                            <button type="submit" class="mt-2 w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Xác nhận từ chối
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($booking->status === BookingStatus::Confirmed)
                <form method="POST" action="{{ route('barber.bookings.start', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-purple-600 text-purple-600 hover:bg-purple-600 hover:text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                        Bắt đầu phục vụ
                    </button>
                </form>
            @endif

            @if($booking->status === BookingStatus::InProgress)
                <form method="POST" action="{{ route('barber.bookings.complete', $booking) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hoàn thành
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
