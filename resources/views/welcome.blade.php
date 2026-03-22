@extends('layouts.client')

@section('title', 'Trang chủ')

@section('content')
    {{-- Hero Section --}}
    <section class="relative w-full min-h-[90vh] flex flex-col lg:flex-row noise-bg">
        {{-- Left: Hero Image --}}
        @php $heroService = \App\Models\Service::where('is_active', true)->whereNotNull('image')->first(); @endphp
        <div class="hidden lg:block absolute top-0 left-0 w-1/2 h-full z-0 overflow-hidden">
            <div class="w-full h-[120%] -mt-[10%] relative">
                @if($heroService && $heroService->image)
                    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter grayscale contrast-[1.1]"
                        style="background-image: url('{{ Storage::url($heroService->image) }}');"></div>
                @else
                    <div class="absolute inset-0 bg-warm-gray/[0.06]"></div>
                @endif
                {{-- Sepia overlay --}}
                <div class="absolute inset-0 bg-[#6b4a2e] opacity-[0.15] mix-blend-color"></div>
                {{-- Gradient fade to right --}}
                <div
                    class="absolute inset-0 bg-gradient-to-l from-bg-light via-bg-light/60 to-transparent w-40 right-0 left-auto">
                </div>
            </div>
        </div>

        {{-- Right: Typography --}}
        <div
            class="flex flex-col justify-center px-6 pt-24 pb-16 md:px-12 lg:px-24 lg:pt-0 z-10 lg:w-1/2 ml-auto bg-bg-light/90 lg:bg-transparent">
            <div class="max-w-xl mx-auto lg:mx-0 lg:mr-auto lg:pl-16 xl:pl-28 flex flex-col gap-10 md:gap-12">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-8 h-[1.5px] bg-primary"></div>
                        <span class="text-[10px] font-semibold tracking-[4px] uppercase text-warm-gray-light">Est. 2024
                            &mdash; Da Nang</span>
                    </div>
                    <h1
                        class="font-serif text-5xl md:text-[68px] lg:text-[76px] font-bold leading-[1.02] tracking-[-0.015em] text-warm-gray">
                        Nghệ Thuật<br />Cắt Tóc Nam.
                    </h1>
                    <p class="text-base md:text-lg font-normal text-warm-gray-light leading-[1.7] max-w-sm">
                        Trải nghiệm tiệm cắt tóc vintage cao cấp. Sự kết hợp giữa nghệ thuật truyền thống và phong cách hiện
                        đại.
                    </p>
                </div>
                <div class="flex items-center gap-8">
                    <a href="{{ route('client.booking.create') }}"
                        class="group relative flex items-center justify-center h-[52px] w-[200px] bg-primary text-white text-[11px] font-bold uppercase tracking-[2.5px] transition-all duration-300 hover:bg-warm-gray overflow-hidden">
                        <span class="relative z-10 transition-transform duration-300 group-hover:-translate-y-10">Đặt Lịch
                            Ngay</span>
                        <span
                            class="absolute z-10 transition-transform duration-300 translate-y-10 group-hover:translate-y-0">Đặt
                            Lịch Ngay</span>
                    </a>
                    <a href="#services"
                        class="text-[11px] font-semibold tracking-[2px] uppercase text-warm-gray-light hover:text-primary transition-colors border-b border-warm-gray-light/40 pb-0.5">Xem
                        Dịch Vụ</a>
                </div>
            </div>
        </div>

        {{-- Mobile Image --}}
        <div class="lg:hidden w-full h-[400px] relative order-first">
            @if($heroService && $heroService->image)
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter grayscale contrast-[1.1]"
                    style="background-image: url('{{ Storage::url($heroService->image) }}');"></div>
            @else
                <div class="absolute inset-0 bg-warm-gray/[0.06]"></div>
            @endif
            <div class="absolute inset-0 bg-[#6b4a2e] opacity-[0.15] mix-blend-color"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-bg-light/80 via-transparent to-bg-light"></div>
        </div>
    </section>

    {{-- Scroll Indicator --}}
    <div class="h-24 bg-bg-light relative z-20 flex items-center justify-center border-t border-warm-gray/10">
        <div class="flex flex-col items-center gap-4">
            <div class="w-[1px] h-12 bg-warm-gray/20"></div>
            <p class="text-warm-gray-light font-medium tracking-[4px] uppercase text-[10px]">Khám phá thêm</p>
        </div>
    </div>

    {{-- Services Section --}}
    <section id="services" class="bg-bg-light">
        <div class="w-full max-w-[1400px] mx-auto px-4 md:px-10 lg:px-20 py-16 md:py-24">
            {{-- Section Header --}}
            <div class="text-center mb-16 md:mb-24 relative">
                <div aria-hidden="true" class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-muted/30"></div>
                </div>
                <div class="relative flex justify-center">
                    <h2
                        class="bg-bg-light px-8 text-4xl md:text-5xl lg:text-6xl font-light tracking-tight text-warm-gray font-display">
                        Dịch Vụ Của Chúng Tôi</h2>
                </div>
            </div>

            {{-- Services Grid 3 columns - from DB --}}
            @php
                $services = \App\Models\Service::where('is_active', true)->get();
                $perCol = max(1, (int) ceil($services->count() / 3));
                $chunks = $services->chunk($perCol);
                $columnTitles = ['Cắt Tóc', 'Tạo Kiểu', 'Chăm Sóc'];
            @endphp

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-0 gap-y-12 border-t border-muted/20 md:border-t-0">
                @foreach($chunks as $index => $chunk)
                    <div
                        class="flex flex-col {{ $index < 2 ? 'lg:border-r' : '' }} border-muted/20 px-4 md:px-8 py-8 {{ $index > 0 ? 'border-t md:border-t-0' : '' }}">
                        <h3 class="text-xl font-bold mb-8 uppercase tracking-widest text-primary text-center">
                            {{ $columnTitles[$index] ?? 'Dịch Vụ' }}</h3>
                        <div class="flex flex-col gap-2">
                            @foreach($chunk as $service)
                                <div class="group cursor-pointer flex justify-between items-end py-4 border-b border-muted/10 hover:bg-surface rounded-sm transition-colors duration-300 px-2 -mx-2 leader-line svc-row"
                                    data-img="{{ $service->image ? Storage::url($service->image) : '' }}"
                                    data-name="{{ $service->name }}"
                                    data-price="{{ number_format($service->price, 0, ',', '.') }}d">
                                    <span
                                        class="service-name text-base font-medium pr-2 bg-bg-light group-hover:bg-surface transition-colors duration-300">{{ $service->name }}</span>
                                    <span
                                        class="service-price text-base font-bold pl-2 bg-bg-light group-hover:bg-surface transition-colors duration-300">{{ number_format($service->price, 0, ',', '.') }}d</span>
                                </div>
                                @if($service->description)
                                    <p class="text-sm text-muted mt-1 mb-2 pr-8">{{ Str::limit($service->description, 80) }}</p>
                                @endif
                            @endforeach
                        </div>
                        {{-- Column image from first service with image --}}

                    </div>
                @endforeach
            </div>
{{-- ===== FLOATING IMAGE PANEL ===== --}}
        <div id="svcImgPanel" style="
            position: fixed; pointer-events: none; opacity: 0; z-index: 50;
            width: 180px; background: #2a2118; overflow: hidden;
            transition: opacity 0.25s ease, transform 0.25s ease;
            transform: translateY(8px);
        ">
            <div id="svcPanelImg" style="
                height: 200px; background-size: cover; background-position: center;
                filter: grayscale(100%) contrast(1.05); position: relative;
            ">
                <div style="position:absolute;inset:0;background:rgba(107,74,46,0.18);mix-blend-mode:multiply;"></div>
            </div>
            <div id="svcPanelName" style="padding:8px 10px 2px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.55);"></div>
            <div id="svcPanelPrice" style="padding:0 10px 10px;font-size:12px;font-weight:500;color:#c8956a;letter-spacing:0.05em;"></div>
            {{-- corner accents --}}
            <div style="position:absolute;top:0;left:0;width:10px;height:10px;border-top:1.5px solid #c8956a;border-left:1.5px solid #c8956a;"></div>
            <div style="position:absolute;bottom:0;right:0;width:10px;height:10px;border-bottom:1.5px solid #c8956a;border-right:1.5px solid #c8956a;"></div>
        </div>

        <script>
        (function(){
            const panel   = document.getElementById('svcImgPanel');
            const panelImg  = document.getElementById('svcPanelImg');
            const panelName = document.getElementById('svcPanelName');
            const panelPrice= document.getElementById('svcPanelPrice');

            document.querySelectorAll('.svc-row').forEach(row => {
                row.addEventListener('mouseenter', function(e) {
                    const img = this.dataset.img;
                    if (!img) return;
                    panelImg.style.backgroundImage = `url('${img}')`;
                    panelName.textContent  = this.dataset.name  || '';
                    panelPrice.textContent = this.dataset.price || '';
                    panel.style.opacity   = '1';
                    panel.style.transform = 'translateY(0)';
                });
                row.addEventListener('mousemove', function(e) {
                    const x = e.clientX + 20;
                    const y = e.clientY - 80;
                    panel.style.left = Math.min(x, window.innerWidth - 200) + 'px';
                    panel.style.top  = Math.max(10, y) + 'px';
                });
                row.addEventListener('mouseleave', function() {
                    panel.style.opacity   = '0';
                    panel.style.transform = 'translateY(8px)';
                });
            });
        })();
        </script>
        {{-- ===== END FLOATING IMAGE PANEL ===== --}}
            {{-- CTA --}}
            <div class="mt-24 pt-12 border-t border-muted/20 text-center">
                <h2 class="text-2xl font-light mb-8 font-display">Sẵn sàng cho một diện mạo mới?</h2>
                <a href="{{ route('client.booking.create') }}"
                    class="inline-flex items-center justify-center h-14 px-8 bg-warm-gray text-bg-light text-sm font-bold uppercase tracking-widest transition-all duration-300 hover:bg-primary hover:text-white">
                    Đặt Lịch Ngay
                </a>
            </div>
        </div>
    </section>

    {{-- Story Section --}}
    <section id="story" class="bg-warm-gray text-bg-light noise-bg">
        <div class="max-w-[1200px] mx-auto px-6 md:px-12 lg:px-24 py-20 md:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-8 h-[1.5px] bg-primary"></div>
                        <span class="text-[10px] font-semibold tracking-[4px] uppercase text-bg-light/50">Câu chuyện</span>
                    </div>
                    <h2 class="font-serif text-4xl md:text-5xl font-bold leading-tight mb-8 text-bg-light">
                        Nơi Nghệ Thuật<br />Gặp Gỡ Phong Cách.
                    </h2>
                    <p class="text-base leading-[1.8] text-bg-light/70 mb-6">
                        Classic Cut ra đời từ niềm đam mê với nghệ thuật cắt tóc truyền thống. Chúng tôi tin rằng mỗi lần
                        cắt tóc là một trải nghiệm — không chỉ là dịch vụ.
                    </p>
                    <p class="text-base leading-[1.8] text-bg-light/70 mb-8">
                        Đội ngũ thợ cắt của chúng tôi được đào tạo bài bản, sử dụng kỹ thuật truyền thống kết hợp công cụ
                        hiện đại để mang lại kết quả hoàn hảo nhất.
                    </p>
                    <a href="{{ route('client.barbers.index') }}"
                        class="text-[11px] font-semibold tracking-[2px] uppercase text-bg-light/60 hover:text-primary transition-colors border-b border-bg-light/20 pb-0.5">
                        Gặp gỡ đội ngũ &rarr;
                    </a>
                </div>
                <div class="relative flex items-center justify-center">
                    @php $storyImage = \App\Models\Service::where('is_active', true)->whereNotNull('image')->skip(1)->first() ?? \App\Models\Service::where('is_active', true)->whereNotNull('image')->first(); @endphp
                    <div class="aspect-[3/4] w-full max-w-sm relative overflow-hidden">
                        @if($storyImage && $storyImage->image)
                            <img src="{{ Storage::url($storyImage->image) }}" alt="{{ $storyImage->name }}"
                                class="w-full h-full object-cover filter grayscale contrast-[1.1] opacity-90 hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                            <div class="absolute inset-0 bg-[#6b4a2e] opacity-[0.12] mix-blend-color"></div>
                        @else
                            <div class="w-full h-full bg-bg-light/[0.06] flex flex-col items-center justify-center text-center gap-4">
                                <p class="text-[10px] tracking-[4px] uppercase text-bg-light/30">Since 2024</p>
                                <p class="font-serif text-3xl text-bg-light/20 font-bold">Classic<br/>Cut</p>
                            </div>
                        @endif
                        {{-- Corner accents --}}
                        <div class="absolute top-3 left-3 w-6 h-6 border-t-2 border-l-2 border-primary/60"></div>
                        <div class="absolute top-3 right-3 w-6 h-6 border-t-2 border-r-2 border-primary/60"></div>
                        <div class="absolute bottom-3 left-3 w-6 h-6 border-b-2 border-l-2 border-primary/60"></div>
                        <div class="absolute bottom-3 right-3 w-6 h-6 border-b-2 border-r-2 border-primary/60"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Barbers Preview --}}
    <section class="bg-bg-light">
        <div class="max-w-[1200px] mx-auto px-6 md:px-12 lg:px-24 py-20 md:py-28">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="w-8 h-[1.5px] bg-primary"></div>
                    <span class="text-[10px] font-semibold tracking-[4px] uppercase text-warm-gray-light">Đội ngũ</span>
                    <div class="w-8 h-[1.5px] bg-primary"></div>
                </div>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-warm-gray">Thợ Cắt Của Chúng Tôi</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $barbers = \App\Models\Barber::with('user')->where('is_active', true)->take(3)->get();
                @endphp
                @foreach($barbers as $barber)
                    <a href="{{ route('client.barbers.show', $barber) }}" class="group block text-center">
                        <div
                            class="w-48 h-48 mx-auto rounded-full overflow-hidden mb-6 border-2 border-transparent group-hover:border-primary transition-all duration-500">
                            @if($barber->user->avatar)
                                <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                    class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition-all duration-700">
                            @else
                                <div class="w-full h-full bg-surface flex items-center justify-center">
                                    <span class="material-symbols-outlined text-5xl text-muted">person</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-warm-gray group-hover:text-primary transition-colors">
                            {{ $barber->user->name }}</h3>
                        <p class="text-sm text-warm-gray-light mt-1">{{ $barber->experience_years }} năm kinh nghiệm</p>
                        @if($barber->rating > 0)
                            <div class="flex items-center justify-center gap-1 mt-2">
                                <span class="material-symbols-outlined fill text-primary text-sm">star</span>
                                <span class="text-sm font-medium text-warm-gray">{{ number_format($barber->rating, 1) }}</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('client.barbers.index') }}"
                    class="text-[11px] font-semibold tracking-[2px] uppercase text-warm-gray-light hover:text-primary transition-colors border-b border-warm-gray-light/40 pb-0.5">Xem
                    tất cả thợ cắt &rarr;</a>
            </div>
        </div>
    </section>

    {{-- CTA Banner --}}
    <section class="bg-primary noise-bg">
        <div class="max-w-[800px] mx-auto px-6 py-20 md:py-24 text-center">
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-white mb-6">Sẵn Sàng Đặt Lịch?</h2>
            <p class="text-white/70 text-base mb-10 max-w-md mx-auto">Chọn dịch vụ, chọn thợ cắt yêu thích, và đặt lịch ngay
                hôm nay.</p>
            <a href="{{ route('client.booking.create') }}"
                class="inline-flex items-center justify-center h-14 px-10 bg-white text-primary text-sm font-bold uppercase tracking-widest transition-all duration-300 hover:bg-bg-light">
                Đặt Lịch Ngay
            </a>
        </div>
    </section>
@endsection