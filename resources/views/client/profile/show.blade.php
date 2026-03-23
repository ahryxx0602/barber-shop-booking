@use('App\Enums\BookingStatus')

@extends('layouts.client')

@section('title', 'Thông tin cá nhân')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;padding:48px 16px;" class="sm:px-6 lg:px-8">
    <div style="max-width:900px;margin:0 auto;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
            <div style="display:flex;align-items:center;">
                <a href="{{ url('/') }}" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;color:var(--v-ink);margin-right:16px;border:1px solid var(--v-rule);transition:border-color 0.2s;"
                    onmouseover="this.style.borderColor='var(--v-copper)'" onmouseout="this.style.borderColor='var(--v-rule)'">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="v-title-sm">Thông Tin Cá Nhân</h1>
            </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="{{ route('client.profile.loyalty') }}" class="v-btn-outline v-btn-sm" style="gap:8px;">
                <span class="material-symbols-outlined" style="font-size:14px;">stars</span>
                Điểm thưởng
            </a>
            <a href="{{ route('client.profile.edit') }}" class="v-btn-outline v-btn-sm" style="gap:8px;">
                <span class="material-symbols-outlined" style="font-size:14px;">edit</span>
                Chỉnh sửa
            </a>
        </div>

        @if(session('success'))
            <div style="margin-bottom:24px;padding:16px;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);color:var(--v-copper-dk);font-size:14px;font-weight:500;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Profile Card --}}
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);margin-bottom:32px;">
            <div style="padding:24px 24px;" class="sm:p-8">
                <div style="display:flex;align-items:flex-start;gap:24px;">
                    {{-- Avatar --}}
                    <div style="width:80px;height:80px;background:var(--v-surface);flex-shrink:0;overflow:hidden;position:relative;border:1px solid var(--v-rule);">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <span class="material-symbols-outlined" style="font-size:32px;color:var(--v-muted);">person</span>
                            </div>
                        @endif
                        {{-- Mini corner accents --}}
                        <div style="position:absolute;top:0;left:0;width:10px;height:10px;border-top:1.5px solid var(--v-copper);border-left:1.5px solid var(--v-copper);"></div>
                        <div style="position:absolute;bottom:0;right:0;width:10px;height:10px;border-bottom:1.5px solid var(--v-copper);border-right:1.5px solid var(--v-copper);"></div>
                    </div>
                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <h2 style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);margin-bottom:4px;">{{ $user->name }}</h2>
                        <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:14px;">
                                <span class="material-symbols-outlined" style="font-size:16px;color:var(--v-muted);">mail</span>
                                <span style="color:var(--v-ink);">{{ $user->email }}</span>
                            </div>
                            @if($user->phone)
                            <div style="display:flex;align-items:center;gap:8px;font-size:14px;">
                                <span class="material-symbols-outlined" style="font-size:16px;color:var(--v-muted);">phone</span>
                                <span style="color:var(--v-ink);">{{ $user->phone }}</span>
                            </div>
                            @endif
                            <div style="display:flex;align-items:center;gap:8px;font-size:14px;">
                                <span class="material-symbols-outlined" style="font-size:16px;color:var(--v-muted);">calendar_month</span>
                                <span style="color:var(--v-muted);">Thành viên từ {{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Bookings --}}
        <div style="margin-bottom:32px;">
            <div class="v-ornament" style="max-width:360px;margin-bottom:16px;justify-content:flex-start;">
                Lịch hẹn sắp tới
            </div>

            @if($upcomingBookings->isEmpty())
                <div style="border:1px solid var(--v-rule);background:#fff;padding:24px;text-align:center;">
                    <span class="material-symbols-outlined" style="font-size:32px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:6px;">calendar_today</span>
                    <p style="color:var(--v-muted);font-size:13px;">Chưa có lịch hẹn nào.</p>
                    <a href="{{ route('client.booking.create') }}" class="v-btn-primary v-btn-sm" style="margin-top:12px;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                        Đặt lịch ngay
                    </a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($upcomingBookings as $booking)
                        <div style="border:1px solid var(--v-rule);background:#fff;padding:12px 16px;box-shadow:2px 2px 0 var(--v-copper);">
                            <div style="display:flex;align-items:center;gap:12px;">
                                {{-- Date badge compact --}}
                                <div style="flex-shrink:0;width:40px;height:40px;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                    <span style="font-family:var(--font-serif);font-size:15px;font-weight:700;color:var(--v-copper);line-height:1;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                                    <span style="font-size:8px;letter-spacing:1px;text-transform:uppercase;color:var(--v-copper);">Thg{{ \Carbon\Carbon::parse($booking->booking_date)->format('m') }}</span>
                                </div>
                                {{-- Main info --}}
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                        <span style="font-size:13px;font-weight:600;color:var(--v-ink);">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                        <span style="font-size:12px;color:var(--v-muted);">·</span>
                                        <span style="font-size:12px;font-weight:500;color:var(--v-ink);">{{ $booking->barber->user->name }}</span>
                                        <span style="display:inline-flex;padding:1px 6px;font-size:8px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
                                            @if($booking->status === BookingStatus::Confirmed) background:rgba(22,163,74,0.06);color:#15803d;border:1px solid rgba(22,163,74,0.2);
                                            @elseif($booking->status === BookingStatus::Pending) background:rgba(202,138,4,0.06);color:#a16207;border:1px solid rgba(202,138,4,0.2);
                                            @elseif($booking->status === BookingStatus::InProgress) background:rgba(147,51,234,0.06);color:#7e22ce;border:1px solid rgba(147,51,234,0.2);
                                            @else background:var(--v-surface);color:var(--v-muted);border:1px solid var(--v-rule);
                                            @endif">{{ $booking->status->label() }}</span>
                                    </div>
                                    <p style="font-size:11px;color:var(--v-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $booking->services->pluck('name')->join(', ') }}</p>
                                </div>
                                {{-- Price --}}
                                <div style="flex-shrink:0;text-align:right;">
                                    <span style="font-family:var(--font-serif);font-size:13px;font-weight:700;color:var(--v-ink);">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                                    <div style="font-size:9px;color:var(--v-muted);font-family:monospace;">{{ $booking->booking_code }}</div>
                                </div>
                            </div>
                            {{-- Cancel (inline) --}}
                            @if(in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed]))
                                @php
                                    $appointmentTime = \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
                                    $canCancel = now()->diffInMinutes($appointmentTime, false) >= 120;
                                @endphp
                                <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--v-rule);display:flex;align-items:center;">
                                    @if($canCancel)
                                        <div x-data="{ open: false }" style="position:relative;">
                                            <button @click="open = !open" type="button"
                                                style="display:flex;align-items:center;gap:4px;font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#dc2626;background:none;border:none;cursor:pointer;transition:color 0.2s;"
                                                onmouseover="this.style.color='#991b1b'" onmouseout="this.style.color='#dc2626'">
                                                <span class="material-symbols-outlined" style="font-size:12px;">cancel</span>
                                                Huỷ lịch
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition
                                                style="position:absolute;z-index:10;margin-top:6px;left:0;width:260px;border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);padding:14px;">
                                                <form method="POST" action="{{ route('client.booking.cancel', $booking) }}">
                                                    @csrf @method('PATCH')
                                                    <label style="display:block;font-size:12px;font-weight:500;color:var(--v-ink);margin-bottom:6px;">Lý do huỷ</label>
                                                    <textarea name="cancel_reason" rows="2" class="v-textarea" style="font-size:12px;" placeholder="Nhập lý do..."></textarea>
                                                    <button type="submit" style="margin-top:6px;width:100%;padding:7px;background:#dc2626;color:#fff;border:none;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:background 0.2s;"
                                                        onmouseover="this.style.background='#991b1b'" onmouseout="this.style.background='#dc2626'">
                                                        Xác nhận huỷ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <p style="font-size:10px;color:var(--v-muted);font-style:italic;">Không thể huỷ trong vòng 2h trước giờ hẹn.</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Past Bookings --}}
        <div>
            <div class="v-ornament" style="max-width:360px;margin-bottom:16px;justify-content:flex-start;">
                Lịch sử đặt lịch
            </div>

            @if($pastBookings->isEmpty())
                <div style="border:1px solid var(--v-rule);background:#fff;padding:24px;text-align:center;">
                    <p style="color:var(--v-muted);font-size:13px;">Chưa có lịch sử đặt lịch.</p>
                </div>
            @else
                <div style="border:1px solid var(--v-rule);background:#fff;overflow:hidden;" x-data="{ showAll: false }">
                    {{-- Table header --}}
                    <div style="display:grid;grid-template-columns:60px 1fr 80px 70px;gap:0;padding:8px 16px;background:var(--v-surface);border-bottom:1px solid var(--v-rule);">
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Ngày</span>
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Chi tiết</span>
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:right;">Giá</span>
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:right;">T.Thái</span>
                    </div>
                    {{-- Rows --}}
                    @foreach($pastBookings->values() as $index => $booking)
                        <div x-show="showAll || {{ $index }} < 5" x-transition
                            style="border-bottom:1px solid var(--v-rule);">
                            {{-- Main row --}}
                            <div style="display:grid;grid-template-columns:60px 1fr 80px 70px;gap:0;padding:10px 16px;align-items:center;">
                                <span style="font-size:12px;font-weight:600;color:var(--v-ink);">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m') }}</span>
                                <div style="min-width:0;">
                                    <div style="font-size:12px;color:var(--v-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        <span style="font-weight:500;">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</span>
                                        <span style="color:var(--v-muted);margin:0 2px;">·</span>
                                        <span>{{ $booking->barber->user->name }}</span>
                                        <span style="color:var(--v-muted);margin:0 2px;">·</span>
                                        <span style="color:var(--v-muted);">{{ $booking->services->pluck('name')->join(', ') }}</span>
                                    </div>
                                </div>
                                <span style="font-family:var(--font-serif);font-size:12px;font-weight:700;color:var(--v-ink);text-align:right;">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                                <div style="text-align:right;">
                                    <span style="display:inline-flex;padding:1px 5px;font-size:7px;font-weight:700;letter-spacing:1px;text-transform:uppercase;
                                        @if($booking->status === BookingStatus::Completed) background:rgba(22,163,74,0.06);color:#15803d;border:1px solid rgba(22,163,74,0.2);
                                        @elseif($booking->status === BookingStatus::Cancelled) background:rgba(220,38,38,0.06);color:#dc2626;border:1px solid rgba(220,38,38,0.2);
                                        @else background:var(--v-surface);color:var(--v-muted);border:1px solid var(--v-rule);
                                        @endif">{{ $booking->status->label() }}</span>
                                </div>
                            </div>
                            {{-- Review inline (compact) --}}
                            @if($booking->status === BookingStatus::Completed)
                                @if($booking->review)
                                    <div style="padding:0 16px 8px 76px;display:flex;align-items:center;gap:6px;">
                                        <div style="display:flex;align-items:center;gap:1px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-symbols-outlined {{ $i <= $booking->review->rating ? 'fill' : '' }}" style="font-size:10px;color:{{ $i <= $booking->review->rating ? 'var(--v-copper)' : 'var(--v-muted)' }};opacity:{{ $i <= $booking->review->rating ? '1' : '0.3' }};">star</span>
                                            @endfor
                                        </div>
                                        @if($booking->review->comment)
                                            <span style="font-size:11px;color:var(--v-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($booking->review->comment, 50) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div style="padding:0 16px 8px 76px;" x-data="{ open: false, rating: 0, hoverRating: 0 }">
                                        <button @click="open = !open" type="button"
                                            style="display:flex;align-items:center;gap:4px;font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-copper);background:none;border:none;cursor:pointer;padding:0;">
                                            <span class="material-symbols-outlined" style="font-size:12px;">rate_review</span>
                                            Đánh giá
                                        </button>
                                        <div x-show="open" x-transition x-cloak style="margin-top:8px;padding-bottom:4px;">
                                            <form method="POST" action="{{ route('client.reviews.store') }}">
                                                @csrf
                                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                <div style="display:flex;align-items:center;gap:2px;margin-bottom:8px;">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <button type="button" @click="rating = {{ $i }}"
                                                            @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0"
                                                            style="background:none;border:none;cursor:pointer;padding:0;">
                                                            <span class="material-symbols-outlined" style="font-size:18px;transition:color 0.15s;"
                                                                :class="(hoverRating || rating) >= {{ $i }} ? 'fill' : ''"
                                                                :style="(hoverRating || rating) >= {{ $i }} ? 'color:var(--v-copper)' : 'color:var(--v-muted);opacity:0.3'">star</span>
                                                        </button>
                                                    @endfor
                                                    <input type="hidden" name="rating" :value="rating">
                                                </div>
                                                <textarea name="comment" rows="1" class="v-textarea" style="font-size:12px;margin-bottom:6px;" placeholder="Nhận xét (không bắt buộc)..."></textarea>
                                                <button type="submit" class="v-btn-primary v-btn-sm" style="height:32px;font-size:8px;"
                                                    :disabled="rating === 0"
                                                    :style="rating === 0 ? 'opacity:0.4;cursor:not-allowed' : ''">Gửi đánh giá</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                    {{-- Show more toggle --}}
                    @if($pastBookings->count() > 5)
                        <button @click="showAll = !showAll" type="button"
                            style="width:100%;padding:10px;background:var(--v-surface);border:none;cursor:pointer;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--v-copper);display:flex;align-items:center;justify-content:center;gap:4px;transition:color 0.2s;"
                            onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">
                            <span x-text="showAll ? 'Thu gọn' : 'Xem thêm ({{ $pastBookings->count() - 5 }})'"></span>
                            <span class="material-symbols-outlined" style="font-size:14px;" x-text="showAll ? 'expand_less' : 'expand_more'"></span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Logout --}}
        <div style="margin-top:40px;padding-top:32px;border-top:1px solid var(--v-rule);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--v-muted);background:none;border:none;cursor:pointer;transition:color 0.2s;"
                    onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">
                    <span class="material-symbols-outlined" style="font-size:16px;">logout</span>
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
