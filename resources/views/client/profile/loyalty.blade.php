@extends('layouts.client')

@section('title', 'Điểm thưởng')

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
                <h1 class="v-title-sm">Điểm Thưởng</h1>
            </div>
        </div>

        {{-- Balance Card --}}
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);margin-bottom:32px;">
            <div style="padding:24px;" class="sm:p-8">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:56px;height:56px;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);display:flex;align-items:center;justify-content:center;">
                        <span class="material-symbols-outlined" style="font-size:28px;color:var(--v-copper);">stars</span>
                    </div>
                    <div>
                        <p style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:4px;">Điểm hiện tại</p>
                        <p style="font-family:var(--font-serif);font-size:36px;font-weight:700;color:var(--v-ink);line-height:1;">{{ number_format($balance) }}</p>
                    </div>
                </div>
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--v-rule);">
                    <p style="font-size:12px;color:var(--v-muted);">
                        <span class="material-symbols-outlined" style="font-size:14px;vertical-align:-2px;">info</span>
                        Mỗi {{ number_format(App\Services\LoyaltyService::POINTS_PER_VND) }}đ chi tiêu = 1 điểm thưởng. Điểm được cộng tự động sau khi hoàn thành dịch vụ.
                    </p>
                </div>
            </div>
        </div>

        {{-- History --}}
        <div>
            <div class="v-ornament" style="max-width:360px;margin-bottom:16px;justify-content:flex-start;">
                Lịch sử điểm thưởng
            </div>

            @if($history->isEmpty())
                <div style="border:1px solid var(--v-rule);background:#fff;padding:24px;text-align:center;">
                    <span class="material-symbols-outlined" style="font-size:32px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:6px;">stars</span>
                    <p style="color:var(--v-muted);font-size:13px;">Chưa có lịch sử điểm thưởng.</p>
                    <a href="{{ route('client.booking.create') }}" class="v-btn-primary v-btn-sm" style="margin-top:12px;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                        Đặt lịch để tích điểm
                    </a>
                </div>
            @else
                <div style="border:1px solid var(--v-rule);background:#fff;overflow:hidden;">
                    {{-- Table header --}}
                    <div style="display:grid;grid-template-columns:1fr 80px 80px;gap:0;padding:8px 16px;background:var(--v-surface);border-bottom:1px solid var(--v-rule);">
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);">Mô tả</span>
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:right;">Điểm</span>
                        <span style="font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-align:right;">Ngày</span>
                    </div>
                    {{-- Rows --}}
                    @foreach($history as $item)
                        <div style="display:grid;grid-template-columns:1fr 80px 80px;gap:0;padding:10px 16px;align-items:center;border-bottom:1px solid var(--v-rule);">
                            <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                                @if($item->type === \App\Enums\LoyaltyPointType::Earn)
                                    <span class="material-symbols-outlined" style="font-size:16px;color:#15803d;">arrow_upward</span>
                                @else
                                    <span class="material-symbols-outlined" style="font-size:16px;color:#dc2626;">arrow_downward</span>
                                @endif
                                <span style="font-size:13px;color:var(--v-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->description }}</span>
                            </div>
                            <span style="font-family:var(--font-serif);font-size:13px;font-weight:700;text-align:right;
                                color:{{ $item->type === \App\Enums\LoyaltyPointType::Earn ? '#15803d' : '#dc2626' }};">
                                {{ $item->type === \App\Enums\LoyaltyPointType::Earn ? '+' : '' }}{{ $item->points }}
                            </span>
                            <span style="font-size:12px;color:var(--v-muted);text-align:right;">{{ $item->created_at->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div style="margin-top:16px;">
                    {{ $history->links() }}
                </div>
            @endif
        </div>

    </div>
</section>
@endsection
