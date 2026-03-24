@extends('layouts.tailadmin')

@section('title', 'Quản lý hoa hồng')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Quản lý hoa hồng</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Thiết lập tỷ lệ và theo dõi hoa hồng cho thợ cắt</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        {{-- Card: Tổng hoa hồng tháng --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @php $change = $overview['change']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $change > 0 ? 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400' : ($change < 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-gray-50 text-gray-500') }}">
                    @if ($change > 0) +{{ $change }}%
                    @elseif ($change < 0) {{ $change }}%
                    @else 0%
                    @endif
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['total_commission'], 0, ',', '.') }} ₫</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tổng hoa hồng tháng này</p>
        </div>

        {{-- Card: Doanh thu booking --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['total_booking_amount'], 0, ',', '.') }} ₫</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Doanh thu có hoa hồng</p>
        </div>

        {{-- Card: Số lượt tính --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($overview['total_records']) }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Booking đã tính hoa hồng</p>
        </div>

        {{-- Card: Tỷ lệ trung bình --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10">
                    <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overview['avg_rate'] }}%</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tỷ lệ hoa hồng thực tế</p>
        </div>

    </div>

    {{-- Thiết lập tỷ lệ hoa hồng + Bảng tổng hợp theo barber --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Cập nhật hàng loạt --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Cập nhật hàng loạt</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Áp dụng cùng 1 tỷ lệ cho tất cả hoặc nhóm thợ cắt đã chọn.
            </p>

            <form id="bulkForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tỷ lệ hoa hồng (%)</label>
                    <input type="number" name="commission_rate" id="bulkRate" step="0.5" min="0" max="100"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="VD: 30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Áp dụng cho</label>
                    <select id="bulkTarget"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all">Tất cả thợ cắt</option>
                        @foreach ($allBarbers as $b)
                            <option value="{{ $b->id }}">{{ $b->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" id="bulkSubmitBtn"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2.5 px-4 rounded-lg transition-colors
                    focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Cập nhật tỷ lệ
                </button>
            </form>

            <div id="bulkMessage" class="hidden mt-3 text-sm p-3 rounded-lg"></div>
        </div>

        {{-- Bảng tỷ lệ theo barber --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tỷ lệ hoa hồng theo thợ</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Tháng {{ now()->format('m/Y') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-3 font-medium">Thợ cắt</th>
                            <th class="pb-3 font-medium text-center">Tỷ lệ (%)</th>
                            <th class="pb-3 font-medium text-right">Doanh thu</th>
                            <th class="pb-3 font-medium text-right">Hoa hồng</th>
                            <th class="pb-3 font-medium text-center">Booking</th>
                            <th class="pb-3 font-medium text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse ($barbers as $barber)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" id="barber-row-{{ $barber['id'] }}">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($barber['avatar'])
                                            <img src="{{ asset('storage/' . $barber['avatar']) }}" alt="{{ $barber['name'] }}"
                                                class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ mb_substr($barber['name'], 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white">{{ $barber['name'] }}</p>
                                            <p class="text-xs text-gray-400">⭐ {{ number_format($barber['rating'], 1) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <input type="number" step="0.5" min="0" max="100"
                                        value="{{ $barber['commission_rate'] }}"
                                        class="rate-input w-20 text-center border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 text-sm
                                        bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                        focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        data-barber-id="{{ $barber['id'] }}"
                                        data-original="{{ $barber['commission_rate'] }}">
                                </td>
                                <td class="py-3 text-right text-gray-700 dark:text-gray-300 font-medium">
                                    {{ number_format($barber['total_booking_amount'], 0, ',', '.') }} ₫
                                </td>
                                <td class="py-3 text-right">
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($barber['total_commission'], 0, ',', '.') }} ₫
                                    </span>
                                </td>
                                <td class="py-3 text-center text-gray-600 dark:text-gray-400">
                                    {{ $barber['total_commission_bookings'] }}
                                </td>
                                <td class="py-3 text-center">
                                    <button type="button"
                                        class="save-rate-btn hidden text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors"
                                        data-barber-id="{{ $barber['id'] }}"
                                        title="Lưu tỷ lệ">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                    Chưa có dữ liệu thợ cắt
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Lịch sử hoa hồng chi tiết --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Lịch sử hoa hồng</h3>

            {{-- Bộ lọc --}}
            <form method="GET" action="{{ route('admin.commissions.index') }}" class="flex items-center gap-2 flex-wrap">
                <select name="barber_id"
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5
                    bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300
                    focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Tất cả thợ</option>
                    @foreach ($allBarbers as $b)
                        <option value="{{ $b->id }}" {{ request('barber_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->user->name }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5
                    bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300
                    focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5
                    bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300
                    focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <button type="submit"
                    class="text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition-colors">
                    Lọc
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 font-medium">Thời gian</th>
                        <th class="pb-3 font-medium">Mã booking</th>
                        <th class="pb-3 font-medium">Thợ cắt</th>
                        <th class="pb-3 font-medium">Khách hàng</th>
                        <th class="pb-3 font-medium text-right">Giá trị booking</th>
                        <th class="pb-3 font-medium text-center">Tỷ lệ</th>
                        <th class="pb-3 font-medium text-right">Hoa hồng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($history as $commission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 text-gray-500 dark:text-gray-400">
                                {{ $commission->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3">
                                <span class="font-mono text-xs text-blue-600 dark:text-blue-400">
                                    {{ $commission->booking->booking_code ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    @if ($commission->barber && $commission->barber->user)
                                        @if ($commission->barber->user->avatar)
                                            <img src="{{ asset('storage/' . $commission->barber->user->avatar) }}"
                                                class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center">
                                                <span class="text-xs text-emerald-600">{{ mb_substr($commission->barber->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-gray-700 dark:text-gray-300">{{ $commission->barber->user->name }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-400">
                                {{ $commission->booking->customer->name ?? '-' }}
                            </td>
                            <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                {{ number_format($commission->booking_amount, 0, ',', '.') }} ₫
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    {{ $commission->commission_rate }}%
                                </span>
                            </td>
                            <td class="py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($commission->commission_amount, 0, ',', '.') }} ₫
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                Chưa có lịch sử hoa hồng
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if ($history->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $history->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Ghi chú --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <strong class="text-gray-700 dark:text-gray-300">Ghi chú:</strong>
            Hoa hồng được <span class="font-medium text-emerald-600 dark:text-emerald-400">tự động tính</span> khi booking chuyển sang trạng thái
            <span class="font-medium text-green-600 dark:text-green-400">Hoàn thành</span>.
            Công thức: <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">Hoa hồng = Giá trị booking × Tỷ lệ % của thợ</code>.
            Mỗi booking chỉ tính hoa hồng 1 lần.
        </p>
    </div>

    {{-- Modal Xác nhận --}}
    <div id="confirmModal" class="fixed inset-0 z-[99999] hidden">
        {{-- Backdrop --}}
        <div id="confirmBackdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>
        {{-- Modal Content --}}
        <div class="flex items-center justify-center min-h-full p-4">
            <div id="confirmBox" class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all duration-300 scale-95 opacity-0">
                {{-- Icon --}}
                <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-500/10 mb-4">
                    <svg class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                {{-- Title --}}
                <h3 id="confirmTitle" class="text-lg font-semibold text-gray-800 dark:text-white text-center mb-2">Xác nhận thay đổi</h3>
                {{-- Message --}}
                <p id="confirmMessage" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6"></p>
                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button id="confirmCancel" type="button"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Huỷ bỏ
                    </button>
                    <button id="confirmOk" type="button"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast thông báo --}}
    <div id="toastContainer" class="fixed top-6 right-6 z-[99998] space-y-3"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

    // ===== MODAL CONFIRM =====
    const modal = document.getElementById('confirmModal');
    const backdrop = document.getElementById('confirmBackdrop');
    const box = document.getElementById('confirmBox');
    const titleEl = document.getElementById('confirmTitle');
    const messageEl = document.getElementById('confirmMessage');
    const cancelBtn = document.getElementById('confirmCancel');
    const okBtn = document.getElementById('confirmOk');

    let resolveConfirm = null;

    function showConfirmModal(title, message) {
        return new Promise((resolve) => {
            resolveConfirm = resolve;
            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            // Trigger animation
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            });
        });
    }

    function hideConfirmModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    cancelBtn.addEventListener('click', () => {
        hideConfirmModal();
        if (resolveConfirm) resolveConfirm(false);
    });

    okBtn.addEventListener('click', () => {
        hideConfirmModal();
        if (resolveConfirm) resolveConfirm(true);
    });

    backdrop.addEventListener('click', () => {
        hideConfirmModal();
        if (resolveConfirm) resolveConfirm(false);
    });

    // ===== TOAST NOTIFICATION =====
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const bgClass = type === 'success'
            ? 'bg-emerald-600 dark:bg-emerald-700'
            : 'bg-red-600 dark:bg-red-700';
        const icon = type === 'success'
            ? '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
            : '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

        toast.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-white text-sm font-medium ${bgClass} transform translate-x-full transition-transform duration-300`;
        toast.innerHTML = `${icon}<span>${message}</span>`;
        container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ===== HIỂN THỊ NÚT LƯU KHI THAY ĐỔI TỶ LỆ =====
    document.querySelectorAll('.rate-input').forEach(input => {
        input.addEventListener('input', function () {
            const barberId = this.dataset.barberId;
            const original = parseFloat(this.dataset.original);
            const current = parseFloat(this.value);
            const btn = document.querySelector(`.save-rate-btn[data-barber-id="${barberId}"]`);

            if (btn) {
                btn.classList.toggle('hidden', current === original);
            }
        });
    });

    // ===== LƯU TỶ LỆ TỪNG BARBER (AJAX + Modal) =====
    document.querySelectorAll('.save-rate-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const barberId = this.dataset.barberId;
            const input = document.querySelector(`.rate-input[data-barber-id="${barberId}"]`);
            const rate = parseFloat(input.value);
            const originalRate = parseFloat(input.dataset.original);

            if (isNaN(rate) || rate < 0 || rate > 100) {
                showToast('Tỷ lệ hoa hồng phải từ 0 đến 100%.', 'error');
                return;
            }

            // Lấy tên barber từ row
            const row = document.getElementById(`barber-row-${barberId}`);
            const barberName = row?.querySelector('p.font-medium')?.textContent?.trim() || 'thợ cắt';

            const confirmed = await showConfirmModal(
                'Cập nhật tỷ lệ hoa hồng',
                `Bạn có chắc muốn thay đổi tỷ lệ hoa hồng của ${barberName} từ ${originalRate}% sang ${rate}%?`
            );

            if (!confirmed) return;

            this.disabled = true;
            try {
                const response = await fetch(`/admin/commissions/${barberId}/rate`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ commission_rate: rate }),
                });

                const result = await response.json();
                if (result.success) {
                    input.dataset.original = rate;
                    this.classList.add('hidden');
                    // Flash effect
                    row.classList.add('bg-emerald-50', 'dark:bg-emerald-500/5');
                    setTimeout(() => row.classList.remove('bg-emerald-50', 'dark:bg-emerald-500/5'), 1500);
                    showToast(`Đã cập nhật tỷ lệ hoa hồng cho ${barberName} thành ${rate}%.`);
                } else {
                    showToast(result.message || 'Có lỗi xảy ra.', 'error');
                }
            } catch (err) {
                showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
                console.error(err);
            } finally {
                this.disabled = false;
            }
        });
    });

    // ===== CẬP NHẬT HÀNG LOẠT (form submit + Modal) =====
    document.getElementById('bulkForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const rate = parseFloat(document.getElementById('bulkRate').value);
        const target = document.getElementById('bulkTarget').value;
        const bulkMsgEl = document.getElementById('bulkMessage');
        const submitBtn = document.getElementById('bulkSubmitBtn');

        if (isNaN(rate) || rate < 0 || rate > 100) {
            bulkMsgEl.className = 'mt-3 text-sm p-3 rounded-lg bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400';
            bulkMsgEl.textContent = 'Tỷ lệ hoa hồng phải từ 0 đến 100%.';
            bulkMsgEl.classList.remove('hidden');
            return;
        }

        const targetLabel = target === 'all'
            ? 'TẤT CẢ thợ cắt'
            : document.querySelector(`#bulkTarget option[value="${target}"]`)?.textContent?.trim() || 'thợ cắt đã chọn';

        const confirmed = await showConfirmModal(
            'Cập nhật hàng loạt',
            `Bạn có chắc muốn áp dụng tỷ lệ hoa hồng ${rate}% cho ${targetLabel}? Thay đổi này sẽ ảnh hưởng đến các booking hoàn thành sau này.`
        );

        if (!confirmed) return;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang cập nhật...';

        const body = { commission_rate: rate };
        if (target !== 'all') {
            body.barber_ids = [parseInt(target)];
        }

        try {
            const response = await fetch('{{ route("admin.commissions.bulkUpdateRate") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });

            const result = await response.json();

            if (result.success) {
                bulkMsgEl.classList.add('hidden');
                showToast(result.message);

                // Cập nhật UI inline
                if (target === 'all') {
                    document.querySelectorAll('.rate-input').forEach(input => {
                        input.value = rate;
                        input.dataset.original = rate;
                    });
                    document.querySelectorAll('.save-rate-btn').forEach(btn => btn.classList.add('hidden'));
                } else {
                    const input = document.querySelector(`.rate-input[data-barber-id="${target}"]`);
                    if (input) {
                        input.value = rate;
                        input.dataset.original = rate;
                    }
                    const saveBtn = document.querySelector(`.save-rate-btn[data-barber-id="${target}"]`);
                    if (saveBtn) saveBtn.classList.add('hidden');
                }
            } else {
                bulkMsgEl.className = 'mt-3 text-sm p-3 rounded-lg bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400';
                bulkMsgEl.textContent = result.message || 'Có lỗi xảy ra.';
                bulkMsgEl.classList.remove('hidden');
            }
        } catch (err) {
            showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Cập nhật tỷ lệ';
        }
    });
});
</script>
@endpush

