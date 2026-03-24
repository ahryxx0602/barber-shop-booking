@extends('layouts.client')

@section('title', 'Đặt lịch')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;padding:32px 16px 48px;" class="sm:px-6 lg:px-8"
    x-data="bookingWizard()">

    {{-- Header + Summary Bar --}}
    <div style="max-width:640px;margin:0 auto 20px;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;">
            <a href="{{ url()->previous() }}" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;color:var(--v-ink);margin-right:12px;border:1px solid var(--v-rule);transition:border-color 0.2s;"
                onmouseover="this.style.borderColor='var(--v-copper)'" onmouseout="this.style.borderColor='var(--v-rule)'">
                <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
            </a>
            <h1 class="v-title-sm" style="font-size:20px;">Đặt Lịch Hẹn</h1>
        </div>
        {{-- Mini progress --}}
        <div style="display:flex;align-items:center;gap:4px;">
            <div style="width:24px;height:3px;transition:background 0.3s;" :style="selectedServices.length > 0 ? 'background:var(--v-copper)' : 'background:var(--v-rule)'"></div>
            <div style="width:24px;height:3px;transition:background 0.3s;" :style="selectedBarber ? 'background:var(--v-copper)' : 'background:var(--v-rule)'"></div>
            <div style="width:24px;height:3px;transition:background 0.3s;" :style="selectedSlot ? 'background:var(--v-copper)' : 'background:var(--v-rule)'"></div>
            <div style="width:24px;height:3px;transition:background 0.3s;" :style="canSubmit ? 'background:var(--v-copper)' : 'background:var(--v-rule)'"></div>
        </div>
    </div>

    @if(session('success'))
        <div style="max-width:640px;margin:0 auto 20px;padding:16px;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);color:var(--v-copper-dk);font-size:14px;font-weight:500;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="max-width:640px;margin:0 auto 20px;padding:16px;border:1px solid #dc2626;background:rgba(220,38,38,0.06);color:#dc2626;font-size:14px;font-weight:500;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Booking Form --}}
    <form action="{{ route('client.booking.store') }}" method="POST" style="max-width:640px;margin:0 auto;">
        @csrf

        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);overflow:hidden;">

            {{-- ═══ STEP 1: Select Services ═══ --}}
            <div style="border-bottom:1px solid var(--v-rule);">
                {{-- Step Header (clickable to toggle) --}}
                <button type="button" @click="currentStep = currentStep === 1 ? 0 : 1"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:none;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="v-step-badge" :class="selectedServices.length > 0 ? 'active' : ''" style="width:24px;height:24px;font-size:10px;">1</span>
                        <span style="font-family:var(--font-serif);font-size:16px;font-weight:600;color:var(--v-ink);">Chọn dịch vụ</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span x-show="selectedServices.length > 0" x-cloak style="font-size:12px;color:var(--v-copper);font-weight:600;" x-text="selectedServices.length + ' dịch vụ · ' + formatPrice(totalPrice)"></span>
                        <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-muted);transition:transform 0.2s;" :style="currentStep === 1 ? 'transform:rotate(180deg)' : ''">expand_more</span>
                    </div>
                </button>
                {{-- Step Content --}}
                <div x-show="currentStep === 1" x-transition.duration.200ms>
                    <div style="padding:0 20px 20px;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($services as $service)
                            <label style="position:relative;display:flex;flex-direction:column;padding:12px;border:1px solid var(--v-rule);cursor:pointer;transition:all 0.2s;"
                                :style="selectedServices.includes({{ $service->id }})
                                    ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.06);box-shadow:2px 2px 0 var(--v-copper)'
                                    : ''">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" style="position:absolute;opacity:0;width:0;height:0;"
                                    @change="toggleService({{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})"
                                    :checked="selectedServices.includes({{ $service->id }})">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">
                                    <span style="font-weight:500;font-size:13px;transition:color 0.2s;padding-right:18px;" :style="selectedServices.includes({{ $service->id }}) ? 'color:var(--v-copper-dk)' : 'color:var(--v-ink)'">{{ $service->name }}</span>
                                    <span style="font-weight:600;font-family:var(--font-serif);font-size:13px;white-space:nowrap;transition:color 0.2s;" :style="selectedServices.includes({{ $service->id }}) ? 'color:var(--v-copper)' : 'color:var(--v-ink)'">{{ number_format($service->price, 0, ',', '.') }}đ</span>
                                </div>
                                <span style="font-size:11px;color:var(--v-muted);line-height:1.4;">{{ $service->duration_minutes }}p · {{ Str::limit($service->description, 40) }}</span>
                                <div style="position:absolute;top:8px;right:8px;" x-show="selectedServices.includes({{ $service->id }})">
                                    <span class="material-symbols-outlined fill" style="font-size:16px;color:var(--v-copper);">check_circle</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('service_ids')
                            <p style="color:#dc2626;font-size:12px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                        {{-- Auto-advance --}}
                        <button type="button" x-show="selectedServices.length > 0" @click="currentStep = 2"
                            style="margin-top:12px;width:100%;padding:10px;border:1px solid var(--v-copper);background:rgba(176,137,104,0.06);color:var(--v-copper-dk);font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                            onmouseover="this.style.background='var(--v-copper)';this.style.color='var(--v-cream)'" onmouseout="this.style.background='rgba(176,137,104,0.06)';this.style.color='var(--v-copper-dk)'">
                            Tiếp: Chọn thợ cắt
                            <span class="material-symbols-outlined" style="font-size:16px;">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 2: Choose Barber ═══ --}}
            <div style="border-bottom:1px solid var(--v-rule);" :style="selectedServices.length === 0 ? 'opacity:0.4;pointer-events:none' : ''">
                <button type="button" @click="if(selectedServices.length > 0) currentStep = currentStep === 2 ? 0 : 2"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:none;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="v-step-badge" :class="selectedBarber ? 'active' : ''" style="width:24px;height:24px;font-size:10px;">2</span>
                        <span style="font-family:var(--font-serif);font-size:16px;font-weight:600;color:var(--v-ink);">Chọn thợ cắt</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span x-show="selectedBarber" x-cloak style="font-size:12px;color:var(--v-copper);font-weight:600;" x-text="barberNames[selectedBarber] || ''"></span>
                        <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-muted);transition:transform 0.2s;" :style="currentStep === 2 ? 'transform:rotate(180deg)' : ''">expand_more</span>
                    </div>
                </button>
                <div x-show="currentStep === 2" x-transition.duration.200ms>
                    <div style="padding:0 20px 20px;">
                        {{-- Branch filter --}}
                        @if($branches->count() > 0)
                        <div style="margin-bottom:12px;">
                            <select x-model="selectedBranch" @change="filterBarberByBranch()"
                                style="width:100%;padding:8px 12px;border:1px solid var(--v-rule);background:#fff;font-size:12px;color:var(--v-ink);cursor:pointer;font-family:var(--font-body);outline:none;">
                                <option value="">Tất cả chi nhánh</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));gap:12px;">
                            @foreach($barbers as $barber)
                            <label x-show="!selectedBranch || selectedBranch == '{{ $barber->branch_id }}'"
                                style="display:flex;flex-direction:column;align-items:center;cursor:pointer;padding:10px 4px;border:1px solid transparent;transition:all 0.2s;"
                                :style="selectedBarber == {{ $barber->id }} ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.06);box-shadow:2px 2px 0 var(--v-copper)' : 'border-color:var(--v-rule)'"
                                class="group">
                                <input type="radio" name="barber_id" value="{{ $barber->id }}" style="position:absolute;opacity:0;width:0;height:0;"
                                    @change="selectBarber({{ $barber->id }}); currentStep = 3"
                                    :checked="selectedBarber == {{ $barber->id }}"
                                    {{ request('barber_id') == $barber->id ? 'checked' : '' }}>
                                <div style="width:64px;height:64px;overflow:hidden;margin-bottom:6px;flex-shrink:0;border:1px solid var(--v-rule);"
                                    :style="selectedBarber == {{ $barber->id }} ? 'border-color:var(--v-copper)' : ''">
                                    @if($barber->user->avatar)
                                        <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                            style="width:100%;height:100%;object-fit:cover;display:block;transition:filter 0.3s;"
                                            :style="selectedBarber == {{ $barber->id }} ? 'filter:none' : 'filter:grayscale(1);opacity:0.7'">
                                    @else
                                        <div style="width:100%;height:100%;background:var(--v-surface);display:flex;align-items:center;justify-content:center;">
                                            <span class="material-symbols-outlined" style="font-size:24px;color:var(--v-muted);">person</span>
                                        </div>
                                    @endif
                                </div>
                                <span style="font-size:11px;font-weight:500;text-align:center;line-height:1.3;transition:color 0.2s;"
                                    :style="selectedBarber == {{ $barber->id }} ? 'color:var(--v-copper-dk)' : 'color:var(--v-muted)'">{{ $barber->user->name }}</span>
                                @if($barber->rating > 0)
                                    <div style="display:flex;align-items:center;gap:2px;margin-top:2px;">
                                        <span class="material-symbols-outlined fill" style="font-size:10px;color:var(--v-copper);">star</span>
                                        <span style="font-size:10px;color:var(--v-muted);">{{ number_format($barber->rating, 1) }}</span>
                                    </div>
                                @endif
                                @if($barber->branch)
                                    <span style="font-size:9px;color:var(--v-copper);margin-top:2px;text-align:center;line-height:1.2;">{{ Str::limit($barber->branch->name, 15) }}</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @error('barber_id')
                            <p style="color:#dc2626;font-size:12px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 3: Date & Time ═══ --}}
            <div style="border-bottom:1px solid var(--v-rule);" :style="!selectedBarber ? 'opacity:0.4;pointer-events:none' : ''">
                <button type="button" @click="if(selectedBarber) currentStep = currentStep === 3 ? 0 : 3"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:none;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="v-step-badge" :class="selectedSlot ? 'active' : ''" style="width:24px;height:24px;font-size:10px;">3</span>
                        <span style="font-family:var(--font-serif);font-size:16px;font-weight:600;color:var(--v-ink);">Ngày & giờ</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span x-show="selectedSlot" x-cloak style="font-size:12px;color:var(--v-copper);font-weight:600;" x-text="selectedDate ? formatDateShort(selectedDate) + ' · ' + (selectedSlotLabel || '') : ''"></span>
                        <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-muted);transition:transform 0.2s;" :style="currentStep === 3 ? 'transform:rotate(180deg)' : ''">expand_more</span>
                    </div>
                </button>
                <div x-show="currentStep === 3" x-transition.duration.200ms>
                    <div style="padding:0 20px 20px;">
                        {{-- Date row - horizontal scroll --}}
                        <div style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Chọn ngày</div>
                        <div style="display:flex;gap:6px;margin-bottom:16px;overflow-x:auto;padding-bottom:4px;">
                            @for($i = 0; $i < 7; $i++)
                                @php
                                    $date = now()->addDays($i);
                                    $dateStr = $date->format('Y-m-d');
                                    $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                                @endphp
                                <button type="button" @click="selectDate('{{ $dateStr }}')"
                                    style="flex-shrink:0;width:60px;padding:8px 4px;text-align:center;border:1px solid var(--v-rule);transition:all 0.2s;cursor:pointer;background:transparent;"
                                    :style="selectedDate === '{{ $dateStr }}'
                                        ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.08);box-shadow:2px 2px 0 var(--v-copper)'
                                        : ''">
                                    <div style="font-size:9px;text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;" :style="selectedDate === '{{ $dateStr }}' ? 'color:var(--v-copper)' : 'color:var(--v-muted)'">{{ $dayNames[$date->dayOfWeek] }}</div>
                                    <div style="font-size:16px;font-weight:600;font-family:var(--font-serif);line-height:1.2;" :style="selectedDate === '{{ $dateStr }}' ? 'color:var(--v-copper-dk)' : 'color:var(--v-ink)'">{{ $date->format('d') }}</div>
                                    <div style="font-size:9px;" :style="selectedDate === '{{ $dateStr }}' ? 'color:var(--v-copper)' : 'color:var(--v-muted)'">Thg {{ $date->format('m') }}</div>
                                </button>
                            @endfor
                        </div>

                        {{-- Time Slots --}}
                        <div style="font-size:11px;font-weight:600;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Giờ trống</div>

                        <div x-show="loadingSlots" style="display:flex;align-items:center;justify-content:center;padding:24px 0;">
                            <div style="width:20px;height:20px;border:2px solid var(--v-rule);border-top-color:var(--v-copper);border-radius:50%;animation:spin 1s linear infinite;"></div>
                        </div>

                        <div x-show="!selectedDate && !loadingSlots" style="text-align:center;padding:24px 0;color:var(--v-muted);font-size:12px;">
                            ← Chọn ngày phía trên
                        </div>

                        <div x-show="selectedDate && !loadingSlots && slots.length === 0" style="text-align:center;padding:24px 0;">
                            <p style="color:var(--v-muted);font-size:12px;margin-bottom:12px;">Thợ đã kín lịch trong ngày này.</p>
                            @auth
                            <button type="button" @click="$refs.waitlistForm.submit()" class="v-btn-outline v-btn-sm" style="margin:0 auto;">
                                <span class="material-symbols-outlined" style="font-size:14px;">notifications_active</span>
                                Đăng ký nhận thông báo
                            </button>
                            @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(route('client.booking.create')) }}" class="v-btn-outline v-btn-sm" style="margin:0 auto;text-decoration:none;display:inline-flex;">
                                Đăng nhập để vào danh sách chờ
                            </a>
                            @endauth
                        </div>

                        <div x-show="!loadingSlots && slots.length > 0" style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;max-height:180px;overflow-y:auto;padding-right:4px;">
                            <template x-for="slot in slots" :key="slot.id">
                                <label style="cursor:pointer;">
                                    <input type="radio" name="time_slot_id" :value="slot.id" style="position:absolute;opacity:0;width:0;height:0;"
                                        @change="selectedSlot = slot.id; selectedSlotLabel = slot.label; @guest currentStep = 4 @else currentStep = 5 @endguest">
                                    <div style="padding:8px 4px;text-align:center;border:1px solid var(--v-rule);font-size:12px;transition:all 0.2s;"
                                        :style="selectedSlot == slot.id
                                            ? 'border-color:var(--v-copper);background:rgba(176,137,104,0.08);color:var(--v-copper-dk);font-weight:600;box-shadow:2px 2px 0 var(--v-copper)'
                                            : 'color:var(--v-ink)'"
                                        x-text="slot.label"></div>
                                </label>
                            </template>
                        </div>

                        @error('time_slot_id')
                            <p style="color:#dc2626;font-size:12px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ═══ STEP 4: Guest Info (non-auth only) ═══ --}}
            @guest
            <div style="border-bottom:1px solid var(--v-rule);" :style="!selectedSlot ? 'opacity:0.4;pointer-events:none' : ''">
                <button type="button" @click="if(selectedSlot) currentStep = currentStep === 4 ? 0 : 4"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:none;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="v-step-badge" :class="guestName && guestPhone && guestEmail ? 'active' : ''" style="width:24px;height:24px;font-size:10px;">4</span>
                        <span style="font-family:var(--font-serif);font-size:16px;font-weight:600;color:var(--v-ink);">Thông tin liên hệ</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span x-show="guestName" x-cloak style="font-size:12px;color:var(--v-copper);font-weight:600;" x-text="guestName"></span>
                        <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-muted);transition:transform 0.2s;" :style="currentStep === 4 ? 'transform:rotate(180deg)' : ''">expand_more</span>
                    </div>
                </button>
                <div x-show="currentStep === 4" x-transition.duration.200ms>
                    <div style="padding:0 20px 20px;">
                        <p style="font-size:12px;color:var(--v-muted);margin-bottom:12px;">Bạn chưa đăng nhập. Vui lòng điền thông tin liên hệ.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label for="guest_name" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Họ và tên <span style="color:var(--v-copper);">*</span></label>
                                <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required
                                    x-model="guestName" placeholder="Nguyễn Văn A" class="v-input" style="padding:10px 14px;font-size:13px;">
                                @error('guest_name')
                                    <p style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="guest_phone" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">SĐT <span style="color:var(--v-copper);">*</span></label>
                                <input type="tel" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" required
                                    x-model="guestPhone" placeholder="0901234567" class="v-input" style="padding:10px 14px;font-size:13px;">
                                @error('guest_phone')
                                    <p style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="guest_email" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Email <span style="color:var(--v-copper);">*</span></label>
                                <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" required
                                    x-model="guestEmail" placeholder="email@example.com" class="v-input" style="padding:10px 14px;font-size:13px;">
                                @error('guest_email')
                                    <p style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p style="font-size:10px;color:var(--v-muted);margin-top:10px;display:flex;align-items:center;gap:4px;">
                            <span class="material-symbols-outlined" style="font-size:12px;">info</span>
                            Đã có tài khoản? <a href="{{ route('login') }}?redirect={{ urlencode(route('client.booking.create')) }}" style="color:var(--v-copper);font-weight:600;text-decoration:none;">Đăng nhập</a>
                        </p>
                    </div>
                </div>
            </div>
            @endguest

            {{-- ═══ STEP 5: Tùy chọn bổ sung ═══ --}}
            <div :style="!selectedSlot ? 'opacity:0.4;pointer-events:none' : ''">
                <button type="button" @click="if(selectedSlot) currentStep = currentStep === 5 ? 0 : 5"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:none;border:none;cursor:pointer;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="v-step-badge" style="width:24px;height:24px;font-size:10px;">@auth 4 @else 5 @endauth</span>
                        <span style="font-family:var(--font-serif);font-size:16px;font-weight:600;color:var(--v-ink);">Tuỳ chọn bổ sung</span>
                        <span style="font-size:11px;color:var(--v-muted);font-style:italic;">không bắt buộc</span>
                    </div>
                    <span class="material-symbols-outlined" style="font-size:18px;color:var(--v-muted);transition:transform 0.2s;" :style="currentStep === 5 ? 'transform:rotate(180deg)' : ''">expand_more</span>
                </button>
                <div x-show="currentStep === 5" x-transition.duration.200ms>
                    <div style="padding:0 20px 16px;display:flex;flex-direction:column;gap:16px;">
                        {{-- Ghi chú --}}
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Ghi chú</label>
                            <textarea name="note" rows="2" placeholder="Yêu cầu đặc biệt, kiểu tóc mong muốn..."
                                class="v-textarea" style="font-size:13px;">{{ old('note') }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Mã giảm giá --}}
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Mã giảm giá</label>
                                <div style="display:flex;gap:8px;">
                                    <input type="text" name="coupon_code" x-model="couponCode" placeholder="Nhập mã (nếu có)" class="v-input" style="padding:10px 14px;font-size:13px;font-family:monospace;text-transform:uppercase;flex:1;">
                                    <button type="button" @click="applyCoupon" :disabled="loadingCoupon || !couponCode" class="v-btn-outline v-btn-sm" style="height:auto;padding:0 16px;">
                                        <span x-show="!loadingCoupon">Áp dụng</span>
                                        <span x-show="loadingCoupon" class="material-symbols-outlined" style="font-size:16px;animation:spin 1s linear infinite;">autorenew</span>
                                    </button>
                                </div>
                                <div x-show="couponMessage" style="margin-top:6px;font-size:12px;font-weight:500;"
                                     :style="couponError ? 'color:#dc2626;' : 'color:#15803d;'"
                                     x-text="couponMessage"></div>
                            </div>

                            {{-- Đặt lịch lặp lại --}}
                            @auth
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;">Lặp lại lịch</label>
                                <select name="recurring_frequency" class="v-input" style="padding:10px 14px;font-size:13px;background-color:#fff;">
                                    <option value="none">Không lặp lại</option>
                                    <option value="weekly">Hàng tuần</option>
                                    <option value="biweekly">Mỗi 2 tuần</option>
                                    <option value="monthly">Hàng tháng</option>
                                </select>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ STICKY FOOTER: Summary + Submit ═══ --}}
            <div style="padding:16px 20px;background:var(--v-surface);border-top:1px solid var(--v-rule);">
                {{-- Compact summary row --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:12px;font-size:12px;color:var(--v-muted);">
                        <span x-text="selectedServices.length + ' dịch vụ'">0 dịch vụ</span>
                        <span style="width:1px;height:12px;background:var(--v-rule);"></span>
                        <span x-text="totalDuration + ' phút'">0 phút</span>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;">
                        <span x-show="discountAmount > 0" style="font-size:12px;color:var(--v-muted);text-decoration:line-through;margin-bottom:2px;" x-text="formatPrice(totalPrice)">0đ</span>
                        <span style="font-family:var(--font-serif);font-weight:700;font-size:20px;color:var(--v-ink);" x-text="formatPrice(Math.max(0, totalPrice - discountAmount))">0đ</span>
                    </div>
                </div>
                <button type="submit" :disabled="!canSubmit" class="v-btn-primary"
                    style="width:100%;justify-content:center;height:48px;"
                    :style="canSubmit ? '' : 'opacity:0.35;cursor:not-allowed;pointer-events:none'">
                    Xác Nhận Đặt Lịch
                </button>
                <p style="text-align:center;font-size:10px;color:var(--v-muted);margin-top:8px;display:flex;align-items:center;justify-content:center;gap:4px;">
                    <span class="material-symbols-outlined" style="font-size:12px;">lock</span>
                    Thanh toán tại quầy sau khi sử dụng dịch vụ.
                </p>
            </div>
        </div>
    </form>
</section>

@auth
<form x-ref="waitlistForm" method="POST" action="{{ route('client.waitlist.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="barber_id" :value="selectedBarber">
    <input type="hidden" name="desired_date" :value="selectedDate">
</form>
@endauth

@push('styles')
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script>
function bookingWizard() {
    return {
        currentStep: 1,
        selectedServices: [],
        servicePrices: {},
        serviceDurations: {},
        totalPrice: 0,
        totalDuration: 0,
        selectedBarber: {{ request('barber_id', 'null') }},
        selectedBranch: '',
        selectedDate: null,
        selectedSlot: null,
        selectedSlotLabel: null,
        slots: [],
        loadingSlots: false,
        isGuest: {{ auth()->check() ? 'false' : 'true' }},
        guestName: '{{ old("guest_name", "") }}',
        guestPhone: '{{ old("guest_phone", "") }}',
        guestEmail: '{{ old("guest_email", "") }}',
        couponCode: '{{ old("coupon_code", "") }}',
        discountAmount: 0,
        couponMessage: '',
        couponError: false,
        loadingCoupon: false,
        barberNames: {
            @foreach($barbers as $barber)
                {{ $barber->id }}: '{{ $barber->user->name }}',
            @endforeach
        },

        init() {
            if (this.selectedBarber) {
                this.currentStep = 3;
            }
            if (this.couponCode) {
                // Try applying if already populated by old input
                // wait for it to be rendered first 
            }
        },

        async applyCoupon() {
            if (!this.couponCode) return;
            if (this.totalPrice === 0) {
                this.couponMessage = 'Vui lòng chọn dịch vụ trước khi áp dụng mã.';
                this.couponError = true;
                return;
            }
            this.loadingCoupon = true;
            this.couponMessage = '';
            this.couponError = false;
            try {
                const res = await fetch(`{{ route('client.booking.apply-coupon') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        coupon_code: this.couponCode,
                        total_price: this.totalPrice
                    })
                });
                const data = await res.json();
                if (data.valid) {
                    this.discountAmount = data.discount_amount;
                    this.couponMessage = data.message + ` (Giảm ${this.formatPrice(data.discount_amount)})`;
                    this.couponError = false;
                } else {
                    this.discountAmount = 0;
                    this.couponMessage = data.message || 'Mã giảm giá không hợp lệ.';
                    this.couponError = true;
                }
            } catch (e) {
                this.couponMessage = 'Lỗi kết nối hoặc mã giảm giá sai.';
                this.couponError = true;
                this.discountAmount = 0;
            }
            this.loadingCoupon = false;
        },

        toggleService(id, price, duration) {
            const idx = this.selectedServices.indexOf(id);
            if (idx > -1) {
                this.selectedServices.splice(idx, 1);
                delete this.servicePrices[id];
                delete this.serviceDurations[id];
            } else {
                this.selectedServices.push(id);
                this.servicePrices[id] = price;
                this.serviceDurations[id] = duration;
            }
            this.totalPrice = Object.values(this.servicePrices).reduce((a, b) => a + b, 0);
            this.totalDuration = Object.values(this.serviceDurations).reduce((a, b) => a + b, 0);
            if (this.couponCode && Object.keys(this.servicePrices).length > 0) {
                this.applyCoupon();
            } else if (Object.keys(this.servicePrices).length === 0) {
                this.discountAmount = 0;
                this.couponMessage = '';
            }
        },

        selectBarber(id) {
            this.selectedBarber = id;
            this.selectedSlot = null;
            this.selectedSlotLabel = null;
            if (this.selectedDate) {
                this.fetchSlots();
            }
        },

        filterBarberByBranch() {
            // Nếu barber đang chọn không thuộc chi nhánh mới → reset
            if (this.selectedBarber && this.selectedBranch) {
                const barberBranches = {
                    @foreach($barbers as $barber)
                        {{ $barber->id }}: '{{ $barber->branch_id }}',
                    @endforeach
                };
                if (barberBranches[this.selectedBarber] != this.selectedBranch) {
                    this.selectedBarber = null;
                    this.selectedSlot = null;
                    this.selectedSlotLabel = null;
                }
            }
        },

        selectDate(date) {
            this.selectedDate = date;
            this.selectedSlot = null;
            this.selectedSlotLabel = null;
            if (this.selectedBarber) {
                this.fetchSlots();
            }
        },

        async fetchSlots() {
            this.loadingSlots = true;
            this.slots = [];
            try {
                const res = await fetch(`{{ route('client.booking.slots') }}?barber_id=${this.selectedBarber}&date=${this.selectedDate}`);
                this.slots = await res.json();
            } catch (e) {
                this.slots = [];
            }
            this.loadingSlots = false;
        },

        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        },

        formatDateShort(dateStr) {
            const d = new Date(dateStr);
            const days = ['CN','T2','T3','T4','T5','T6','T7'];
            return days[d.getDay()] + ' ' + String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0');
        },

        get canSubmit() {
            const baseReady = this.selectedServices.length > 0 && this.selectedBarber && this.selectedSlot;
            if (this.isGuest) {
                return baseReady && this.guestName.trim() !== '' && this.guestPhone.trim() !== '' && this.guestEmail.trim() !== '';
            }
            return baseReady;
        }
    }
}
</script>
@endpush
@endsection