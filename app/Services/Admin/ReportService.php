<?php

namespace App\Services\Admin;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Lấy thống kê tổng quan theo tháng.
     * Hỗ trợ filter theo branch_id (qua barber).
     */
    public function getMonthlyOverview(?Carbon $date = null, ?int $branchId = null): array
    {
        $date = $date ?? now();

        $currentMonthStart = $date->copy()->startOfMonth();
        $currentMonthEnd = $date->copy()->endOfMonth();
        $prevMonthStart = $date->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $date->copy()->subMonth()->endOfMonth();

        // Base query builder cho booking (có thể filter theo branch)
        $bookingQuery = fn () => $branchId
            ? Booking::whereHas('barber', fn ($q) => $q->where('branch_id', $branchId))
            : Booking::query();

        // Tổng booking
        $currentBookings = $bookingQuery()->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $prevBookings = $bookingQuery()->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        // Doanh thu (chỉ tính booking confirmed, in_progress, completed)
        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $currentRevenue = $bookingQuery()
            ->whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_price');

        $prevRevenue = $bookingQuery()
            ->whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_price');

        // Khách hàng mới (không filter theo branch vì khách không thuộc branch)
        $currentNewCustomers = User::where('role', UserRole::Customer)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $prevNewCustomers = User::where('role', UserRole::Customer)
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->count();

        return [
            'month' => $date->format('m/Y'),
            'bookings' => [
                'total' => $currentBookings,
                'change' => $this->calculateChange($currentBookings, $prevBookings),
            ],
            'revenue' => [
                'total' => $currentRevenue,
                'change' => $this->calculateChange($currentRevenue, $prevRevenue),
            ],
            'newCustomers' => [
                'total' => $currentNewCustomers,
                'change' => $this->calculateChange($currentNewCustomers, $prevNewCustomers),
            ],
        ];
    }

    /**
     * Lấy doanh thu theo ngày. Hỗ trợ filter branch.
     */
    public function getDailyRevenue(?int $month = null, ?int $year = null, ?int $branchId = null): array
    {
        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $query = Booking::whereIn('status', $revenueStatuses)
            ->whereBetween('booking_date', [$startDate, $endDate]);

        if ($branchId) {
            $query->whereHas('barber', fn ($q) => $q->where('branch_id', $branchId));
        }

        $revenues = $query
            ->selectRaw('DATE(booking_date) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');
            $data[] = (float) ($revenues[$dateKey] ?? 0);
            $current->addDay();
        }

        return compact('labels', 'data');
    }

    /**
     * Lấy doanh thu theo từng tháng trong năm. Hỗ trợ filter branch.
     */
    public function getMonthlyRevenue(int $year, ?int $branchId = null): array
    {
        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $query = Booking::whereIn('status', $revenueStatuses)
            ->whereYear('booking_date', $year);

        if ($branchId) {
            $query->whereHas('barber', fn ($q) => $q->where('branch_id', $branchId));
        }

        $revenues = $query
            ->selectRaw('MONTH(booking_date) as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = 'T' . $m;
            $data[] = (float) ($revenues[$m] ?? 0);
        }

        return compact('labels', 'data');
    }

    /**
     * Lấy danh sách các năm có booking.
     */
    public function getAvailableYears(): array
    {
        $years = Booking::selectRaw('DISTINCT YEAR(booking_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentYear = (int) now()->format('Y');
        if (!in_array($currentYear, $years)) {
            array_unshift($years, $currentYear);
        }

        return $years;
    }

    /**
     * Top thợ theo doanh thu (tháng hiện tại). Hỗ trợ filter branch.
     */
    public function getTopBarbers(int $limit = 5, ?int $branchId = null): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $query = Barber::select('barbers.*')
            ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->leftJoin('bookings', function ($join) use ($startOfMonth, $endOfMonth, $revenueStatuses) {
                $join->on('barbers.id', '=', 'bookings.barber_id')
                    ->whereBetween('bookings.booking_date', [$startOfMonth, $endOfMonth])
                    ->whereIn('bookings.status', $revenueStatuses);
            })
            ->with('user:id,name,avatar', 'branch:id,name');

        if ($branchId) {
            $query->where('barbers.branch_id', $branchId);
        }

        return $query
            ->groupBy('barbers.id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($barber) => [
                'name' => $barber->user->name,
                'avatar' => $barber->user->avatar,
                'branch' => $barber->branch?->name,
                'revenue' => (float) $barber->total_revenue,
                'bookings' => (int) $barber->total_bookings,
                'rating' => (float) $barber->rating,
            ])
            ->toArray();
    }

    /**
     * Top dịch vụ theo số lần đặt (tháng hiện tại). Hỗ trợ filter branch.
     */
    public function getTopServices(int $limit = 5, ?int $branchId = null): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $query = Service::select('services.id', 'services.name', 'services.price', 'services.image')
            ->selectRaw('COUNT(booking_services.id) as times_booked')
            ->selectRaw('COALESCE(SUM(booking_services.price_snapshot), 0) as total_revenue')
            ->leftJoin('booking_services', 'services.id', '=', 'booking_services.service_id')
            ->leftJoin('bookings', function ($join) use ($startOfMonth, $endOfMonth) {
                $join->on('booking_services.booking_id', '=', 'bookings.id')
                    ->whereBetween('bookings.booking_date', [$startOfMonth, $endOfMonth]);
            });

        if ($branchId) {
            $query->leftJoin('barbers', 'bookings.barber_id', '=', 'barbers.id')
                ->where('barbers.branch_id', $branchId);
        }

        return $query
            ->groupBy('services.id', 'services.name', 'services.price', 'services.image')
            ->orderByDesc('times_booked')
            ->limit($limit)
            ->get()
            ->map(fn ($service) => [
                'name' => $service->name,
                'price' => (float) $service->price,
                'image' => $service->image,
                'times_booked' => (int) $service->times_booked,
                'revenue' => (float) $service->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Lấy thống kê tổng quan sản phẩm theo tháng.
     */
    public function getProductMonthlyOverview(?Carbon $date = null): array
    {
        $date = $date ?? now();

        $currentMonthStart = $date->copy()->startOfMonth();
        $currentMonthEnd = $date->copy()->endOfMonth();
        $prevMonthStart = $date->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $date->copy()->subMonth()->endOfMonth();

        // Tổng đơn hàng (phát sinh)
        $currentOrders = \App\Models\Order::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
        $prevOrders = \App\Models\Order::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        // Doanh thu (chỉ tính đơn hàng Confirmed, Shipping, Delivered)
        $revenueStatuses = [
            \App\Enums\OrderStatus::Confirmed,
            \App\Enums\OrderStatus::Shipping,
            \App\Enums\OrderStatus::Delivered,
        ];

        $currentRevenue = \App\Models\Order::whereIn('status', $revenueStatuses)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');

        $prevRevenue = \App\Models\Order::whereIn('status', $revenueStatuses)
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_amount');

        // Tổng số sản phẩm đang bán
        $totalProducts = \App\Models\Product::where('is_active', true)->count();

        return [
            'month' => $date->format('m/Y'),
            'orders' => [
                'total' => $currentOrders,
                'change' => $this->calculateChange($currentOrders, $prevOrders),
            ],
            'revenue' => [
                'total' => $currentRevenue,
                'change' => $this->calculateChange($currentRevenue, $prevRevenue),
            ],
            'products' => [
                'total' => $totalProducts,
            ],
        ];
    }

    /**
     * Lấy doanh thu sản phẩm theo ngày (biểu đồ).
     */
    public function getProductDailyRevenue(?int $month = null, ?int $year = null): array
    {
        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $revenueStatuses = [
            \App\Enums\OrderStatus::Confirmed,
            \App\Enums\OrderStatus::Shipping,
            \App\Enums\OrderStatus::Delivered,
        ];

        $revenues = \App\Models\Order::whereIn('status', $revenueStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');
            $data[] = (float) ($revenues[$dateKey] ?? 0);
            $current->addDay();
        }

        return compact('labels', 'data');
    }

    /**
     * Lấy doanh thu sản phẩm theo từng tháng trong năm.
     */
    public function getProductMonthlyRevenue(int $year): array
    {
        $revenueStatuses = [
            \App\Enums\OrderStatus::Confirmed,
            \App\Enums\OrderStatus::Shipping,
            \App\Enums\OrderStatus::Delivered,
        ];

        $revenues = \App\Models\Order::whereIn('status', $revenueStatuses)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = 'T' . $m;
            $data[] = (float) ($revenues[$m] ?? 0);
        }

        return compact('labels', 'data');
    }

    /**
     * Top sản phẩm bán chạy.
     */
    public function getTopSellingProducts(int $limit = 5): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $revenueStatuses = [
            \App\Enums\OrderStatus::Confirmed,
            \App\Enums\OrderStatus::Shipping,
            \App\Enums\OrderStatus::Delivered,
        ];

        return \App\Models\Product::select('products.id', 'products.name', 'products.price', 'products.image')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->selectRaw('COALESCE(SUM(order_items.total_price), 0) as total_revenue')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function ($join) use ($startOfMonth, $endOfMonth, $revenueStatuses) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                    ->whereIn('orders.status', $revenueStatuses);
            })
            ->groupBy('products.id', 'products.name', 'products.price', 'products.image')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'price' => (float) $item->price,
                'image' => $item->image,
                'sold' => (int) $item->total_sold,
                'revenue' => (float) $item->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Tính % thay đổi so với tháng trước.
     */
    private function calculateChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }

    /**
     * Dữ liệu bản đồ nhiệt cho Booking.
     */
    public function getBookingHeatmapData(?int $branchId = null): array
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $revenueStatuses = [
            BookingStatus::Confirmed,
            BookingStatus::InProgress,
            BookingStatus::Completed,
        ];

        $query = Booking::join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.id')
            ->whereIn('bookings.status', $revenueStatuses)
            ->whereBetween('bookings.booking_date', [$startDate, $endDate]);

        if ($branchId) {
            // Need to join barbers or use whereHas appropriately
            $query->whereHas('barber', fn ($q) => $q->where('branch_id', $branchId));
        }

        $results = $query->selectRaw('DAYOFWEEK(bookings.booking_date) as day_of_week, HOUR(time_slots.start_time) as hour_of_day, COUNT(bookings.id) as total')
            ->groupBy('day_of_week', 'hour_of_day')
            ->get();

        return $this->formatHeatmapData($results);
    }

    /**
     * Dữ liệu bản đồ nhiệt cho Đơn hàng.
     */
    public function getProductHeatmapData(): array
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $revenueStatuses = [
            \App\Enums\OrderStatus::Confirmed,
            \App\Enums\OrderStatus::Shipping,
            \App\Enums\OrderStatus::Delivered,
        ];

        $results = \App\Models\Order::whereIn('status', $revenueStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, HOUR(created_at) as hour_of_day, COUNT(id) as total')
            ->groupBy('day_of_week', 'hour_of_day')
            ->get();

        return $this->formatHeatmapData($results, true);
    }

    private function formatHeatmapData($results, bool $isOrder = false): array
    {
        $days = [
            1 => 'Chủ nhật',
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
        ];

        // Frame from 8h to 20h for booking, 7h to 22h for order
        $startHour = $isOrder ? 7 : 8;
        $endHour = $isOrder ? 22 : 20;
        $hoursRange = range($startHour, $endHour);

        $data = [];
        foreach ($days as $dayNum => $dayName) {
            $dayData = [];
            foreach ($hoursRange as $h) {
                $record = $results->first(fn ($r) => $r->day_of_week == $dayNum && $r->hour_of_day == $h);
                $dayData[] = [
                    'x' => sprintf('%02d:00', $h),
                    'y' => $record ? (int)$record->total : 0
                ];
            }
            $data[] = [
                'name' => $dayName,
                'data' => $dayData
            ];
        }

        // ApexCharts puts the first array item at the top of the Y axis, 
        // to order from Monday to Sunday, we should reverse or just build it carefully.
        // Actually top to bottom: T2 -> CN. So let's re-order the $data array
        $orderedData = [];
        $orderKeys = [2, 3, 4, 5, 6, 7, 1]; // Monday to Sunday
        foreach ($orderKeys as $key) {
            // Find in $data
            $found = collect($data)->firstWhere('name', $days[$key]);
            if ($found) {
                $orderedData[] = $found;
            }
        }

        return $orderedData;
    }
}
