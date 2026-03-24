@extends('layouts.tailadmin')

@section('page', 'leaves')
@section('title', 'Quản lý ngày nghỉ')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý ngày nghỉ</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Duyệt hoặc từ chối đơn xin nghỉ của thợ cắt.</p>
        </div>
        @if($pendingCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                {{ $pendingCount }} đơn chờ duyệt
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- Filter tabs --}}
    <div class="mb-6 flex gap-2 flex-wrap">
        @if(!$statusFilter)
            <a href="{{ route('admin.leaves.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg shadow-sm" style="background-color: #2563eb; color: #fff;">Tất cả</a>
        @else
            <a href="{{ route('admin.leaves.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">Tất cả</a>
        @endif

        @if($statusFilter === 'pending')
            <a href="{{ route('admin.leaves.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm font-medium rounded-lg shadow-sm" style="background-color: #eab308; color: #fff;">
                Chờ duyệt
                @if($pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color: #fff; color: #a16207;">{{ $pendingCount }}</span>
                @endif
            </a>
        @else
            <a href="{{ route('admin.leaves.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                Chờ duyệt
                @if($pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color: #fef08a; color: #854d0e;">{{ $pendingCount }}</span>
                @endif
            </a>
        @endif

        @if($statusFilter === 'approved')
            <a href="{{ route('admin.leaves.index', ['status' => 'approved']) }}" class="px-4 py-2 text-sm font-medium rounded-lg shadow-sm" style="background-color: #16a34a; color: #fff;">Đã duyệt</a>
        @else
            <a href="{{ route('admin.leaves.index', ['status' => 'approved']) }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">Đã duyệt</a>
        @endif

        @if($statusFilter === 'rejected')
            <a href="{{ route('admin.leaves.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm font-medium rounded-lg shadow-sm" style="background-color: #dc2626; color: #fff;">Từ chối</a>
        @else
            <a href="{{ route('admin.leaves.index', ['status' => 'rejected']) }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">Từ chối</a>
        @endif
    </div>

    {{-- Danh sách đơn nghỉ --}}
    @if($leaves->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Không có đơn nghỉ nào.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Thợ cắt</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ngày nghỉ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Loại</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lý do</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ngày gửi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $leave->status->value === 'pending' ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '' }}">
                                {{-- Thợ cắt --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($leave->barber->user->avatar)
                                            <img src="{{ asset('storage/' . $leave->barber->user->avatar) }}" class="w-8 h-8 rounded-full object-cover" alt="">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-300">
                                                {{ mb_substr($leave->barber->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $leave->barber->user->name }}</span>
                                    </div>
                                </td>

                                {{-- Ngày nghỉ --}}
                                <td class="px-4 py-3">
                                    <span class="text-gray-800 dark:text-white font-medium">{{ $leave->leave_date->format('d/m/Y') }}</span>
                                    <span class="text-xs text-gray-400 block">{{ $leave->leave_date->locale('vi')->isoFormat('dddd') }}</span>
                                </td>

                                {{-- Loại --}}
                                <td class="px-4 py-3">
                                    @if($leave->type === 'full_day')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Cả ngày</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                            {{ \Carbon\Carbon::parse($leave->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($leave->end_time)->format('H:i') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Lý do --}}
                                <td class="px-4 py-3 max-w-[200px]">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 truncate block">{{ $leave->reason ?? '—' }}</span>
                                </td>

                                {{-- Trạng thái --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $colors = [
                                            'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$leave->status->value] ?? '' }}">
                                        {{ $leave->status->label() }}
                                    </span>
                                    @if($leave->reviewer)
                                        <p class="text-xs text-gray-400 mt-1">bởi {{ $leave->reviewer->name }}</p>
                                    @endif
                                    @if($leave->admin_note)
                                        <p class="text-xs text-gray-400 mt-0.5 italic">{{ $leave->admin_note }}</p>
                                    @endif
                                </td>

                                {{-- Ngày gửi --}}
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $leave->created_at->format('d/m H:i') }}
                                </td>

                                {{-- Hành động --}}
                                <td class="px-4 py-3 text-center" x-data="{ showNote: false, note: '' }">
                                    @if($leave->status->value === 'pending')
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Duyệt --}}
                                            <form method="POST" action="{{ route('admin.leaves.approve', $leave) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-green-600 hover:text-green-700 border border-green-200 dark:border-green-800 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                                    title="Duyệt">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Duyệt
                                                </button>
                                            </form>

                                            {{-- Từ chối (mở form ghi chú) --}}
                                            <button @click="showNote = !showNote" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:text-red-700 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Từ chối">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Từ chối
                                            </button>
                                        </div>

                                        {{-- Form ghi chú từ chối --}}
                                        <div x-show="showNote" x-transition class="mt-2">
                                            <form method="POST" action="{{ route('admin.leaves.reject', $leave) }}" class="flex flex-col gap-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="admin_note" x-model="note" placeholder="Lý do từ chối (tuỳ chọn)"
                                                    class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs focus:ring-red-500 focus:border-red-500">
                                                <button type="submit" class="text-xs text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition-colors">
                                                    Xác nhận từ chối
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
