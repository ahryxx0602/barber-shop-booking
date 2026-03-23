@extends('layouts.client')

@section('title', 'Trang chủ')

@push('styles')
    <style>
        /* ── SERVICE ROW ────────────────────────────────── */
        .svc-row {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 14px 0;
            border-bottom: 1px solid var(--v-rule);
            cursor: pointer;
            transition: padding-left 0.2s ease, color 0.2s ease;
        }

        .svc-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 2px;
            background: var(--v-copper);
            transition: width 0.25s ease;
        }

        .svc-row:hover {
            padding-left: 16px;
        }

        .svc-row:hover::before {
            width: 10px;
        }

        .svc-row:hover .service-name {
            color: var(--v-copper-dk);
        }

        .svc-row:hover .service-price {
            color: var(--v-copper);
        }

        .service-name {
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .service-price {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }

        /* ── BARBER CARD ─────────────────────────────────── */
        .barber-card {
            position: relative;
            transition: transform 0.3s ease;
        }

        .barber-card:hover {
            transform: translateY(-6px);
        }

        .barber-img-wrap {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            border: 2px solid var(--v-rule);
            box-shadow: 5px 5px 0 var(--v-copper);
            overflow: hidden;
            transition: box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .barber-card:hover .barber-img-wrap {
            box-shadow: 7px 7px 0 var(--v-copper-dk);
            border-color: var(--v-copper);
        }

        .barber-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(1) contrast(1.05);
            transition: filter 0.6s ease;
        }

        .barber-card:hover .barber-img-wrap img {
            filter: grayscale(0) contrast(1);
        }

        /* ── FLOATING IMAGE PANEL ───────────────────────── */
        #svcImgPanel {
            position: fixed;
            pointer-events: none;
            opacity: 0;
            z-index: 9999;
            width: 200px;
            background: var(--v-ink);
            border: 1px solid var(--v-copper);
            box-shadow: 5px 5px 0 var(--v-copper-dk);
            overflow: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease;
            transform: translateY(10px) rotate(-1deg);
        }

        #svcImgPanel.active {
            opacity: 1;
            transform: translateY(0) rotate(0deg);
        }

        #svcPanelImg {
            height: 180px;
            background-size: cover;
            background-position: center;
            filter: grayscale(1) contrast(1.05) sepia(0.2);
        }

        #svcPanelMeta {
            padding: 10px 12px 12px;
        }

        #svcPanelName {
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(245, 240, 232, 0.45);
            margin-bottom: 4px;
        }

        #svcPanelPrice {
            font-size: 13px;
            font-weight: 600;
            color: var(--v-copper);
            letter-spacing: 0.5px;
        }

        /* ── HERO IMAGE CORNERS ─────────────────────────── */
        .corner {
            position: absolute;
            width: 20px;
            height: 20px;
        }

        .corner-tl {
            top: 0;
            left: 0;
            border-top: 2px solid var(--v-copper);
            border-left: 2px solid var(--v-copper);
        }

        .corner-tr {
            top: 0;
            right: 0;
            border-top: 2px solid var(--v-copper);
            border-right: 2px solid var(--v-copper);
        }

        .corner-bl {
            bottom: 0;
            left: 0;
            border-bottom: 2px solid var(--v-copper);
            border-left: 2px solid var(--v-copper);
        }

        .corner-br {
            bottom: 0;
            right: 0;
            border-bottom: 2px solid var(--v-copper);
            border-right: 2px solid var(--v-copper);
        }

        /* ── SECTION LABEL ──────────────────────────────── */
        .v-label {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--v-muted);
        }

        /* ── SECTION TITLE (serif large) ────────────────── */
        .v-title {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 700;
            line-height: 1.1;
            color: var(--v-ink);
            letter-spacing: -0.01em;
        }

        .v-title-light {
            color: var(--v-cream);
        }

        /* ── STORY SECTION DARK ─────────────────────────── */
        .story-dark {
            background: var(--v-ink);
            position: relative;
            overflow: hidden;
        }

        .story-dark::before {
            content: 'CLASSIC CUT';
            position: absolute;
            bottom: -40px;
            right: -20px;
            font-family: var(--font-display);
            font-size: 140px;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.03);
            white-space: nowrap;
            pointer-events: none;
            letter-spacing: 10px;
        }

        /* ── CTA BANNER ─────────────────────────────────── */
        .cta-banner {
            background: var(--v-copper);
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            inset: 8px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            pointer-events: none;
        }

        /* ── SCROLL INDICATOR ───────────────────────────── */
        @keyframes scrollDot {

            0%,
            100% {
                transform: translateY(0);
                opacity: 1;
            }

            50% {
                transform: translateY(8px);
                opacity: 0.3;
            }
        }

        .scroll-dot {
            animation: scrollDot 1.6s ease-in-out infinite;
        }
    </style>
