@extends('layouts.tailadmin')

@section('title', 'Sửa lịch — ' . $barber->user->name)

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.schedules.index') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Lịch làm việc</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-700 dark:text-gray-300">{{ $barber->user->name }}</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa lịch làm việc — {{ $barber->user->name }}</h2>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400 rounded-lg text-sm">
            <p class="font-medium mb-1">Có lỗi xảy ra:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-100 dark:bg-brand-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Cài đặt lịch làm việc hàng tuần</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bật/tắt ngày làm việc và chọn giờ bắt đầu — kết thúc.</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.schedules.update', $barber) }}"
                  x-data="scheduleForm()" x-cloak>
                @csrf
                @method('PUT')

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($days as $index => $day)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4 transition-colors duration-200"
                             :class="schedules[{{ $index }}].is_working ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/60 dark:bg-gray-800/40'">

                            <input type="hidden" name="schedules[{{ $index }}][day_of_week]" value="{{ $day['day_of_week'] }}">

                            <div class="flex items-center gap-3 sm:w-44 shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="schedules[{{ $index }}][is_working]" value="0">
                                    <input type="checkbox"
                                           name="schedules[{{ $index }}][is_working]"
                                           value="1"
                                           x-model="schedules[{{ $index }}].is_working"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-100 dark:peer-focus:ring-brand-900/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                                </label>
                                <span class="text-sm font-semibold min-w-[72px]"
                                      :class="schedules[{{ $index }}].is_working ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-500'">
                                    {{ $day['label'] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 flex-1" x-show="schedules[{{ $index }}].is_working"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Từ</label>
                                    <input type="time" name="schedules[{{ $index }}][start_time]"
                                           x-model="schedules[{{ $index }}].start_time"
                                           class="block border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm text-sm focus:ring-brand-500 focus:border-brand-500 py-2 px-3 w-32">
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Đến</label>
                                    <input type="time" name="schedules[{{ $index }}][end_time]"
                                           x-model="schedules[{{ $index }}].end_time"
                                           class="block border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm text-sm focus:ring-brand-500 focus:border-brand-500 py-2 px-3 w-32">
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500 hidden md:inline-block"
                                      x-text="calculateDuration(schedules[{{ $index }}].start_time, schedules[{{ $index }}].end_time)">
                                </span>
                            </div>

                            <div x-show="!schedules[{{ $index }}].is_working" class="flex-1">
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                    Ngày nghỉ
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                    <a href="{{ route('admin.schedules.index') }}"
                       class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        ← Quay lại
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-100 dark:focus:ring-brand-900/30 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Lưu lịch làm việc
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function scheduleForm() {
        return {
            schedules: @json($daysJson),

            calculateDuration(start, end) {
                if (!start || !end) return '';
                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);
                let diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff <= 0) return '';
                const hours = Math.floor(diff / 60);
                const mins = diff % 60;
                let result = '';
                if (hours > 0) result += hours + ' giờ ';
                if (mins > 0) result += mins + ' phút';
                return '(' + result.trim() + ')';
            }
        };
    }
</script>
@endpush
