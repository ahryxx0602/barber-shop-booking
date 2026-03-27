<?php

namespace App\Services\Barber;

use App\DTOs\Barber\CreateBookingData;
use App\Enums\BookingStatus;
use App\Enums\RecurringFrequency;
use App\Enums\TimeSlotStatus;
use App\Enums\UserRole;
use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Exceptions\SlotNotAvailableException;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Repositories\Contracts\Barber\BookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected CouponService $couponService,
        protected BookingRepositoryInterface $bookingRepo,
    ) {}

    public function create(CreateBookingData $data, ?User $customer = null): Booking
    {
        return DB::transaction(function () use ($data, $customer) {
            // Ai đến trước → giữ khóa, người sau phải CHỜ 
            // SELECT * FROM time_slots WHERE id = 5 FOR UPDATE;
            $slot = $this->bookingRepo->findSlotForUpdate($data->time_slot_id);

            if ($slot->status !== TimeSlotStatus::Available) {
                throw new SlotNotAvailableException('Slot này vừa được đặt, vui lòng chọn lại.');
            }

            if (!$customer) {
                $customer = $this->findOrCreateGuest($data);
            }

            $services = Service::whereIn('id', $data->service_ids)->get();
            $totalPrice = $services->sum('price');
            $totalDuration = $services->sum('duration_minutes');
            $endTime = Carbon::parse($slot->start_time)->addMinutes($totalDuration)->format('H:i:s');

            // Xử lý coupon nếu có
            $discountAmount = 0;
            $couponCode = null;
            if ($data->coupon_code) {
                try {
                    $coupon = $this->couponService->validate($data->coupon_code, $totalPrice);
                    $discountAmount = $this->couponService->calculateDiscount($coupon, $totalPrice);
                    $couponCode = $coupon->code;
                    $this->couponService->markUsed($coupon);
                } catch (\InvalidArgumentException $e) {
                    // M1: Thông báo cho user thay vì nuốt im lặng
                    session()->flash('warning', 'Mã giảm giá không hợp lệ: ' . $e->getMessage());
                }
            }

            $booking = $this->bookingRepo->create([
                'booking_code' => $this->generateCode(),
                'customer_id' => $customer->id,
                'barber_id' => $data->barber_id,
                'time_slot_id' => $slot->id,
                'booking_date' => $slot->slot_date,
                'start_time' => $slot->start_time,
                'end_time' => $endTime,
                'total_price' => $totalPrice - $discountAmount,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'note' => $data->note,
                'status' => BookingStatus::Pending,
            ]);

            foreach ($services as $service) {
                $booking->services()->attach($service->id, [
                    'price_snapshot' => $service->price,
                    'duration_snapshot' => $service->duration_minutes,
                ]);
            }

            $this->bookingRepo->updateSlotStatus($slot->id, TimeSlotStatus::Booked);

            Log::channel('booking')->info('Booking created', [
                'booking_code' => $booking->booking_code,
                'customer_id' => $customer->id,
                'barber_id' => $data->barber_id,
                'total_price' => $totalPrice - $discountAmount,
                'discount' => $discountAmount,
            ]);

            // Gửi thông báo cho thợ cắt
            $barber = \App\Models\Barber::find($data->barber_id);
            if ($barber && $barber->user_id) {
                $message = "Bạn có lịch hẹn mới #{$booking->booking_code} từ khách hàng {$customer->name} "
                         . "vào lúc {$booking->start_time} ngày {$booking->booking_date->format('d/m/Y')}.";

                \App\Jobs\SendBookingNotificationJob::dispatch(
                    $barber->user_id,
                    $message,
                    'Có người đặt lịch mới',
                    'new_booking'
                );
            }

            return $booking;
        });
    }

    /**
     * Đặt lịch lặp lại (recurring) theo tần suất.
     * Tạo booking gốc + tối đa 3 booking tiếp theo ở các tuần/tháng tiếp theo.
     * Bỏ qua slot không khả dụng thay vì throw.
     *
     * @return array<Booking> danh sách booking đã tạo
     */
    public function createRecurring(CreateBookingData $data, User $customer): array
    {
        $frequency = RecurringFrequency::tryFrom($data->recurring_frequency) ?? RecurringFrequency::None;

        // Luôn tạo booking gốc
        $firstBooking = $this->create($data, $customer);
        $bookings = [$firstBooking];

        if ($frequency === RecurringFrequency::None) {
            return $bookings;
        }

        $baseSlot = $this->bookingRepo->findSlotForUpdate($data->time_slot_id);
        $interval = $frequency->daysInterval();

        // Tạo tối đa 3 booking lặp tiếp theo
        for ($i = 1; $i <= 3; $i++) {
            $nextDate = Carbon::parse($baseSlot->slot_date)->addDays($interval * $i);

            // Tìm slot cùng giờ, cùng barber, ngày tiếp theo
            $nextSlot = $this->bookingRepo->findAvailableRecurringSlot(
                $data->barber_id,
                $nextDate->format('Y-m-d'),
                $baseSlot->start_time
            );

            if (!$nextSlot) {
                Log::channel('booking')->info('Recurring booking skipped', [
                    'date' => $nextDate->format('Y-m-d'),
                    'barber_id' => $data->barber_id,
                    'reason' => 'Slot not available',
                ]);
                continue; // Bỏ qua nếu slot không khả dụng
            }

            try {
                $recurData = new CreateBookingData(
                    barber_id: $data->barber_id,
                    time_slot_id: $nextSlot->id,
                    service_ids: $data->service_ids,
                    note: $data->note ? $data->note . ' (lặp lại)' : 'Lịch lặp lại',
                );
                $bookings[] = $this->create($recurData, $customer);
            } catch (\Exception $e) {
                Log::channel('booking')->warning('Recurring booking failed', [
                    'date' => $nextDate->format('Y-m-d'),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $bookings;
    }

    protected function findOrCreateGuest(CreateBookingData $data): User
    {
        return User::firstOrCreate(
            ['email' => $data->guest_email],
            [
                'name' => $data->guest_name,
                'phone' => $data->guest_phone,
                'password' => bcrypt(Str::random(32)),
                'role' => UserRole::Customer,
            ]
        );
    }

    public function confirm(Booking $booking): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Confirmed)) {
            throw new \InvalidArgumentException('Không thể xác nhận booking ở trạng thái: ' . $booking->status->label());
        }

        $booking->update(['status' => BookingStatus::Confirmed]);

        Log::channel('booking')->info('Booking confirmed', [
            'booking_code' => $booking->booking_code,
        ]);

        event(new BookingConfirmed($booking));

        return $booking;
    }

    public function reject(Booking $booking, ?string $reason = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Cancelled)) {
            throw new \InvalidArgumentException('Không thể từ chối booking ở trạng thái: ' . $booking->status->label());
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason ?? 'Thợ từ chối lịch hẹn',
            ]);

            $this->reopenSlot($booking);

            Log::channel('booking')->info('Booking rejected', [
                'booking_code' => $booking->booking_code,
                'reason' => $reason,
            ]);

            event(new BookingCancelled($booking));

            return $booking;
        });
    }

    public function start(Booking $booking): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::InProgress)) {
            throw new \InvalidArgumentException('Không thể bắt đầu phục vụ booking ở trạng thái: ' . $booking->status->label());
        }

        $booking->update(['status' => BookingStatus::InProgress]);

        Log::channel('booking')->info('Booking started', [
            'booking_code' => $booking->booking_code,
        ]);

        return $booking;
    }

    public function complete(Booking $booking): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Completed)) {
            throw new \InvalidArgumentException('Không thể hoàn thành booking ở trạng thái: ' . $booking->status->label());
        }

        $booking->update(['status' => BookingStatus::Completed]);

        Log::channel('booking')->info('Booking completed', [
            'booking_code' => $booking->booking_code,
        ]);

        event(new BookingCompleted($booking));

        return $booking;
    }

    public function cancel(Booking $booking, ?string $reason = null): Booking
    {
        if (!$booking->status->canTransitionTo(BookingStatus::Cancelled)) {
            throw new \InvalidArgumentException('Không thể huỷ booking ở trạng thái: ' . $booking->status->label());
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason ?? 'Khách hàng huỷ lịch',
            ]);

            $this->reopenSlot($booking);

            Log::channel('booking')->info('Booking cancelled', [
                'booking_code' => $booking->booking_code,
                'reason' => $reason,
            ]);

            event(new BookingCancelled($booking));

            return $booking;
        });
    }

    protected function reopenSlot(Booking $booking): void
    {
        if ($booking->time_slot_id) {
            $this->bookingRepo->updateSlotStatus($booking->time_slot_id, TimeSlotStatus::Available);
        }
    }

    protected function generateCode(): string
    {
        return 'BB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
