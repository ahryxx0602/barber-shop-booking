@extends('layouts.client')

@section('title', 'Hệ thống Chi nhánh')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 24px 64px;" class="md:px-12 lg:px-24">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:40px;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 14px;">
                Hệ thống
            </div>
            <h1 class="v-title" style="font-size:clamp(1.75rem,3vw,2.5rem);">Chi Nhánh Của Chúng Tôi</h1>
            <p style="color:var(--v-muted);max-width:460px;margin:10px auto 0;font-size:14px;line-height:1.6;">
                Tìm chi nhánh gần bạn nhất và đặt lịch ngay.
            </p>
        </div>

        {{-- Branch List --}}
        @if($branches->isEmpty())
            <div style="text-align:center;padding:48px 0;">
                <span class="material-symbols-outlined" style="font-size:40px;color:var(--v-muted);opacity:0.4;display:block;margin-bottom:10px;">location_off</span>
                <p style="color:var(--v-muted);font-size:14px;">Chưa có chi nhánh nào.</p>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;">
                @foreach($branches as $branch)
                <div class="v-card" style="border-radius:16px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.12)'"
                     onmouseout="this.style.transform='';this.style.boxShadow=''">

                    {{-- Ảnh chi nhánh --}}
                    @if($branch->image)
                        <div style="height:200px;overflow:hidden;">
                            <img src="{{ Storage::url($branch->image) }}" alt="{{ $branch->name }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    @else
                        <div style="height:200px;background:linear-gradient(135deg,var(--v-brown) 0%,var(--v-gold) 100%);display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-outlined" style="font-size:56px;color:rgba(255,255,255,0.5);">storefront</span>
                        </div>
                    @endif

                    {{-- Nội dung --}}
                    <div style="padding:20px 24px 24px;">
                        <h3 style="font-family:var(--v-font-serif);font-size:1.25rem;color:var(--v-text);margin-bottom:12px;">
                            {{ $branch->name }}
                        </h3>

                        {{-- Địa chỉ --}}
                        <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-gold);margin-top:1px;">location_on</span>
                            <span style="font-size:13px;color:var(--v-muted);line-height:1.5;">{{ $branch->address }}</span>
                        </div>

                        {{-- SĐT --}}
                        @if($branch->phone)
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-gold);">call</span>
                            <a href="tel:{{ $branch->phone }}" style="font-size:13px;color:var(--v-brown);text-decoration:none;">{{ $branch->phone }}</a>
                        </div>
                        @endif

                        {{-- Số thợ --}}
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                            <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-gold);">group</span>
                            <span style="font-size:13px;color:var(--v-muted);">{{ $branch->barbers_count }} thợ cắt</span>
                        </div>

                        {{-- Mô tả --}}
                        @if($branch->description)
                        <p style="font-size:12.5px;color:var(--v-muted);line-height:1.6;margin-bottom:16px;opacity:0.85;">
                            {{ Str::limit($branch->description, 100) }}
                        </p>
                        @endif

                        {{-- Actions --}}
                        <div style="display:flex;gap:10px;">
                            <a href="{{ route('client.barbers.index', ['branch_id' => $branch->id]) }}"
                               class="v-btn v-btn-primary" style="flex:1;text-align:center;font-size:13px;padding:10px 16px;">
                                Xem thợ cắt
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection
