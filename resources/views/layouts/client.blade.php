<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Classic Cut') - {{ config('app.name', 'Classic Cut') }}</title>

    {{-- Google Fonts: Vintage Design System --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── VINTAGE DESIGN TOKENS ─────────────────────── */
        :root {
            --v-cream: #f5f0e8;
            --v-parchment: #ede5d0;
            --v-ink: #1c1713;
            --v-ink-soft: #3d3228;
            --v-muted: #8a7a6a;
            --v-copper: #b08968;
            --v-copper-dk: #8b6340;
            --v-surface: #ede8df;
            --v-rule: rgba(176, 137, 104, 0.3);

            --font-serif: 'Playfair Display', Georgia, serif;
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'DM Sans', system-ui, sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--v-cream);
            color: var(--v-ink);
        }

        /* ── NOISE TEXTURE ──────────────────────────────── */
        .v-noise {
            position: relative;
        }
        .v-noise::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 128px;
        }

        /* ── ORNAMENT DIVIDER ───────────────────────────── */
        .v-ornament {
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--v-copper);
            font-size: 10px;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .v-ornament::before,
        .v-ornament::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--v-rule);
        }

        /* ── SECTION LABEL ──────────────────────────────── */
        .v-label {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--v-muted);
        }

        /* ── SECTION TITLE ──────────────────────────────── */
        .v-title {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 700;
            line-height: 1.1;
            color: var(--v-ink);
            letter-spacing: -0.01em;
        }
        .v-title-sm {
            font-family: var(--font-serif);
            font-size: clamp(1.25rem, 2.5vw, 1.75rem);
            font-weight: 700;
            line-height: 1.2;
            color: var(--v-ink);
        }
        .v-title-light { color: var(--v-cream); }

        /* ── VINTAGE BUTTONS ────────────────────────────── */
        .v-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 36px;
            height: 52px;
            background: var(--v-ink);
            color: var(--v-cream);
            font-family: var(--font-body);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-decoration: none;
            border: 1px solid var(--v-ink);
            box-shadow: 4px 4px 0 var(--v-copper);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            position: relative;
        }
        .v-btn-primary:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 var(--v-copper);
            color: var(--v-cream);
        }
        .v-btn-primary:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 var(--v-copper);
        }

        .v-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 32px;
            height: 52px;
            background: transparent;
            color: var(--v-ink);
            font-family: var(--font-body);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-decoration: none;
            border: 1px solid var(--v-ink);
            box-shadow: 3px 3px 0 var(--v-copper-dk);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s;
        }
        .v-btn-outline:hover {
            background: var(--v-ink);
            color: var(--v-cream);
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0 var(--v-copper-dk);
        }

        .v-btn-sm {
            height: 40px;
            padding: 0 24px;
            font-size: 9px;
            letter-spacing: 2.5px;
        }

        /* ── CORNER ACCENTS ─────────────────────────────── */
        .corner { position: absolute; width: 20px; height: 20px; }
        .corner-tl { top: 0; left: 0; border-top: 2px solid var(--v-copper); border-left: 2px solid var(--v-copper); }
        .corner-tr { top: 0; right: 0; border-top: 2px solid var(--v-copper); border-right: 2px solid var(--v-copper); }
        .corner-bl { bottom: 0; left: 0; border-bottom: 2px solid var(--v-copper); border-left: 2px solid var(--v-copper); }
        .corner-br { bottom: 0; right: 0; border-bottom: 2px solid var(--v-copper); border-right: 2px solid var(--v-copper); }

        /* ── VINTAGE CARD ───────────────────────────────── */
        .v-card {
            background: #fff;
            border: 1px solid var(--v-rule);
            box-shadow: 4px 4px 0 var(--v-copper);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .v-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 var(--v-copper);
        }

        /* ── VINTAGE INPUT ──────────────────────────────── */
        .v-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--v-rule);
            background: #fff;
            color: var(--v-ink);
            font-family: var(--font-body);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .v-input:focus {
            border-color: var(--v-copper);
            box-shadow: 0 0 0 1px var(--v-copper);
        }
        .v-input::placeholder {
            color: var(--v-muted);
        }

        /* ── VINTAGE TEXTAREA ───────────────────────────── */
        .v-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--v-rule);
            background: #fff;
            color: var(--v-ink);
            font-family: var(--font-body);
            font-size: 14px;
            outline: none;
            resize: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .v-textarea:focus {
            border-color: var(--v-copper);
            box-shadow: 0 0 0 1px var(--v-copper);
        }

        /* ── GRAYSCALE IMAGE EFFECT ─────────────────────── */
        .v-img-grayscale {
            filter: grayscale(1) contrast(1.05);
            transition: filter 0.6s ease;
        }
        .v-img-grayscale:hover,
        .group:hover .v-img-grayscale {
            filter: grayscale(0) contrast(1);
        }

        /* ── STEP BADGE ─────────────────────────────────── */
        .v-step-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid var(--v-rule);
            color: var(--v-muted);
            background: var(--v-surface);
        }
        .v-step-badge.active {
            background: var(--v-ink);
            color: var(--v-cream);
            border-color: var(--v-ink);
            box-shadow: 2px 2px 0 var(--v-copper);
        }

        /* ── NAVBAR ─────────────────────────────────────── */
        .v-navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            background: var(--v-cream);
            border-bottom: 1px solid var(--v-rule);
            height: 72px;
            display: flex;
            align-items: center;
        }
        .v-nav-link {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--v-muted);
            text-decoration: none;
            transition: color 0.2s;
            padding: 4px 0;
            border-bottom: 1px solid transparent;
        }
        .v-nav-link:hover {
            color: var(--v-copper);
            border-bottom-color: var(--v-copper);
        }

        /* ── FOOTER ─────────────────────────────────────── */
        .v-footer {
            background: var(--v-ink);
            color: var(--v-cream);
            position: relative;
            overflow: hidden;
        }
        .v-footer a {
            color: rgba(245,240,232,0.7);
            text-decoration: none;
            transition: color 0.2s;
        }
        .v-footer a:hover {
            color: var(--v-copper);
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- Navigation --}}
    <nav class="v-navbar" id="main-nav">
        <div class="w-full max-w-[1600px] mx-auto px-6 md:px-12 lg:px-24 flex justify-between items-center">
            {{-- Logo --}}
            <a href="{{ url('/') }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                <div style="width:16px;height:16px;color:var(--v-copper);">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                            fill="currentColor"></path>
                    </svg>
                </div>
                <span style="font-family:var(--font-serif);font-size:15px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--v-ink);">Classic Cut</span>
            </a>

            {{-- Desktop Links --}}
            <div class="hidden md:flex items-center" style="gap:32px;">
                <a class="v-nav-link" href="{{ url('/') }}#services">Dịch vụ</a>
                <a class="v-nav-link" href="{{ url('/') }}#story">Câu chuyện</a>
                <a class="v-nav-link" href="{{ route('client.barbers.index') }}">Thợ cắt</a>
                <a class="v-nav-link" href="{{ route('client.branches.index') }}">Chi nhánh</a>
                <a class="v-nav-link" href="{{ route('client.shop.index') }}">Cửa hàng</a>
                {{-- Cart Icon --}}
                @php $cartCount = array_sum(array_column(session('cart', []), 'quantity')); @endphp
                <a href="{{ route('client.cart') }}" style="position:relative;display:inline-flex;align-items:center;color:var(--v-muted);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">
                    <span class="material-symbols-outlined" style="font-size:22px;">shopping_cart</span>
                    <span class="cart-badge-count" style="display:{{ $cartCount > 0 ? 'flex' : 'none' }};position:absolute;top:-6px;right:-8px;width:18px;height:18px;border-radius:50%;background:var(--v-copper);color:#fff;font-size:9px;font-weight:700;align-items:center;justify-content:center;">{{ $cartCount }}</span>
                </a>
                @auth
                    <a class="v-nav-link" href="{{ route('client.profile.show') }}" style="display:inline-flex;align-items:center;gap:8px;">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid var(--v-rule);" alt="" />
                        @else
                            <span style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--v-copper);color:#fff;font-size:11px;font-weight:700;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                        Tài khoản
                    </a>
                @else
                    <a class="v-nav-link" href="{{ route('login') }}">Đăng nhập</a>
                @endauth
                <a href="{{ route('client.booking.create') }}" class="v-btn-primary v-btn-sm">Đặt lịch</a>
            </div>

            {{-- Mobile Menu --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" style="padding:8px;color:var(--v-ink);">
                    <span class="material-symbols-outlined" x-text="open ? 'close' : 'menu'">menu</span>
                </button>
                <div x-show="open" x-cloak @click.away="open = false" x-transition
                    style="position:absolute;top:72px;left:0;right:0;z-index:50;background:var(--v-cream);border-bottom:1px solid var(--v-rule);padding:24px;display:flex;flex-direction:column;gap:16px;">
                    <a class="v-nav-link" href="{{ url('/') }}#services">Dịch vụ</a>
                    <a class="v-nav-link" href="{{ url('/') }}#story">Câu chuyện</a>
                    <a class="v-nav-link" href="{{ route('client.barbers.index') }}">Thợ cắt</a>
                    <a class="v-nav-link" href="{{ route('client.branches.index') }}">Chi nhánh</a>
                    <a class="v-nav-link" href="{{ route('client.shop.index') }}">Cửa hàng</a>
                    <a class="v-nav-link" href="{{ route('client.cart') }}" style="display:flex;align-items:center;gap:6px;">
                        <span class="material-symbols-outlined" style="font-size:18px;">shopping_cart</span>
                        Giỏ hàng
                        @if($cartCount > 0)
                            <span class="cart-badge-count" style="display:flex;width:18px;height:18px;border-radius:50%;background:var(--v-copper);color:#fff;font-size:9px;font-weight:700;align-items:center;justify-content:center;">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        <a class="v-nav-link" href="{{ route('client.profile.show') }}" style="display:flex;align-items:center;gap:8px;">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid var(--v-rule);" alt="" />
                            @else
                                <span style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--v-copper);color:#fff;font-size:11px;font-weight:700;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            Tài khoản
                        </a>
                    @else
                        <a class="v-nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    @endauth
                    <a href="{{ route('client.booking.create') }}" class="v-btn-primary v-btn-sm" style="text-align:center;">Đặt lịch</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main style="padding-top:72px;">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="v-footer v-noise">
        <div style="max-width:1400px;margin:0 auto;padding:64px 24px 0;" class="md:px-12 lg:px-24">
            <div style="display:grid;gap:48px;margin-bottom:56px;" class="grid-cols-1 md:grid-cols-3">
                {{-- Brand --}}
                <div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
                        <div style="width:16px;height:16px;color:var(--v-copper);">
                            <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <span style="font-family:var(--font-serif);font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--v-cream);">Classic Cut</span>
                    </div>
                    <p style="font-size:14px;line-height:1.7;color:rgba(245,240,232,0.55);max-width:300px;">Tiệm cắt tóc nam phong cách vintage.
                        Nơi nghệ thuật và phong cách hội tụ.</p>
                </div>
                {{-- Quick Links --}}
                <div>
                    <h4 style="font-size:9px;font-weight:700;letter-spacing:4px;text-transform:uppercase;color:rgba(245,240,232,0.35);margin-bottom:24px;">Liên kết</h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;">
                        <li><a href="{{ url('/') }}#services" style="font-size:14px;">Dịch vụ</a></li>
                        <li><a href="{{ route('client.barbers.index') }}" style="font-size:14px;">Đội ngũ thợ cắt</a></li>
                        <li><a href="{{ route('client.branches.index') }}" style="font-size:14px;">Hệ thống chi nhánh</a></li>
                        <li><a href="{{ route('client.shop.index') }}" style="font-size:14px;">Cửa hàng sản phẩm</a></li>
                        <li><a href="{{ route('client.booking.create') }}" style="font-size:14px;">Đặt lịch ngay</a></li>
                    </ul>
                </div>
                {{-- Hours --}}
                <div>
                    <h4 style="font-size:9px;font-weight:700;letter-spacing:4px;text-transform:uppercase;color:rgba(245,240,232,0.35);margin-bottom:24px;">Giờ làm việc</h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;font-size:14px;color:rgba(245,240,232,0.7);">
                        <li style="display:flex;justify-content:space-between;"><span>Thu 2 - Thu 6</span><span>9:00 - 19:00</span></li>
                        <li style="display:flex;justify-content:space-between;"><span>Thu 7</span><span>9:00 - 17:00</span></li>
                        <li style="display:flex;justify-content:space-between;"><span>Chủ Nhật</span><span style="color:var(--v-copper);">Nghỉ</span></li>
                    </ul>
                </div>
            </div>
            <div style="border-top:1px solid rgba(245,240,232,0.08);padding:24px 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                <p style="font-size:12px;color:rgba(245,240,232,0.3);">&copy; {{ date('Y') }} Classic Cut Barbershop. Đồ án tốt nghiệp -
                    Phan Văn Thành.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>