@endpush

@section('content')

    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║ HERO SECTION ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <section class="v-noise relative w-full min-h-[92vh] flex flex-col lg:flex-row items-stretch overflow-hidden"
        style="background: var(--v-cream);">

        {{-- LEFT: Typography --}}
        <div class="relative z-20 flex flex-col justify-center
                    px-6 pt-28 pb-16
                    md:px-14
                    lg:pl-[10vw] lg:pr-16 lg:pt-0
                    w-full lg:w-[52%]
                    order-last lg:order-first">

            {{-- Watermark behind text --}}
            <div aria-hidden="true" style="
                position:absolute; top:50%; left: -10px; transform:translateY(-55%);
                font-family: var(--font-display); font-size: 200px; font-weight: 300;
                color: rgba(28,23,19,0.04); white-space:nowrap; pointer-events:none;
                letter-spacing: 8px; line-height:1; user-select:none;">
                1924
            </div>

            <div class="relative max-w-[520px] mx-auto lg:mx-0 flex flex-col gap-10">

                {{-- Label --}}
                <div class="flex items-center gap-4">
                    <div style="width:32px;height:1.5px;background:var(--v-copper)"></div>
                    <span class="v-label">Est. 2024 &mdash; Da Nang</span>
                </div>

                {{-- Heading --}}
                <div class="flex flex-col gap-3">
                    <h1 class="v-title" style="color: var(--v-ink)">
                        <em style="font-style:italic; font-weight:400; color:var(--v-copper)">Nghệ Thuật</em><br>
                        Cắt Tóc Nam.
                    </h1>
                    <p style="font-size:15px; line-height:1.8; color:var(--v-muted); max-width:360px; margin-top:4px;">
                        Trải nghiệm tiệm cắt tóc vintage cao cấp — nơi kỹ thuật truyền thống
                        gặp gỡ phong cách hiện đại tại Đà Nẵng.
                    </p>
                </div>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-5">
                    <a href="{{ route('client.booking.create') }}" class="v-btn-primary">
                        Đặt Lịch Ngay
                    </a>
                    <a href="#services" style="
                        font-size:10px; font-weight:600; letter-spacing:3px; text-transform:uppercase;
                        color:var(--v-muted); text-decoration:none;
                        border-bottom:1px solid var(--v-rule); padding-bottom:3px;
                        transition: color 0.2s, border-color 0.2s;"
                        onmouseover="this.style.color='var(--v-copper)';this.style.borderColor='var(--v-copper)'"
                        onmouseout="this.style.color='var(--v-muted)';this.style.borderColor='var(--v-rule)'">
                        Xem Dịch Vụ ↓
                    </a>
                </div>

                {{-- Stats strip --}}
                <div style="display:flex; gap:32px; padding-top:8px; border-top:1px solid var(--v-rule);">
                    <div>
                        <div
                            style="font-family:var(--font-serif);font-size:28px;font-weight:700;color:var(--v-ink);line-height:1">
                            10+</div>
                        <div class="v-label" style="margin-top:4px;">Thợ chuyên nghiệp</div>
                    </div>
                    <div style="width:1px;background:var(--v-rule)"></div>
                    <div>
                        <div
                            style="font-family:var(--font-serif);font-size:28px;font-weight:700;color:var(--v-ink);line-height:1">
                            5★</div>
                        <div class="v-label" style="margin-top:4px;">Đánh giá khách hàng</div>
                    </div>
                    <div style="width:1px;background:var(--v-rule)"></div>
                    <div>
                        <div
                            style="font-family:var(--font-serif);font-size:28px;font-weight:700;color:var(--v-ink);line-height:1">
                            2024</div>
                        <div class="v-label" style="margin-top:4px;">Thành lập</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Image --}}
        <div class="relative w-full lg:w-[48%] flex items-center justify-center
                    p-8 pt-20 lg:p-12 xl:pr-20
                    order-first lg:order-last">
            <div class="relative w-full max-w-lg" style="padding: 20px;">

                {{-- Outer frame (double border vintage style) --}}
                <div style="
                    position:absolute; inset:0;
                    border: 1px solid var(--v-copper);
                    pointer-events:none; z-index:2;
                "></div>
                <div style="
                    position:absolute; inset:8px;
                    border: 1px solid rgba(176,137,104,0.35);
                    pointer-events:none; z-index:2;
                "></div>

                {{-- Corner accents (larger, bolder) --}}
                <div
                    style="position:absolute;top:-4px;left:-4px;width:28px;height:28px;border-top:3px solid var(--v-copper);border-left:3px solid var(--v-copper);z-index:3;">
                </div>
                <div
                    style="position:absolute;top:-4px;right:-4px;width:28px;height:28px;border-top:3px solid var(--v-copper);border-right:3px solid var(--v-copper);z-index:3;">
                </div>
                <div
                    style="position:absolute;bottom:-4px;left:-4px;width:28px;height:28px;border-bottom:3px solid var(--v-copper);border-left:3px solid var(--v-copper);z-index:3;">
                </div>
                <div
                    style="position:absolute;bottom:-4px;right:-4px;width:28px;height:28px;border-bottom:3px solid var(--v-copper);border-right:3px solid var(--v-copper);z-index:3;">
                </div>

                {{-- Hard offset shadow box --}}
                <div style="
                    position:absolute; inset:0;
                    transform: translate(8px, 8px);
                    background: var(--v-copper); opacity: 0.25;
                    z-index:0;
                "></div>

                {{-- Image --}}
                <div class="relative overflow-hidden" style="aspect-ratio:4/3; z-index:1;">
                    <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&q=80&w=1200"
                        alt="Barber Tools" style="width:100%;height:100%;object-fit:cover;
                               filter: grayscale(0.4) contrast(1.1) sepia(0.15);
                               transition: filter 0.5s ease;"
                        onmouseover="this.style.filter='grayscale(0) contrast(1) sepia(0)'"
                        onmouseout="this.style.filter='grayscale(0.4) contrast(1.1) sepia(0.15)'">
                    {{-- Vintage color wash --}}
                    <div
                        style="position:absolute;inset:0;background:rgba(107,74,46,0.12);mix-blend-mode:multiply;pointer-events:none;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Vertical text sidebar --}}
        <div style="
            position:absolute; left:24px; bottom:40px;
            writing-mode:vertical-rl; text-orientation:mixed;
            font-size:9px; font-weight:600; letter-spacing:5px; text-transform:uppercase;
            color: var(--v-muted); opacity:0.5;
            display: none;
        " class="lg:block">
            Classic Cut Barbershop &mdash; Da Nang
        </div>
    </section>

    {{-- ── SCROLL INDICATOR ──────────────────────── --}}
    <div
        style="height:72px; background:var(--v-cream); display:flex; align-items:center; justify-content:center; border-top:1px solid var(--v-rule);">
        <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="scroll-dot" style="width:6px;height:6px;border-radius:50%;background:var(--v-copper);"></div>
            <p class="v-label">Khám phá thêm</p>
        </div>
    </div>


    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║ SERVICES SECTION ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <section id="services" style="background:var(--v-cream);">
        <div style="max-width:1380px; margin:0 auto; padding:80px 24px 100px;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:64px;">
                <div class="v-ornament" style="max-width:480px;margin:0 auto 20px;">
                    Dịch Vụ
                </div>
                <h2 class="v-title">Của Chúng Tôi</h2>
            </div>

            {{-- Grid --}}
            @php
                $services = \App\Models\Service::where('is_active', true)->get();
                $perCol = max(1, (int) ceil($services->count() / 3));
                $chunks = $services->chunk($perCol);
                $columnTitles = ['Cắt Tóc', 'Tạo Kiểu', 'Chăm Sóc'];
            @endphp

            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0; border:1px solid var(--v-rule);">
                @foreach($chunks as $index => $chunk)
                    <div
                        style="padding:40px 32px; {{ $index < 2 ? 'border-right:1px solid var(--v-rule);' : '' }} position:relative;">

                        {{-- Column header --}}
                        <div
                            style="text-align:center; margin-bottom:32px; padding-bottom:24px; border-bottom:1px solid var(--v-rule); position:relative;">
                            <div class="v-ornament">{{ $columnTitles[$index] ?? 'Dịch Vụ' }}</div>
                        </div>

                        {{-- Service rows --}}
                        @foreach($chunk as $service)
                            <div class="svc-row" data-img="{{ $service->image ? Storage::url($service->image) : '' }}"
                                data-name="{{ $service->name }}" data-price="{{ number_format($service->price, 0, ',', '.') }}đ">
                                <span class="service-name">{{ $service->name }}</span>
                                <span class="service-price" style="font-family:var(--font-serif)">
                                    {{ number_format($service->price, 0, ',', '.') }}đ
                                </span>
                            </div>
                            @if($service->description)
                                <p style="font-size:12px;color:var(--v-muted);margin:-4px 0 8px 0;line-height:1.6;">
                                    {{ Str::limit($service->description, 80) }}
                                </p>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- CTA --}}
            <div
                style="margin-top:64px; text-align:center; display:flex;flex-direction:column;align-items:center;gap:24px;">
                <div class="v-ornament" style="max-width:360px; margin:0 auto;">
                    Sẵn sàng cho một diện mạo mới?
                </div>
                <a href="{{ route('client.booking.create') }}" class="v-btn-primary">Đặt Lịch Ngay</a>
            </div>
        </div>
    </section>


    {{-- ── FLOATING IMAGE PANEL ──────────────────── --}}
    <div id="svcImgPanel">
        <div id="svcPanelImg"></div>
        <div id="svcPanelMeta">
            <div id="svcPanelName"></div>
            <div id="svcPanelPrice"></div>
        </div>
        {{-- Corner accents --}}
        <div
            style="position:absolute;top:0;left:0;width:12px;height:12px;border-top:1.5px solid var(--v-copper);border-left:1.5px solid var(--v-copper);">
        </div>
        <div
            style="position:absolute;bottom:0;right:0;width:12px;height:12px;border-bottom:1.5px solid var(--v-copper);border-right:1.5px solid var(--v-copper);">
        </div>
    </div>


    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║ STORY SECTION (Dark) ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <section id="story" class="story-dark v-noise">
        <div style="max-width:1160px; margin:0 auto; padding:80px 32px 96px;">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:center;">

                {{-- Text side --}}
                <div style="position:relative; z-index:2;">
                    <div class="v-ornament" style="justify-content:flex-start;margin-bottom:28px;">
                        Câu Chuyện Của Chúng Tôi
                    </div>
                    <h2 class="v-title v-title-light" style="margin-bottom:28px;">
                        Nơi Nghệ Thuật<br>
                        <em style="font-style:italic;font-weight:400;color:var(--v-copper)">Gặp Gỡ</em> Phong Cách.
                    </h2>
                    <p style="color:rgba(245,240,232,0.65);line-height:1.85;margin-bottom:16px;font-size:15px;">
                        Classic Cut ra đời từ niềm đam mê với nghệ thuật cắt tóc truyền thống. Chúng tôi tin rằng
                        mỗi lần cắt tóc là một trải nghiệm — không chỉ là dịch vụ.
                    </p>
                    <p style="color:rgba(245,240,232,0.65);line-height:1.85;margin-bottom:36px;font-size:15px;">
                        Đội ngũ thợ cắt được đào tạo bài bản, sử dụng kỹ thuật truyền thống kết hợp công cụ hiện
                        đại để mang lại kết quả hoàn hảo nhất.
                    </p>
                    <a href="{{ route('client.barbers.index') }}" style="font-size:10px;font-weight:600;letter-spacing:3px;text-transform:uppercase;
                               color:var(--v-copper);text-decoration:none;
                               border-bottom:1px solid rgba(176,137,104,0.4);padding-bottom:3px;
                               transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--v-copper)'"
                        onmouseout="this.style.borderColor='rgba(176,137,104,0.4)'">
                        Gặp gỡ đội ngũ &rarr;
                    </a>
                </div>

                {{-- Image side --}}
                <div style="position:relative; display:flex; justify-content:center; z-index:2;">
                    @php
                        $storyImage = \App\Models\Service::where('is_active', true)->whereNotNull('image')->skip(1)->first()
                            ?? \App\Models\Service::where('is_active', true)->whereNotNull('image')->first();
                    @endphp
                    <div style="position:relative; max-width:340px; width:100%;">
                        {{-- Hard offset shadow --}}
                        <div
                            style="position:absolute;inset:0;transform:translate(10px,10px);background:var(--v-copper);opacity:0.3;">
                        </div>

                        <div
                            style="position:relative; aspect-ratio:3/4; overflow:hidden; border:1px solid rgba(176,137,104,0.4);">
                            @if($storyImage && $storyImage->image)
                                <img src="{{ Storage::url($storyImage->image) }}" alt="{{ $storyImage->name }}" style="width:100%;height:100%;object-fit:cover;
                                               filter:grayscale(1) contrast(1.05) sepia(0.1);
                                               transition: filter 0.7s ease;"
                                    onmouseover="this.style.filter='grayscale(0) contrast(1) sepia(0)'"
                                    onmouseout="this.style.filter='grayscale(1) contrast(1.05) sepia(0.1)'">
                            @else
                                <div
                                    style="width:100%;height:100%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;">
                                    <p
                                        style="font-family:var(--font-display);font-size:48px;color:rgba(255,255,255,0.1);font-weight:300;letter-spacing:4px;text-align:center;line-height:1.2;">
                                        Classic<br>Cut
                                    </p>
                                </div>
                            @endif
                            {{-- Corner accents --}}
                            <div
                                style="position:absolute;top:8px;left:8px;width:16px;height:16px;border-top:2px solid var(--v-copper);border-left:2px solid var(--v-copper);">
                            </div>
                            <div
                                style="position:absolute;top:8px;right:8px;width:16px;height:16px;border-top:2px solid var(--v-copper);border-right:2px solid var(--v-copper);">
                            </div>
                            <div
                                style="position:absolute;bottom:8px;left:8px;width:16px;height:16px;border-bottom:2px solid var(--v-copper);border-left:2px solid var(--v-copper);">
                            </div>
                            <div
                                style="position:absolute;bottom:8px;right:8px;width:16px;height:16px;border-bottom:2px solid var(--v-copper);border-right:2px solid var(--v-copper);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║ BARBERS SECTION ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <section style="background:var(--v-parchment);">
        <div style="max-width:1200px; margin:0 auto; padding:80px 32px 96px;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:56px;">
                <div class="v-ornament" style="max-width:320px; margin:0 auto 16px;">
                    Đội ngũ
                </div>
                <h2 class="v-title">Thợ Cắt Của Chúng Tôi</h2>
            </div>

            {{-- Grid --}}
            @php
                $barbers = \App\Models\Barber::with('user')->where('is_active', true)->take(3)->get();
            @endphp

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:48px 32px;">
                @foreach($barbers as $barber)
                    <a href="{{ route('client.barbers.show', $barber) }}" class="barber-card"
                        style="text-align:center; text-decoration:none;">
                        <div class="barber-img-wrap">
                            @if($barber->user->avatar)
                                <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}">
                            @else
                                <div
                                    style="width:100%;height:100%;background:var(--v-surface);display:flex;align-items:center;justify-content:center;">
                                    <span class="material-symbols-outlined"
                                        style="font-size:52px;color:var(--v-muted);">person</span>
                                </div>
                            @endif
                        </div>
                        <h3
                            style="font-family:var(--font-serif);font-size:20px;font-weight:700;color:var(--v-ink);margin-bottom:4px;transition:color 0.2s;">
                            {{ $barber->user->name }}
                        </h3>
                        <p
                            style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:8px;">
                            {{ $barber->experience_years }} năm kinh nghiệm
                        </p>
                        @if($barber->rating > 0)
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                <span class="material-symbols-outlined fill"
                                    style="font-size:14px;color:var(--v-copper);">star</span>
                                <span
                                    style="font-size:13px;font-weight:600;color:var(--v-ink);">{{ number_format($barber->rating, 1) }}</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div style="text-align:center; margin-top:48px;">
                <a href="{{ route('client.barbers.index') }}" class="v-btn-outline">
                    Xem Tất Cả Thợ Cắt
                </a>
            </div>
        </div>
    </section>


    {{-- ╔══════════════════════════════════════════════╗ --}}
    {{-- ║ CTA BANNER ║ --}}
    {{-- ╚══════════════════════════════════════════════╝ --}}
    <section class="cta-banner">
        <div style="max-width:720px; margin:0 auto; padding:80px 32px; text-align:center; position:relative; z-index:1;">
            <div class="v-ornament" style="max-width:280px;margin:0 auto 20px;">
                Hôm nay
            </div>
            <h2 style="font-family:var(--font-serif);font-size:clamp(2rem,4vw,3.5rem);font-weight:700;
                       color:#fff;margin-bottom:16px;line-height:1.15;">
                Sẵn Sàng Đặt Lịch?
            </h2>
            <p
                style="color:rgba(255,255,255,0.8);font-size:15px;line-height:1.7;margin-bottom:40px;max-width:400px;margin-left:auto;margin-right:auto;">
                Chọn dịch vụ, chọn thợ cắt yêu thích, và đặt lịch ngay hôm nay.
            </p>
            <a href="{{ route('client.booking.create') }}" style="display:inline-flex;align-items:center;justify-content:center;
                       height:54px;padding:0 40px;
                       background:var(--v-ink); color:var(--v-cream);
                       font-size:10px;font-weight:600;letter-spacing:3px;text-transform:uppercase;
                       text-decoration:none;
                       border:1px solid var(--v-ink);
                       box-shadow: 4px 4px 0 rgba(28,23,19,0.4);
                       transition: transform 0.15s, box-shadow 0.15s;"
                onmouseover="this.style.transform='translate(-2px,-2px)';this.style.boxShadow='6px 6px 0 rgba(28,23,19,0.4)'"
                onmouseout="this.style.transform='';this.style.boxShadow='4px 4px 0 rgba(28,23,19,0.4)'">
                Đặt Lịch Ngay
            </a>
        </div>
    </section>


    {{-- ── FLOATING PANEL SCRIPT ─────────────────── --}}
    <script>
        (function () {
            const panel = document.getElementById('svcImgPanel');
            const panelImg = document.getElementById('svcPanelImg');
            const panelName = document.getElementById('svcPanelName');
            const panelPrice = document.getElementById('svcPanelPrice');

            document.querySelectorAll('.svc-row').forEach(row => {
                row.addEventListener('mouseenter', function () {
                    const img = this.dataset.img;
                    if (!img) return;
                    panelImg.style.backgroundImage = `url('${img}')`;
                    panelName.textContent = this.dataset.name || '';
                    panelPrice.textContent = this.dataset.price || '';
                    panel.classList.add('active');
                });
                row.addEventListener('mousemove', function (e) {
                    const x = e.clientX + 24;
                    const y = e.clientY - 90;
                    panel.style.left = Math.min(x, window.innerWidth - 220) + 'px';
                    panel.style.top = Math.max(10, y) + 'px';
                });
                row.addEventListener('mouseleave', function () {
                    panel.classList.remove('active');
                });
            });
        })();
    </script>

@endsection