@extends('layouts.client')

@section('title', 'Dat lich')

@section('content')
<section class="bg-bg-light min-h-screen py-12 px-4 sm:px-6 lg:px-8" x-data="bookingWizard()">
    {{-- Header --}}
    <div class="w-full max-w-[640px] mx-auto mb-8 flex items-center">
        <a href="{{ url()->previous() }}" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-surface transition-colors mr-4">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold font-display tracking-tight text-warm-gray">Dat Lich Hen</h1>
    </div>

    {{-- Booking Form --}}
    <form action="{{ route('client.booking.store') }}" method="POST" class="w-full max-w-[640px] mx-auto">
        @csrf

        <div class="bg-white border border-muted/20 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 space-y-10">

                {{-- Step 1: Select Services --}}
                <section>
                    <div class="flex items-center mb-5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold mr-3"
                            :class="selectedServices.length > 0 ? 'bg-primary text-white' : 'bg-surface text-warm-gray-light'">1</span>
                        <h2 class="text-lg font-semibold font-display text-warm-gray">Chon dich vu</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($services as $service)
                        <label class="relative flex flex-col p-4 border-2 cursor-pointer transition-all duration-200"
                            :class="selectedServices.includes({{ $service->id }})
                                ? 'border-primary bg-primary/5 hover:bg-primary/10'
                                : 'border-muted/20 hover:border-muted/40'">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="sr-only"
                                @change="toggleService({{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})"
                                :checked="selectedServices.includes({{ $service->id }})">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium" :class="selectedServices.includes({{ $service->id }}) ? 'text-primary' : 'text-warm-gray'">{{ $service->name }}</span>
                                <span class="font-semibold" :class="selectedServices.includes({{ $service->id }}) ? 'text-primary' : 'text-warm-gray'">{{ number_format($service->price, 0, ',', '.') }}d</span>
                            </div>
                            <span class="text-sm text-muted">{{ $service->duration_minutes }} phut. {{ Str::limit($service->description, 60) }}</span>
                            <div class="absolute top-3 right-3" x-show="selectedServices.includes({{ $service->id }})">
                                <span class="material-symbols-outlined fill text-primary text-lg">check_circle</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('service_ids')
                        <p class="text-error-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </section>

                <hr class="border-muted/10">

                {{-- Step 2: Choose Barber --}}
                <section :class="selectedServices.length > 0 ? 'opacity-100' : 'opacity-40 pointer-events-none'" class="transition-opacity duration-500">
                    <div class="flex items-center mb-5">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold mr-3"
                            :class="selectedBarber ? 'bg-primary text-white' : 'bg-surface text-warm-gray-light'">2</span>
                        <h2 class="text-lg font-semibold font-display text-warm-gray">Chon tho cat</h2>
                    </div>
                    <div class="flex gap-6 overflow-x-auto pb-2 time-scroll">
                        @foreach($barbers as $barber)
                        <label class="flex flex-col items-center cursor-pointer group flex-shrink-0">
                            <input type="radio" name="barber_id" value="{{ $barber->id }}" class="sr-only"
                                @change="selectBarber({{ $barber->id }})"
                                :checked="selectedBarber == {{ $barber->id }}"
                                {{ request('barber_id') == $barber->id ? 'checked' : '' }}>
                            <div class="w-20 h-20 rounded-full overflow-hidden p-0.5 mb-2 transition-all duration-300"
                                :class="selectedBarber == {{ $barber->id }} ? 'border-2 border-primary' : 'border border-transparent group-hover:border-muted/40'">
                                @if($barber->user->avatar)
                                    <img src="{{ Storage::url($barber->user->avatar) }}" alt="{{ $barber->user->name }}"
                                        class="w-full h-full object-cover rounded-full transition-all duration-300"
                                        :class="selectedBarber == {{ $barber->id }} ? '' : 'grayscale group-hover:grayscale-0 opacity-70 group-hover:opacity-100'">
                                @else
                                    <div class="w-full h-full rounded-full bg-surface flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl text-muted">person</span>
                                    </div>
                                @endif
                            </div>
                            <span class="text-sm font-medium transition-colors"
                                :class="selectedBarber == {{ $barber->id }} ? 'text-primary' : 'text-muted group-hover:text-warm-gray'">{{ $barber->user->name }}</span>
                            @if($barber->rating > 0)
                                <div class="flex items-center gap-0.5 mt-1">
                                    <span class="material-symbols-outlined fill text-primary text-xs">star</span>
                                    <span class="text-xs text-muted">{{ number_format($barber->rating, 1) }}</span>
                                </div>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @error('barber_id')
                        <p class="text-error-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </section>

                <hr class="border-muted/10">

                {{-- Step 3: Date & Time --}}
                <section :class="selectedBarber ? 'opacity-100' : 'opacity-40 pointer-events-none'" class="transition-opacity duration-500">
                    <div class="flex items-center mb-6">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold mr-3"
                            :class="selectedSlot ? 'bg-primary text-white' : 'bg-surface text-warm-gray-light'">3</span>
                        <h2 class="text-lg font-semibold font-display text-warm-gray">Chon ngay & gio</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Date Selector --}}
                        <div>
                            <h3 class="text-sm font-medium mb-3 text-warm-gray">Chon ngay</h3>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                @for($i = 0; $i < 7; $i++)
                                    @php
                                        $date = now()->addDays($i);
                                        $dateStr = $date->format('Y-m-d');
                                        $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                                    @endphp
                                    <button type="button"
                                        @click="selectDate('{{ $dateStr }}')"
                                        class="py-3 px-2 text-center border transition-all duration-200"
                                        :class="selectedDate === '{{ $dateStr }}'
                                            ? 'border-primary bg-primary/5 text-primary font-medium'
                                            : 'border-muted/20 text-warm-gray hover:border-muted/40'">
                                        <div class="text-xs uppercase tracking-wider mb-1" :class="selectedDate === '{{ $dateStr }}' ? 'text-primary' : 'text-muted'">{{ $dayNames[$date->dayOfWeek] }}</div>
                                        <div class="text-lg font-semibold">{{ $date->format('d') }}</div>
                                        <div class="text-xs" :class="selectedDate === '{{ $dateStr }}' ? 'text-primary/70' : 'text-muted'">Thg {{ $date->format('m') }}</div>
                                    </button>
                                @endfor
                            </div>
                        </div>

                        {{-- Time Slots --}}
                        <div>
                            <h3 class="text-sm font-medium mb-3 text-warm-gray">Gio trong</h3>

                            {{-- Loading --}}
                            <div x-show="loadingSlots" class="flex items-center justify-center py-12">
                                <div class="w-6 h-6 border-2 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                            </div>

                            {{-- No date selected --}}
                            <div x-show="!selectedDate && !loadingSlots" class="text-center py-12 text-muted text-sm">
                                Vui long chon ngay truoc
                            </div>

                            {{-- No slots --}}
                            <div x-show="selectedDate && !loadingSlots && slots.length === 0" class="text-center py-12 text-muted text-sm">
                                Khong co gio trong cho ngay nay
                            </div>

                            {{-- Slot grid --}}
                            <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-3 gap-2 max-h-64 overflow-y-auto pr-1 time-scroll">
                                <template x-for="slot in slots" :key="slot.id">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="time_slot_id" :value="slot.id" class="sr-only"
                                            @change="selectedSlot = slot.id; selectedSlotLabel = slot.label">
                                        <div class="py-2.5 px-3 text-center border text-sm transition-all duration-200"
                                            :class="selectedSlot == slot.id
                                                ? 'border-primary bg-primary/5 text-primary font-medium'
                                                : 'border-muted/20 hover:border-primary hover:text-primary'"
                                            x-text="slot.label"></div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                    @error('time_slot_id')
                        <p class="text-error-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </section>

                <hr class="border-muted/10">

                {{-- Step 4: Note --}}
                <section :class="selectedSlot ? 'opacity-100' : 'opacity-40 pointer-events-none'" class="transition-opacity duration-500">
                    <div class="flex items-center mb-4">
                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-surface text-warm-gray-light text-xs font-bold mr-3">4</span>
                        <h2 class="text-lg font-semibold font-display text-warm-gray">Ghi chu (tuy chon)</h2>
                    </div>
                    <textarea name="note" rows="3" placeholder="Yeu cau dac biet, kieu toc mong muon..."
                        class="w-full px-4 py-3 border border-muted/20 text-warm-gray placeholder-muted text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors resize-none">{{ old('note') }}</textarea>
                </section>
            </div>

            {{-- Sticky Footer --}}
            <div class="p-6 bg-surface/30 border-t border-muted/10">
                {{-- Summary --}}
                <div class="flex justify-between items-center mb-2 text-sm">
                    <span class="text-muted">Dich vu</span>
                    <span class="font-medium text-warm-gray" x-text="selectedServices.length + ' dich vu'">0 dich vu</span>
                </div>
                <div class="flex justify-between items-center mb-2 text-sm">
                    <span class="text-muted">Thoi gian</span>
                    <span class="font-medium text-warm-gray" x-text="totalDuration + ' phut'">0 phut</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-muted text-sm">Tong cong</span>
                    <span class="font-bold text-xl text-warm-gray" x-text="formatPrice(totalPrice)">0d</span>
                </div>
                <button type="submit"
                    :disabled="!canSubmit"
                    class="w-full py-4 text-white font-bold tracking-widest uppercase text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                    :class="canSubmit ? 'bg-primary hover:bg-primary-dark cursor-pointer' : 'bg-muted/40 cursor-not-allowed'">
                    Xac Nhan Dat Lich
                </button>
                <p class="text-center text-xs text-muted mt-3 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    Thanh toan sau khi su dung dich vu.
                </p>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
function bookingWizard() {
    return {
        selectedServices: [],
        servicePrices: {},
        serviceDurations: {},
        totalPrice: 0,
        totalDuration: 0,
        selectedBarber: {{ request('barber_id', 'null') }},
        selectedDate: null,
        selectedSlot: null,
        selectedSlotLabel: null,
        slots: [],
        loadingSlots: false,

        init() {
            // If barber_id was pre-selected via URL
            if (this.selectedBarber) {
                // Ready for date selection
            }
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
        },

        selectBarber(id) {
            this.selectedBarber = id;
            this.selectedSlot = null;
            this.selectedSlotLabel = null;
            if (this.selectedDate) {
                this.fetchSlots();
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
            return new Intl.NumberFormat('vi-VN').format(price) + 'd';
        },

        get canSubmit() {
            return this.selectedServices.length > 0 && this.selectedBarber && this.selectedSlot;
        }
    }
}
</script>
@endpush
@endsection
