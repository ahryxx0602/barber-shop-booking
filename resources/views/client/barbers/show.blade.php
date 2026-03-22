@extends('layouts.client')

@section('title', $barber->user->name)

@section('content')
<section class="bg-bg-light min-h-screen">
    <div class="max-w-[1000px] mx-auto px-6 md:px-12 py-16 md:py-24">
        {{-- Back link --}}
        <a href="{{ route('client.barbers.index') }}" class="inline-flex items-center gap-2 text-warm-gray-light hover:text-primary transition-colors text-sm mb-10">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>

        {{-- Profile --}}
        <div class="flex flex-col md:flex-row gap-10 md:gap-16 mb-16">
            {{-- Avatar --}}
            <div class="w-64 h-80 shrink-0 overflow-hidden mx-auto md:mx-0">
                @if($barber->user->avatar)
                    <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                        class="w-full h-full object-cover filter grayscale hover:grayscale-0 transition-all duration-700" />
                @else
                    <div class="w-full h-full bg-surface flex items-center justify-center">
                        <span class="material-symbols-outlined text-8xl text-muted/40">person</span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-8 h-[1.5px] bg-primary"></div>
                    <span class="text-[10px] font-semibold tracking-[4px] uppercase text-warm-gray-light">Thợ cắt</span>
                </div>
                <h1 class="font-serif text-4xl md:text-5xl font-bold text-warm-gray mb-4">{{ $barber->user->name }}</h1>

                <div class="flex items-center gap-6 mb-6 text-sm text-warm-gray-light">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-base text-primary">work_history</span>
                        {{ $barber->experience_years }} năm kinh nghiệm
                    </div>
                    @if($barber->rating > 0)
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill text-primary' : 'text-muted/30' }} text-sm">star</span>
                        @endfor
                        <span class="font-medium text-warm-gray ml-1">{{ number_format($barber->rating, 1) }}</span>
                        <span class="text-muted">({{ $barber->reviews->count() }} đánh giá)</span>
                    </div>
                    @endif
                </div>

                @if($barber->bio)
                <p class="text-base leading-[1.8] text-warm-gray-light mb-8">{{ $barber->bio }}</p>
                @endif

                <a href="{{ route('client.booking.create') }}?barber_id={{ $barber->id }}" class="inline-flex items-center justify-center h-[52px] px-8 bg-primary text-white text-[11px] font-bold uppercase tracking-[2.5px] transition-all duration-300 hover:bg-warm-gray">
                    Đặt lịch với {{ $barber->user->name }}
                </a>
            </div>
        </div>

        {{-- Working Schedule --}}
        @if($barber->workingSchedules->isNotEmpty())
        <div class="mb-16">
            <h2 class="text-xl font-bold text-warm-gray mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">calendar_month</span>
                Lịch làm việc
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                @php
                    $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                @endphp
                @for($day = 0; $day <= 6; $day++)
                    @php
                        $schedule = $barber->workingSchedules->firstWhere('day_of_week', $day);
                    @endphp
                    <div class="text-center p-4 {{ $schedule && !$schedule->is_day_off ? 'bg-white border border-muted/20' : 'bg-surface/50 border border-transparent' }}">
                        <div class="text-xs font-bold tracking-widest uppercase text-warm-gray-light mb-2">{{ $dayNames[$day] }}</div>
                        @if($schedule && !$schedule->is_day_off)
                            <div class="text-sm font-medium text-warm-gray">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                            </div>
                            <div class="text-xs text-muted">đến</div>
                            <div class="text-sm font-medium text-warm-gray">
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </div>
                        @else
                            <div class="text-sm text-muted italic">Nghỉ</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
        @endif

        {{-- Reviews --}}
        <div>
            <h2 class="text-xl font-bold text-warm-gray mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">rate_review</span>
                Đánh giá từ khách hàng
                @if($barber->reviews->isNotEmpty())
                    <span class="text-sm font-normal text-muted">({{ $barber->reviews->count() }})</span>
                @endif
            </h2>

            @if($barber->reviews->isNotEmpty())
                {{-- Rating summary --}}
                <div class="bg-white border border-muted/10 p-6 mb-6 flex flex-col sm:flex-row items-center gap-6">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-warm-gray">{{ number_format($barber->rating, 1) }}</div>
                        <div class="flex items-center gap-0.5 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill text-primary' : 'text-muted/30' }} text-base">star</span>
                            @endfor
                        </div>
                        <div class="text-xs text-muted mt-1">{{ $barber->reviews->count() }} đánh giá</div>
                    </div>
                    <div class="flex-1 w-full space-y-1">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $barber->reviews->where('rating', $star)->count();
                                $pct = $barber->reviews->count() > 0 ? ($count / $barber->reviews->count()) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-3 text-right text-muted">{{ $star }}</span>
                                <span class="material-symbols-outlined fill text-primary text-xs">star</span>
                                <div class="flex-1 h-2 bg-surface rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="w-6 text-right text-xs text-muted">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Review list --}}
                <div class="space-y-4" x-data="{ showAll: false }">
                    @foreach($barber->reviews->sortByDesc('created_at')->values() as $index => $review)
                        <div class="bg-white border border-muted/10 p-6" x-show="showAll || {{ $index }} < 5" x-transition>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="font-medium text-warm-gray">{{ $review->customer->name }}</span>
                                    <span class="text-sm text-muted ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined {{ $i <= $review->rating ? 'fill text-primary' : 'text-muted/30' }} text-sm">star</span>
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-warm-gray-light text-sm leading-relaxed">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach

                    @if($barber->reviews->count() > 5)
                        <button @click="showAll = !showAll" type="button"
                            class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-warm-gray transition-colors mx-auto">
                            <span x-text="showAll ? 'Thu gọn' : 'Xem tất cả {{ $barber->reviews->count() }} đánh giá'"></span>
                            <span class="material-symbols-outlined text-base" x-text="showAll ? 'expand_less' : 'expand_more'"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-white border border-muted/10 p-8 text-center">
                    <span class="material-symbols-outlined text-3xl text-muted/30 mb-2">rate_review</span>
                    <p class="text-sm text-muted">Chưa có đánh giá nào.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
