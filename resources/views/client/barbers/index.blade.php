@extends('layouts.client')

@section('title', 'Đội ngũ thợ cắt')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">
        {{-- Header --}}
        <div style="text-align:center;margin-bottom:32px;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 14px;">
                Đội ngũ
            </div>
            <h1 class="v-title" style="font-size:clamp(1.75rem,3vw,2.5rem);">Thợ Cắt Của Chúng Tôi</h1>
            <p style="color:var(--v-muted);max-width:400px;margin:10px auto 0;font-size:14px;line-height:1.6;">
                Đội ngũ chuyên nghiệp, giàu kinh nghiệm và đam mê với nghề.
            </p>
        </div>

        {{-- Search --}}
        <div style="max-width:440px;margin:0 auto 28px;" x-data="{ search: '{{ request('search') }}' }">
            <form method="GET" action="{{ route('client.barbers.index') }}">
                <div style="position:relative;">
                    <span class="material-symbols-outlined" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--v-muted);font-size:18px;">search</span>
                    <input type="text" name="search" x-model="search" placeholder="Tìm thợ cắt..."
                        class="v-input" style="padding-left:40px;height:40px;font-size:13px;" />
                </div>
            </form>
        </div>

        {{-- Barber List --}}
        @if($barbers->isEmpty())
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:40px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:10px;">content_cut</span>
                <p style="color:var(--v-muted);font-size:14px;">Không tìm thấy thợ cắt nào.</p>
            </div>
        @else
            <div x-data="{ showAll: false }">
                {{-- ═══ CAROUSEL VIEW ═══ --}}
                <div x-show="!showAll" style="position:relative;" x-data="scrollCarousel()">
                    {{-- Arrow Left --}}
                    <button @click="scrollLeft()" type="button"
                        style="position:absolute;left:-12px;top:40%;transform:translateY(-50%);z-index:5;width:36px;height:36px;display:none;align-items:center;justify-content:center;background:#fff;border:1px solid var(--v-rule);box-shadow:2px 2px 0 var(--v-copper);cursor:pointer;transition:all 0.2s;"
                        onmouseover="this.style.boxShadow='3px 3px 0 var(--v-copper-dk)'" onmouseout="this.style.boxShadow='2px 2px 0 var(--v-copper)'"
                        x-ref="btnLeft">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-ink);">chevron_left</span>
                    </button>

                    {{-- Arrow Right --}}
                    <button @click="scrollRight()" type="button"
                        style="position:absolute;right:-12px;top:40%;transform:translateY(-50%);z-index:5;width:36px;height:36px;display:none;align-items:center;justify-content:center;background:#fff;border:1px solid var(--v-rule);box-shadow:2px 2px 0 var(--v-copper);cursor:pointer;transition:all 0.2s;"
                        onmouseover="this.style.boxShadow='3px 3px 0 var(--v-copper-dk)'" onmouseout="this.style.boxShadow='2px 2px 0 var(--v-copper)'"
                        x-ref="btnRight">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--v-ink);">chevron_right</span>
                    </button>

                    {{-- Scrollable Track --}}
                    <div x-ref="scroller" @scroll="updateArrows()"
                        class="barber-scroll-track">
                        @foreach($barbers as $barber)
                        <a href="{{ route('client.barbers.show', $barber) }}" class="barber-scroll-card" style="text-decoration:none;display:block;flex-shrink:0;">
                            <div style="position:relative;margin-bottom:12px;border:1px solid var(--v-rule);box-shadow:3px 3px 0 var(--v-copper);overflow:hidden;transition:box-shadow 0.3s,transform 0.3s;"
                                 onmouseover="this.style.boxShadow='5px 5px 0 var(--v-copper-dk)';this.style.transform='translate(-2px,-2px)'"
                                 onmouseout="this.style.boxShadow='3px 3px 0 var(--v-copper)';this.style.transform=''">
                                <div style="aspect-ratio:3/4;width:100%;background:var(--v-surface);">
                                    @if($barber->user->avatar)
                                        <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                            class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                                    @else
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                            <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-muted);opacity:0.4;">person</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="corner corner-tl"></div>
                                <div class="corner corner-br"></div>
                            </div>
                            <div style="text-align:center;">
                                <h3 style="font-family:var(--font-serif);font-size:16px;font-weight:700;color:var(--v-ink);margin-bottom:2px;">{{ $barber->user->name }}</h3>
                                <p style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:4px;">{{ $barber->experience_years }} năm KN</p>
                                @if($barber->rating > 0)
                                <div style="display:flex;align-items:center;justify-content:center;gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined {{ $i <= round($barber->rating) ? 'fill' : '' }}" style="font-size:11px;color:{{ $i <= round($barber->rating) ? 'var(--v-copper)' : 'var(--v-muted)' }};opacity:{{ $i <= round($barber->rating) ? '1' : '0.3' }};">star</span>
                                    @endfor
                                    <span style="font-size:11px;font-weight:600;color:var(--v-ink);margin-left:2px;">{{ number_format($barber->rating, 1) }}</span>
                                </div>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Toggle View --}}
                @if($barbers->count() > 4)
                <div style="text-align:center;margin-top:20px;">
                    <button @click="showAll = !showAll" type="button"
                        style="display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--v-copper);background:none;border:none;cursor:pointer;transition:color 0.2s;padding:8px 0;"
                        onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">
                        <span class="material-symbols-outlined" style="font-size:16px;" x-text="showAll ? 'view_carousel' : 'grid_view'"></span>
                        <span x-text="showAll ? 'Xem dạng carousel' : 'Xem tất cả {{ $barbers->count() }} thợ cắt'"></span>
                    </button>
                </div>
                @endif

                {{-- ═══ GRID VIEW (compact) ═══ --}}
                <div x-show="showAll" x-transition.duration.300ms style="margin-top:24px;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;">
                        @foreach($barbers as $barber)
                        <a href="{{ route('client.barbers.show', $barber) }}" style="text-decoration:none;display:block;border:1px solid var(--v-rule);background:#fff;overflow:hidden;transition:box-shadow 0.2s,transform 0.2s;"
                            onmouseover="this.style.boxShadow='3px 3px 0 var(--v-copper)';this.style.transform='translate(-1px,-1px)'" onmouseout="this.style.boxShadow='none';this.style.transform=''">
                            <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);overflow:hidden;">
                                @if($barber->user->avatar)
                                    <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                        class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                        <span class="material-symbols-outlined" style="font-size:32px;color:var(--v-muted);opacity:0.4;">person</span>
                                    </div>
                                @endif
                            </div>
                            <div style="padding:8px 10px;text-align:center;">
                                <h3 style="font-family:var(--font-serif);font-size:13px;font-weight:700;color:var(--v-ink);margin-bottom:2px;">{{ $barber->user->name }}</h3>
                                <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                    <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);">{{ $barber->experience_years }} năm</span>
                                    @if($barber->rating > 0)
                                        <span style="color:var(--v-rule);">·</span>
                                        <span class="material-symbols-outlined fill" style="font-size:10px;color:var(--v-copper);">star</span>
                                        <span style="font-size:10px;font-weight:600;color:var(--v-ink);">{{ number_format($barber->rating, 1) }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .barber-scroll-track {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        justify-content: center;
        -ms-overflow-style: none;
        scrollbar-width: none;
        padding: 4px 0 8px;
    }
    .barber-scroll-track::-webkit-scrollbar { display: none; }

    .barber-scroll-card {
        scroll-snap-align: start;
        width: calc((100% - 72px) / 4);    /* 4 cards, 3 gaps of 24px */
        min-width: calc((100% - 72px) / 4);
    }

    @media (max-width: 900px) {
        .barber-scroll-card {
            width: calc((100% - 48px) / 3);  /* 3 cards */
            min-width: calc((100% - 48px) / 3);
        }
    }
    @media (max-width: 640px) {
        .barber-scroll-card {
            width: calc((100% - 24px) / 2);  /* 2 cards */
            min-width: calc((100% - 24px) / 2);
        }
    }
</style>
@endpush

@push('scripts')
<script>
function scrollCarousel() {
    return {
        init() {
            this.$nextTick(() => this.updateArrows());
        },

        scrollLeft() {
            const el = this.$refs.scroller;
            el.scrollBy({ left: -(el.offsetWidth * 0.8), behavior: 'smooth' });
        },

        scrollRight() {
            const el = this.$refs.scroller;
            el.scrollBy({ left: el.offsetWidth * 0.8, behavior: 'smooth' });
        },

        updateArrows() {
            const el = this.$refs.scroller;
            if (!el) return;
            const left = this.$refs.btnLeft;
            const right = this.$refs.btnRight;
            if (left) left.style.display = el.scrollLeft > 10 ? 'flex' : 'none';
            if (right) right.style.display = (el.scrollLeft + el.offsetWidth) < (el.scrollWidth - 10) ? 'flex' : 'none';
        }
    }
}
</script>
@endpush
@endsection
