@extends('layouts.tailbarber')

@section('page', 'Leaves')
@section('title', 'Quản lý ngày nghỉ')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý ngày nghỉ</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gửi đơn xin nghỉ đột xuất — Admin sẽ duyệt trước khi slot bị khoá.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form gửi đơn xin nghỉ --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Gửi đơn xin nghỉ
                </h3>

                <form method="POST" action="{{ route('barber.leaves.store') }}" x-data="{ type: '{{ old('type', 'full_day') }}' }">
                    @csrf
                    {{-- Ngày nghỉ --}}
                    <div class="mb-4">
                        <label for="leave_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày nghỉ <span class="text-red-500">*</span></label>
                        <input type="date" name="leave_date" id="leave_date"
                            value="{{ old('leave_date', now()->addDay()->format('Y-m-d')) }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Loại nghỉ --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loại nghỉ <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="full_day" x-model="type"
                                    class="text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Cả ngày</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="partial" x-model="type"
                                    class="text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Một phần</span>
                            </label>
                        </div>
                    </div>

                    {{-- Giờ (chỉ khi partial) --}}
                    <div x-show="type === 'partial'" x-transition class="mb-4 grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Từ</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Đến</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    {{-- Lý do --}}
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lý do</label>
                        <textarea name="reason" id="reason" rows="2" maxlength="255"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500 resize-none"
                            placeholder="VD: Bận việc gia đình...">{{ old('reason') }}</textarea>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Gửi đơn xin nghỉ
                    </button>
                </form>
            </div>
        </div>

        {{-- Danh sách đơn nghỉ sắp tới --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Đơn nghỉ sắp tới
                    </h3>
                </div>

                @if($leaves->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Không có đơn nghỉ nào sắp tới.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($leaves as $leave)
                            <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    {{-- Date badge --}}
                                    <div class="flex-shrink-0 w-14 h-14 rounded-lg {{ $leave->leave_date->isToday() ? 'bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-400' : 'bg-gray-100 dark:bg-gray-700' }} flex flex-col items-center justify-center">
                                        <span class="text-xs font-medium {{ $leave->leave_date->isToday() ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $leave->leave_date->locale('vi')->isoFormat('ddd') }}
                                        </span>
                                        <span class="text-lg font-bold {{ $leave->leave_date->isToday() ? 'text-amber-700 dark:text-amber-300' : 'text-gray-800 dark:text-white' }}">
                                            {{ $leave->leave_date->format('d') }}
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $leave->leave_date->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            {{-- Loại nghỉ --}}
                                            @if($leave->type === 'full_day')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    Cả ngày
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($leave->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($leave->end_time)->format('H:i') }}
                                                </span>
                                            @endif

                                            {{-- Trạng thái duyệt --}}
                                            @php
                                                $statusColors = [
                                                    'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$leave->status->value] ?? '' }}">
                                                {{ $leave->status->label() }}
                                            </span>

                                            @if($leave->reason)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">— {{ $leave->reason }}</span>
                                            @endif
                                        </div>

                                        {{-- Ghi chú admin --}}
                                        @if($leave->admin_note)
                                            <p class="text-xs text-gray-400 mt-1 italic">
                                                <span class="font-medium">Admin:</span> {{ $leave->admin_note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Nút Huỷ (chỉ khi pending hoặc approved, và chưa qua ngày) --}}
                                @if($leave->status->value !== 'rejected' && $leave->leave_date->gte(now()->startOfDay()))
                                    <form method="POST" action="{{ route('barber.leaves.destroy', $leave) }}"
                                        onsubmit="return confirm('Bạn có chắc muốn huỷ đơn nghỉ này?{{ $leave->status->value === 'approved' ? ' Các slot đã khoá sẽ được mở lại.' : '' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Huỷ
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Lịch sử nghỉ (quá khứ) --}}
            @if($pastLeaves->isNotEmpty())
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Lịch sử nghỉ
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pastLeaves->take(10) as $leave)
                            <div class="p-4 flex items-center gap-4 opacity-60">
                                <div class="flex-shrink-0 w-14 h-14 rounded-lg bg-gray-100 dark:bg-gray-700 flex flex-col items-center justify-center">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $leave->leave_date->locale('vi')->isoFormat('ddd') }}</span>
                                    <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $leave->leave_date->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $leave->leave_date->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($leave->type === 'full_day')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Cả ngày</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($leave->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($leave->end_time)->format('H:i') }}
                                            </span>
                                        @endif
                                        @php
                                            $historyColors = [
                                                'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $historyColors[$leave->status->value] ?? '' }}">
                                            {{ $leave->status->label() }}
                                        </span>
                                        @if($leave->reason)
                                            <span class="text-xs text-gray-400">— {{ $leave->reason }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
