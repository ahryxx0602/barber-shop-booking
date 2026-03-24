@extends('layouts.tailbarber')

@section('page', 'Booking')
@section('title', 'Lịch trực quan')

@section('content')
    {{-- Tab chuyển đổi chế độ xem --}}
    <div class="flex items-center gap-1 mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-1 w-fit">
        <a href="{{ route('barber.bookings.index') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Danh sách
        </a>
        <a href="{{ route('barber.bookings.calendar') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-sm font-medium bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Lịch trực quan
        </a>
    </div>

    {{-- Chú thích màu trạng thái --}}
    <div class="flex flex-wrap items-center gap-3 sm:gap-5 mb-4" style="font-size:12px">
        <span style="color:#6b7280;font-weight:500">Chú thích:</span>
        <span style="display:inline-flex;align-items:center;gap:6px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;flex-shrink:0"></span>
            Chờ xác nhận
        </span>
        <span style="display:inline-flex;align-items:center;gap:6px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3b82f6;flex-shrink:0"></span>
            Đã xác nhận
        </span>
        <span style="display:inline-flex;align-items:center;gap:6px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#8b5cf6;flex-shrink:0"></span>
            Đang phục vụ
        </span>
        <span style="display:inline-flex;align-items:center;gap:6px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;flex-shrink:0"></span>
            Hoàn thành
        </span>
        <span style="display:inline-flex;align-items:center;gap:6px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
            Đã hủy
        </span>
    </div>

    {{-- Calendar container --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <div id="calendar"></div>
    </div>

    {{-- Modal chi tiết booking (Alpine.js) --}}
    <div x-data="bookingModal()" x-cloak>
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" @click="close()"></div>

            {{-- Modal content --}}
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden
                        border border-gray-200 dark:border-gray-700 transform transition-all"
                 x-show="open"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="close()">

                {{-- Header với màu status --}}
                <div class="px-6 py-4" :style="'background-color:' + booking.color">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-xs font-medium tracking-wider uppercase" x-text="booking.status_label"></p>
                            <h3 class="text-white text-lg font-bold mt-0.5" x-text="booking.customer"></h3>
                        </div>
                        <button @click="close()" class="text-white/70 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    {{-- Booking code --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mã đặt lịch</p>
                            <p class="text-sm font-mono font-semibold text-gray-800 dark:text-white" x-text="booking.booking_code"></p>
                        </div>
                    </div>

                    {{-- Thời gian --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Thời gian</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                <span x-text="booking.start_time"></span> — <span x-text="booking.end_time"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Dịch vụ --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-7 7m7-7l-7-7"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Dịch vụ</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="booking.services"></p>
                        </div>
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="flex items-center gap-3" x-show="booking.phone">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Điện thoại</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="booking.phone"></p>
                        </div>
                    </div>

                    {{-- Tổng tiền --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tổng tiền</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" x-text="booking.total_price"></p>
                        </div>
                    </div>

                    {{-- Ghi chú --}}
                    <div x-show="booking.note" class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider">Ghi chú</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 italic" x-text="booking.note"></p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700">
                    <button @click="close()" class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* === FullCalendar Base === */
    .fc {
        --fc-border-color: rgb(229 231 235);
        --fc-today-bg-color: rgba(59, 130, 246, 0.05);
        --fc-neutral-bg-color: transparent;
        --fc-page-bg-color: transparent;
        --fc-list-event-hover-bg-color: rgba(59, 130, 246, 0.04);
    }
    .dark .fc {
        --fc-border-color: rgb(55 65 81);
        --fc-today-bg-color: rgba(59, 130, 246, 0.1);
        --fc-neutral-text-color: rgb(209 213 219);
        --fc-list-event-hover-bg-color: rgba(59, 130, 246, 0.08);
        color: rgb(209 213 219);
    }

    /* === Toolbar === */
    .fc .fc-toolbar-title {
        font-size: 1.125rem !important;
        font-weight: 700 !important;
    }
    .dark .fc .fc-toolbar-title { color: white; }

    .fc .fc-toolbar {
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
    }

    /* === Buttons === */
    .fc .fc-button {
        background-color: white !important;
        border: 1px solid rgb(209 213 219) !important;
        color: rgb(55 65 81) !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        padding: 0.375rem 0.75rem !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.05) !important;
        text-transform: capitalize !important;
    }
    .fc .fc-button:hover { background-color: rgb(249 250 251) !important; }
    .fc .fc-button-active, .fc .fc-button:active {
        background-color: rgb(59 130 246) !important;
        border-color: rgb(59 130 246) !important;
        color: white !important;
    }
    .dark .fc .fc-button {
        background-color: rgb(55 65 81) !important;
        border-color: rgb(75 85 99) !important;
        color: rgb(209 213 219) !important;
    }
    .dark .fc .fc-button:hover { background-color: rgb(75 85 99) !important; }
    .dark .fc .fc-button-active, .dark .fc .fc-button:active {
        background-color: rgb(59 130 246) !important;
        border-color: rgb(59 130 246) !important;
        color: white !important;
    }

    /* === dayGridMonth: Day header === */
    .fc .fc-col-header-cell {
        padding: 0.5rem 0 !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }
    .dark .fc .fc-col-header-cell-cushion { color: rgb(156 163 175); }

    /* === dayGridMonth: Event block === */
    .fc-dayGridMonth-view .fc-event {
        border-radius: 4px !important;
        padding: 3px 6px !important;
        font-size: 0.6875rem !important;
        font-weight: 500 !important;
        cursor: pointer !important;
        border-width: 0 0 0 3px !important;
        margin-bottom: 1px !important;
        line-height: 1.3 !important;
        transition: transform 0.1s ease, box-shadow 0.1s ease !important;
    }
    .fc-dayGridMonth-view .fc-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,.12) !important;
    }
    /* Custom month event content */
    .month-event {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow: hidden;
        white-space: nowrap;
    }
    .month-event-time {
        font-weight: 700;
        opacity: 0.9;
        flex-shrink: 0;
    }
    .month-event-name {
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }
    .fc .fc-daygrid-event-dot { display: none !important; }
    .fc .fc-daygrid-day-number {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        padding: 0.375rem 0.5rem !important;
    }
    .dark .fc .fc-daygrid-day-number { color: rgb(209 213 219); }
    .fc .fc-daygrid-more-link {
        font-size: 0.6875rem !important;
        font-weight: 600 !important;
        color: rgb(59 130 246) !important;
        padding: 1px 4px !important;
    }

    /* === List views: Header (ngày) === */
    .fc .fc-list-day-cushion {
        background: rgb(249 250 251) !important;
        padding: 0.625rem 1rem !important;
    }
    .dark .fc .fc-list-day-cushion {
        background: rgb(31 41 55) !important;
    }
    .fc .fc-list-day-text,
    .fc .fc-list-day-side-text {
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        color: rgb(31 41 55) !important;
    }
    .dark .fc .fc-list-day-text,
    .dark .fc .fc-list-day-side-text {
        color: rgb(229 231 235) !important;
    }

    /* === List views: Event row === */
    .fc .fc-list-event {
        cursor: pointer !important;
    }
    .fc .fc-list-event td {
        padding: 0.5rem 0.75rem !important;
        border-color: rgb(243 244 246) !important;
        vertical-align: middle !important;
    }
    .dark .fc .fc-list-event td {
        border-color: rgb(55 65 81) !important;
    }
    .fc .fc-list-event:hover td {
        background-color: rgb(249 250 251) !important;
    }
    .dark .fc .fc-list-event:hover td {
        background-color: rgb(55 65 81 / 0.5) !important;
    }

    /* === List views: Event dot === */
    .fc .fc-list-event-dot {
        width: 10px !important;
        height: 10px !important;
        border-radius: 4px !important;
        border-width: 0 !important;
    }

    /* === List views: Time column === */
    .fc .fc-list-event-time {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: rgb(55 65 81) !important;
        white-space: nowrap !important;
        min-width: 100px !important;
    }
    .dark .fc .fc-list-event-time {
        color: rgb(209 213 219) !important;
    }

    /* === List views: Custom event content === */
    .fc-list-event-title .event-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .fc-list-event-title .event-customer {
        font-weight: 600;
        font-size: 0.875rem;
        color: rgb(17 24 39);
        min-width: 140px;
    }
    .dark .fc-list-event-title .event-customer {
        color: rgb(243 244 246);
    }
    .fc-list-event-title .event-services {
        font-size: 0.75rem;
        color: rgb(107 114 128);
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dark .fc-list-event-title .event-services {
        color: rgb(156 163 175);
    }
    .fc-list-event-title .event-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .fc-list-event-title .event-price {
        font-size: 0.8125rem;
        font-weight: 700;
        color: rgb(17 24 39);
        white-space: nowrap;
        text-align: right;
        min-width: 90px;
        flex-shrink: 0;
    }
    .dark .fc-list-event-title .event-price {
        color: rgb(243 244 246);
    }

    /* === List views: No events === */
    .fc .fc-list-empty-cushion {
        font-size: 0.875rem !important;
        color: rgb(156 163 175) !important;
        padding: 2rem !important;
    }

    /* === Scrollbar === */
    .fc .fc-scroller::-webkit-scrollbar { width: 6px; }
    .fc .fc-scroller::-webkit-scrollbar-thumb { background: rgb(209 213 219); border-radius: 3px; }
    .dark .fc .fc-scroller::-webkit-scrollbar-thumb { background: rgb(75 85 99); }

    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
{{-- FullCalendar v6 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    // Map màu status -> badge styles
    const STATUS_STYLES = {
        pending:     { bg: '#fef3c7', text: '#92400e', dot: '#f59e0b' },
        confirmed:   { bg: '#dbeafe', text: '#1e40af', dot: '#3b82f6' },
        in_progress: { bg: '#ede9fe', text: '#5b21b6', dot: '#8b5cf6' },
        completed:   { bg: '#d1fae5', text: '#065f46', dot: '#10b981' },
        cancelled:   { bg: '#fee2e2', text: '#991b1b', dot: '#ef4444' },
    };

    // Alpine.js component cho modal chi tiết booking
    function bookingModal() {
        return {
            open: false,
            booking: {
                booking_code: '', customer: '', phone: '', services: '',
                status: '', status_label: '', total_price: '', note: '',
                start_time: '', end_time: '', color: '#6b7280'
            },
            show(event) {
                const props = event.extendedProps;
                this.booking = {
                    booking_code: props.booking_code,
                    customer: props.customer,
                    phone: props.phone,
                    services: props.services,
                    status: props.status,
                    status_label: props.status_label,
                    total_price: props.total_price,
                    note: props.note,
                    start_time: props.start_time,
                    end_time: props.end_time,
                    color: event.backgroundColor
                };
                this.open = true;
            },
            close() { this.open = false; }
        };
    }

    // Khởi tạo FullCalendar
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            firstDay: 1,
            height: 'auto',
            navLinks: true,
            editable: false,
            dayMaxEvents: 4,
            moreLinkClick: 'day',

            // Header toolbar
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek,listDay'
            },

            // Nút text tiếng Việt
            buttonText: {
                today: 'Hôm nay',
                month: 'Tháng',
                listWeek: 'Tuần',
                listDay: 'Ngày'
            },

            // Hiện "Không có lịch hẹn" khi list rỗng
            noEventsText: 'Không có lịch hẹn nào',

            // Nguồn dữ liệu events
            events: {
                url: '{{ route("barber.bookings.events") }}',
                method: 'GET',
                failure: function () {
                    alert('Không thể tải dữ liệu lịch hẹn!');
                }
            },

            // Custom render event content
            eventContent: function (arg) {
                const view = arg.view.type;
                const props = arg.event.extendedProps;

                // List views: hiển thị đầy đủ thông tin
                if (view === 'listWeek' || view === 'listDay') {
                    const style = STATUS_STYLES[props.status] || STATUS_STYLES.pending;
                    return { html: `
                        <div class="event-content">
                            <span class="event-customer">${props.customer}</span>
                            <span class="event-services">${props.services}</span>
                            <span class="event-badge" style="background:${style.bg};color:${style.text}">
                                ${props.status_label}
                            </span>
                            <span class="event-price">${props.total_price}</span>
                        </div>
                    ` };
                }

                // dayGridMonth: hiện giờ + tên khách
                if (view === 'dayGridMonth') {
                    return { html: `
                        <div class="month-event">
                            <span class="month-event-time">${props.start_time}</span>
                            <span class="month-event-name">${props.customer}</span>
                        </div>
                    ` };
                }

                return null;
            },

            // Click vào event -> mở modal
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                const modal = document.querySelector('[x-data="bookingModal()"]');
                if (modal) {
                    const data = Alpine.$data(modal);
                    data.show(info.event);
                }
            },

            // Tooltip khi hover (cho month view)
            eventDidMount: function (info) {
                const props = info.event.extendedProps;
                info.el.title = `${props.customer}\n${props.start_time} - ${props.end_time}\n${props.services}\n${props.total_price}`;
            }
        });

        calendar.render();
    });
</script>
@endpush
