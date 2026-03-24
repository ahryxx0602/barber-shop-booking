@extends('layouts.client')

@section('title', $barber->user->name)

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:900px;margin:0 auto;padding:32px 24px 64px;" class="md:px-12">
        {{-- Back link --}}
        <a href="{{ route('client.barbers.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--v-muted);text-decoration:none;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:24px;transition:color 0.2s;"
            onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">
            <span class="material-symbols-outlined" style="font-size:14px;">arrow_back</span>
            Danh sách thợ cắt
        </a>

        {{-- ═══ PROFILE CARD — avatar + info side by side ═══ --}}
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);margin-bottom:24px;overflow:hidden;">
            <div style="display:flex;flex-direction:column;" class="sm:flex-row">
                {{-- Avatar (compact) --}}
                <div style="width:100%;height:200px;overflow:hidden;position:relative;flex-shrink:0;" class="sm:w-48 sm:h-auto">
                    @if($barber->user->avatar)
                        <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                            class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;" />
                    @else
                        <div style="width:100%;height:100%;background:var(--v-surface);display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-outlined" style="font-size:56px;color:var(--v-muted);opacity:0.4;">person</span>
                        </div>
                    @endif
                    <div class="corner corner-tl"></div>
                    <div class="corner corner-br"></div>
                </div>

                {{-- Info --}}
                <div style="padding:20px 24px;flex:1;display:flex;flex-direction:column;justify-content:center;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <div style="width:20px;height:1.5px;background:var(--v-copper);"></div>
                        <span class="v-label">Thợ cắt</span>
                    </div>
                    <h1 style="font-family:var(--font-serif);font-size:clamp(1.5rem,3vw,2rem);font-weight:700;color:var(--v-ink);margin-bottom:10px;">{{ $barber->user->name }}</h1>

                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;font-size:13px;color:var(--v-muted);flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-outlined" style="font-size:14px;color:var(--v-copper);">work_history</span>
                            {{ $barber->experience_years }} năm KN
                        </div>
                        @if($barber->rating > 0)
                        <div style="display:flex;align-items:center;gap:3px;">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill' : '' }}" style="font-size:12px;color:{{ $i <= round($barber->rating) ? 'var(--v-copper)' : 'var(--v-muted)' }};opacity:{{ $i <= round($barber->rating) ? '1' : '0.3' }};">star</span>
                            @endfor
                            <span style="font-weight:600;color:var(--v-ink);margin-left:2px;">{{ number_format($barber->rating, 1) }}</span>
                            <span style="color:var(--v-muted);">({{ $barber->reviews->count() }})</span>
                        </div>
                        @endif
                    </div>

                    @if($barber->bio)
                    <p style="font-size:13px;line-height:1.7;color:var(--v-muted);margin-bottom:16px;">{{ Str::limit($barber->bio, 150) }}</p>
                    @endif

                    <div style="display:flex;align-items:center;gap:12px;margin-top:auto;">
                        <a href="{{ route('client.booking.create') }}?barber_id={{ $barber->id }}" class="v-btn-primary" style="height:44px;padding:0 28px;font-size:9px;">
                            Đặt lịch ngay
                        </a>
                        
                        @auth
                        <button type="button" onclick="toggleFavorite(event, {{ $barber->id }}, this)"
                            style="width:44px;height:44px;border-radius:2px;background:#fff;border:1px solid var(--v-rule);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;box-shadow:4px 4px 0 var(--v-copper);"
                            onmouseover="this.style.boxShadow='2px 2px 0 var(--v-copper-dk)';this.style.transform='translate(2px,2px)'" 
                            onmouseout="this.style.boxShadow='4px 4px 0 var(--v-copper)';this.style.transform='translate(0,0)'">
                            <span class="material-symbols-outlined {{ auth()->user()->favoriteBarbers->contains($barber->id) ? 'fill' : '' }}" 
                                  style="font-size:20px; transition:color 0.2s; color:{{ auth()->user()->favoriteBarbers->contains($barber->id) ? '#dc2626' : 'var(--v-muted)' }};">favorite</span>
                        </button>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SCHEDULE + REVIEWS — side by side on desktop ═══ --}}
        <div style="display:grid;grid-template-columns:1fr;gap:24px;" class="md:grid-cols-5">

            {{-- LEFT: Schedule (2 cols) --}}
            @if($barber->workingSchedules->isNotEmpty())
            <div class="md:col-span-2">
                <div style="border:1px solid var(--v-rule);background:#fff;overflow:hidden;height:100%;">
                    <div style="padding:12px 16px;background:var(--v-surface);border-bottom:1px solid var(--v-rule);">
                        <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Lịch làm việc</span>
                    </div>
                    <div style="padding:8px;">
                        @php $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']; @endphp
                        @for($day = 0; $day <= 6; $day++)
                            @php $schedule = $barber->workingSchedules->firstWhere('day_of_week', $day); @endphp
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 8px;{{ $day < 6 ? 'border-bottom:1px solid var(--v-rule);' : '' }}">
                                <span style="font-size:11px;font-weight:600;color:{{ $schedule && !$schedule->is_day_off ? 'var(--v-ink)' : 'var(--v-muted)' }};width:24px;">{{ $dayNames[$day] }}</span>
                                @if($schedule && !$schedule->is_day_off)
                                    <span style="font-size:12px;color:var(--v-ink);">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                @else
                                    <span style="font-size:11px;color:var(--v-muted);font-style:italic;">Nghỉ</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            @endif

            {{-- RIGHT: Reviews (3 cols) --}}
            <div class="{{ $barber->workingSchedules->isNotEmpty() ? 'md:col-span-3' : 'md:col-span-5' }}">
                <div style="border:1px solid var(--v-rule);background:#fff;overflow:hidden;">
                    <div style="padding:12px 16px;background:var(--v-surface);border-bottom:1px solid var(--v-rule);display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Đánh giá</span>
                        @if($barber->reviews->isNotEmpty())
                            <span style="font-size:11px;color:var(--v-muted);">{{ $barber->reviews->count() }} đánh giá</span>
                        @endif
                    </div>

                    @if($barber->reviews->isNotEmpty())
                        {{-- Rating summary bar (compact) --}}
                        <div style="padding:16px;border-bottom:1px solid var(--v-rule);display:flex;align-items:center;gap:16px;">
                            <div style="text-align:center;flex-shrink:0;">
                                <div style="font-family:var(--font-serif);font-size:32px;font-weight:700;color:var(--v-ink);line-height:1;">{{ number_format($barber->rating, 1) }}</div>
                                <div style="display:flex;align-items:center;gap:1px;margin-top:4px;justify-content:center;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill' : '' }}" style="font-size:10px;color:{{ $i <= round($barber->rating) ? 'var(--v-copper)' : 'var(--v-muted)' }};opacity:{{ $i <= round($barber->rating) ? '1' : '0.3' }};">star</span>
                                    @endfor
                                </div>
                            </div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:3px;">
                                @for($star = 5; $star >= 1; $star--)
                                    @php
                                        $count = $barber->reviews->where('rating', $star)->count();
                                        $pct = $barber->reviews->count() > 0 ? ($count / $barber->reviews->count()) * 100 : 0;
                                    @endphp
                                    <div style="display:flex;align-items:center;gap:4px;">
                                        <span style="width:8px;font-size:10px;text-align:right;color:var(--v-muted);">{{ $star }}</span>
                                        <div style="flex:1;height:4px;background:var(--v-surface);overflow:hidden;">
                                            <div style="height:100%;background:var(--v-copper);width:{{ $pct }}%;"></div>
                                        </div>
                                        <span style="width:16px;text-align:right;font-size:9px;color:var(--v-muted);">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Review list (compact rows) --}}
                        <div x-data="{ showAll: false }">
                            @foreach($barber->reviews->sortByDesc('created_at')->values() as $index => $review)
                                <div style="padding:10px 16px;border-bottom:1px solid var(--v-rule);" x-show="showAll || {{ $index }} < 3" x-transition>
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:{{ $review->comment ? '4px' : '0' }};">
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <span style="font-size:12px;font-weight:600;color:var(--v-ink);">{{ $review->customer->name }}</span>
                                            <span style="font-size:10px;color:var(--v-muted);">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:1px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-symbols-outlined {{ $i <= $review->rating ? 'fill' : '' }}" style="font-size:10px;color:{{ $i <= $review->rating ? 'var(--v-copper)' : 'var(--v-muted)' }};opacity:{{ $i <= $review->rating ? '1' : '0.3' }};">star</span>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p style="font-size:12px;line-height:1.5;color:var(--v-muted);">{{ Str::limit($review->comment, 120) }}</p>
                                    @endif
                                </div>
                            @endforeach

                            @if($barber->reviews->count() > 3)
                                <button @click="showAll = !showAll" type="button"
                                    style="width:100%;padding:10px;background:var(--v-surface);border:none;cursor:pointer;font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--v-copper);display:flex;align-items:center;justify-content:center;gap:4px;transition:color 0.2s;"
                                    onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">
                                    <span x-text="showAll ? 'Thu gọn' : 'Xem tất cả {{ $barber->reviews->count() }} đánh giá'"></span>
                                    <span class="material-symbols-outlined" style="font-size:14px;" x-text="showAll ? 'expand_less' : 'expand_more'"></span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div style="padding:24px;text-align:center;">
                            <span class="material-symbols-outlined" style="font-size:24px;color:var(--v-muted);opacity:0.3;display:block;margin-bottom:4px;">rate_review</span>
                            <p style="font-size:12px;color:var(--v-muted);">Chưa có đánh giá nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    @media (min-width: 640px) {
        .sm\:w-48 { width: 192px; }
        .sm\:h-auto { height: auto; }
    }
</style>
@endpush

@push('scripts')
<script>
function toggleFavorite(event, barberId, btn) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const icon = btn.querySelector('span');
    fetch(`/barbers/${barberId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 401) window.location.href = '/login';
            throw new Error('Unauthorized or Error');
        }
        return res.json();
    })
    .then(data => {
        if (data.favorited) {
            icon.classList.add('fill');
            icon.style.color = '#dc2626';
        } else {
            icon.classList.remove('fill');
            icon.style.color = 'var(--v-muted)';
        }
    })
    .catch(err => console.error(err));
}
</script>
@endpush
@endsection
