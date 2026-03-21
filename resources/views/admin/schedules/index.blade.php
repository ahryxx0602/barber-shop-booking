@extends('layouts.tailadmin')

@section('title', 'Lịch làm việc')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-100 dark:bg-brand-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Lịch làm việc các thợ</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Xem và quản lý lịch làm việc hàng tuần của tất cả thợ cắt.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</th>
                            @php
                                $dayLabels = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                            @endphp
                            @foreach ($dayLabels as $label)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $label }}</th>
                            @endforeach
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($barbersData as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                {{-- Barber info --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($item['barber']->user->avatar)
                                            <img src="{{ Storage::url($item['barber']->user->avatar) }}"
                                                 class="w-9 h-9 object-cover rounded-full" alt="">
                                        @else
                                            <div class="w-9 h-9 bg-brand-100 dark:bg-brand-900/30 rounded-full flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-semibold">
                                                {{ strtoupper(substr($item['barber']->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['barber']->user->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item['barber']->experience_years }} năm KN</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Schedule for each day --}}
                                @foreach ($item['days'] as $day)
                                    <td class="px-3 py-4 text-center">
                                        @if ($day['is_day_off'])
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700" title="Ngày nghỉ">
                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                </svg>
                                            </span>
                                        @else
                                            <div class="text-xs">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-medium">
                                                    {{ $day['start_time'] }}–{{ $day['end_time'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Actions --}}
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('admin.schedules.edit', $item['barber']) }}"
                                       class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 text-sm font-medium">
                                        Sửa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Chưa có thợ cắt nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
