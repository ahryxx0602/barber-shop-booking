@extends('layouts.client')

@section('title', 'Thợ yêu thích')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;padding:48px 16px;" class="sm:px-6 lg:px-8">
    <div style="max-width:900px;margin:0 auto;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
            <div style="display:flex;align-items:center;">
                <a href="{{ route('client.profile.show') }}" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;color:var(--v-ink);margin-right:16px;border:1px solid var(--v-rule);transition:border-color 0.2s;"
                    onmouseover="this.style.borderColor='var(--v-copper)'" onmouseout="this.style.borderColor='var(--v-rule)'">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="v-title-sm">Thợ Cắt Tóc Yêu Thích</h1>
            </div>
        </div>

        {{-- Barbers Grid --}}
        <div>
            @if($favoriteBarbers->isEmpty())
                <div style="border:1px solid var(--v-rule);background:#fff;padding:48px 24px;text-align:center;">
                    <span class="material-symbols-outlined" style="font-size:32px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:6px;">heart_broken</span>
                    <p style="color:var(--v-muted);font-size:13px;">Bạn chưa có thợ cắt tóc yêu thích nào.</p>
                    <a href="{{ route('client.barbers.index') }}" class="v-btn-primary v-btn-sm" style="margin-top:16px;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:14px;">groups</span>
                        Xem danh sách thợ
                    </a>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;">
                    @foreach($favoriteBarbers as $barber)
                        <a href="{{ route('client.barbers.show', $barber) }}" style="position:relative;text-decoration:none;display:block;border:1px solid var(--v-rule);background:#fff;overflow:hidden;transition:box-shadow 0.2s,transform 0.2s;"
                            onmouseover="this.style.boxShadow='3px 3px 0 var(--v-copper)';this.style.transform='translate(-1px,-1px)'" onmouseout="this.style.boxShadow='none';this.style.transform=''">
                            <div style="aspect-ratio:1/1;width:100%;background:var(--v-surface);overflow:hidden;position:relative;">
                                @if($barber->user->avatar)
                                    <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                        class="v-img-grayscale" style="width:100%;height:100%;object-fit:cover;display:block;" />
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                        <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-muted);opacity:0.5;">person</span>
                                    </div>
                                @endif
                                
                                {{-- Rating --}}
                                <div style="position:absolute;bottom:0;left:0;right:0;padding:24px 8px 8px;background:linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 100%);display:flex;justify-content:space-between;align-items:flex-end;">
                                    <div style="display:flex;align-items:center;gap:4px;color:#fff;">
                                        <span class="material-symbols-outlined fill" style="font-size:12px;color:#fbbf24;">star</span>
                                        <span style="font-size:11px;font-weight:600;">{{ number_format($barber->rating, 1) }}</span>
                                    </div>
                                    <span style="font-size:10px;color:rgba(255,255,255,0.8);">({{ $barber->reviews_count ?? 0 }})</span>
                                </div>
                            </div>
                            
                            {{-- Trái Tim Toggle (Unfavorite) --}}
                            <div style="position:absolute;top:8px;right:8px;z-index:10;">
                                <button type="button" onclick="toggleFavorite(event, {{ $barber->id }}, this)"
                                    style="width:30px;height:30px;border-radius:50%;background:#fff;border:1px solid var(--v-rule);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;box-shadow:2px 2px 0 rgba(0,0,0,0.1);"
                                    onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                    <span class="material-symbols-outlined fill" 
                                          style="font-size:16px; transition:color 0.2s; color:#dc2626;">favorite</span>
                                </button>
                            </div>

                            <div style="padding:12px;text-align:center;">
                                <h3 style="font-family:var(--font-serif);font-size:14px;font-weight:700;color:var(--v-ink);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $barber->user->name }}
                                </h3>
                                <p style="font-size:11px;color:var(--v-muted);display:flex;align-items:center;justify-content:center;gap:4px;">
                                    <span class="material-symbols-outlined" style="font-size:12px;">cut</span>
                                    {{ $barber->experience_years }} năm kinh nghiệm
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</section>

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
            
            // Xoá luôn card đó khỏi giao diện khi unfavorite trong trang này cho mượt
            const card = btn.closest('a');
            if (card) {
                card.style.display = 'none';
            }
        }
    })
    .catch(err => console.error(err));
}
</script>
@endpush
@endsection
