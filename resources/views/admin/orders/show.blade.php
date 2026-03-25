@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentStatus')
@use('App\Enums\OrderPaymentMethod')

@extends('layouts.tailadmin')

@section('title', 'Chi tiết Đơn hàng: ' . $order->order_code)

@section('content')
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Đơn hàng #{{ $order->order_code }}</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide uppercase
                    @if($order->status === OrderStatus::Pending) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400
                    @elseif($order->status === OrderStatus::Confirmed) bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400
                    @elseif($order->status === OrderStatus::Shipping) bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-400
                    @elseif($order->status === OrderStatus::Delivered) bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400
                    @elseif($order->status === OrderStatus::Cancelled) bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400
                    @endif">
                    {{ $order->status->label() }}
                </span>
            </div>
            <p class="text-[12px] text-gray-500 dark:text-gray-400">Tạo: {{ $order->created_at->format('H:i - d/m/Y') }}</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            {{-- Trạng Thái Actions (Buttons only, modals are at the bottom) --}}
            @if($order->status !== OrderStatus::Delivered && $order->status !== OrderStatus::Cancelled)
                @if($order->status === OrderStatus::Pending)
                    <button type="button" @click="$dispatch('open-modal-confirm-order')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Xác nhận
                    </button>
                @elseif($order->status === OrderStatus::Confirmed)
                    <button type="button" @click="$dispatch('open-modal-shipping-order')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs font-semibold shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>Giao hàng
                    </button>
                @elseif($order->status === OrderStatus::Shipping)
                    <button type="button" @click="$dispatch('open-modal-deliver-order')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Hoàn thành
                    </button>
                @endif
                
                {{-- Hủy Đơn Btn --}}
                <button type="button" @click="$dispatch('open-modal-cancel-order')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-500 text-red-600 hover:bg-red-50 rounded text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Hủy
                </button>
            @endif
            
            <a href="{{ route('admin.orders.index') }}" class="inline-flex px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 px-3 py-2 bg-green-100 border-l-4 border-green-500 text-green-800 rounded text-xs">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-3 py-2 bg-red-100 border-l-4 border-red-500 text-red-800 rounded text-xs">{{ session('error') }}</div>
    @endif

    {{-- Order Stepper Timeline --}}
    @if($order->status !== OrderStatus::Cancelled)
        @php
            $steps = [
                OrderStatus::Pending->value => 'Chờ xác nhận',
                OrderStatus::Confirmed->value => 'Đã xác nhận',
                OrderStatus::Shipping->value => 'Giao hàng',
                OrderStatus::Delivered->value => 'Hoàn thành',
            ];
            $stepKeys = array_keys($steps);
            $currentStepIndex = array_search($order->status->value, $stepKeys);
        @endphp
        <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 py-3 px-4">
            <div class="relative flex items-start justify-between w-full max-w-2xl mx-auto">
                {{-- Background Line --}}
                <div class="absolute left-[12%] top-3.5 w-[76%] h-0.5 bg-gray-100 dark:bg-gray-700 rounded-full" aria-hidden="true"></div>
                
                {{-- Active Line --}}
                <div class="absolute left-[12%] top-3.5 h-0.5 bg-brand-500 rounded-full transition-all duration-500 ease-in-out" 
                     style="width: {{ $currentStepIndex === 0 ? '0%' : ($currentStepIndex === 1 ? '25.33%' : ($currentStepIndex === 2 ? '50.66%' : '76%')) }}"></div>

                @foreach($steps as $statusValue => $label)
                    @php
                        $stepIndex = $loop->index;
                        $isCompleted = $currentStepIndex >= $stepIndex;
                        $isCurrent = $currentStepIndex === $stepIndex;
                    @endphp
                    <div class="relative flex flex-col items-center z-10 w-[25%]">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800 transition-colors duration-300
                            {{ $isCompleted ? 'bg-brand-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                            @if($isCompleted)
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <span class="text-[10px] font-bold">{{ $stepIndex + 1 }}</span>
                            @endif
                        </div>
                        <div class="mt-1 text-center">
                            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider
                                {{ $isCurrent ? 'text-brand-600 dark:text-brand-400' : ($isCompleted ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400') }}">
                                {{ $label }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Báo Hủy Đơn Compact --}}
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm border border-red-200 dark:border-red-800 p-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 flex-shrink-0 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-800 dark:text-red-400 mb-0.5">Đơn hàng bị Hủy @if($order->cancelled_at) <span class="text-xs font-normal">({{ \Carbon\Carbon::parse($order->cancelled_at)->format('H:i d/m/Y') }})</span> @endif</h3>
                <p class="text-xs text-red-700 dark:text-red-300"><strong>Lý do:</strong> {{ $order->cancel_reason ?? 'Không có lý do chi tiết.' }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Cột trái (Chi tiết SP) --}}
        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Chi tiết Sản phẩm ({{ $order->items->count() }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500 border-b border-gray-200">
                                <th class="px-4 py-2">Sản phẩm</th>
                                <th class="px-4 py-2 text-right">Đơn giá</th>
                                <th class="px-4 py-2 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-3">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" class="w-9 h-9 object-cover rounded border border-gray-100 p-0.5">
                                            @else
                                                <div class="w-9 h-9 rounded bg-gray-100 border border-gray-200 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 truncate max-w-[180px]">{{ $item->product->name }}</p>
                                                <p class="text-[10px] text-brand-600">SKU: {{ $item->product->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <div class="text-[11px] text-gray-800">{{ number_format($item->unit_price, 0, ',', '.') }}đ x {{ $item->quantity }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <div class="text-[12px] font-bold text-gray-800">{{ number_format($item->total_price, 0, ',', '.') }}đ</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tổng kết Breakdown Compact --}}
                <div class="bg-gray-50/70 p-4 border-t border-gray-200 flex flex-col items-end">
                    <div class="w-full max-w-xs space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Tạm tính</span>
                            <span class="font-medium text-gray-800">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        @if($order->product_discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Mã giảm giá SP {{ $order->product_coupon_code ? "({$order->product_coupon_code})" : '' }}</span>
                                <span>-{{ number_format($order->product_discount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Thuế VAT ({{ $order->tax_rate }}%)</span>
                            <span class="font-medium text-gray-800">{{ number_format($order->tax_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí giao hàng</span>
                            <span class="font-medium text-gray-800">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                        </div>
                        @if($order->shipping_discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Mã miễn phí Ship {{ $order->shipping_coupon_code ? "({$order->shipping_coupon_code})" : '' }}</span>
                                <span>-{{ number_format($order->shipping_discount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-800">Thành tiền</span>
                            <span class="text-base font-black text-brand-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cột Phải (Combined Card) --}}
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-4">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Thông tin Đơn hàng
                    </h3>
                </div>
                
                <div class="p-4 space-y-4 divide-y divide-gray-100">
                    {{-- Section 1: Khách hàng --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Khách hàng</span>
                            <a href="{{ route('admin.users.show', $order->customer) }}" class="text-[10px] font-semibold text-brand-600 hover:text-brand-800">Hồ sơ &rarr;</a>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($order->customer->avatar)
                                <img src="{{ Storage::url($order->customer->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-600 flex justify-center items-center font-bold text-xs border border-brand-100">
                                    {{ mb_substr($order->customer->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="text-xs">
                                <p class="font-bold text-gray-900">{{ $order->customer->name }}</p>
                                <p class="text-gray-500">{{ $order->customer->phone ?? $order->customer->email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Giao hàng --}}
                    <div class="pt-4 text-xs">
                        <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-2">Giao hàng</p>
                        @if($order->shippingAddress)
                            <div class="space-y-1 text-gray-700">
                                <p><span class="font-semibold text-gray-900">{{ $order->shippingAddress->receiver_name }}</span> - {{ $order->shippingAddress->phone }}</p>
                                <p>{{ $order->shippingAddress->address }}</p>
                                <p>{{ $order->shippingAddress->ward }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->city }}</p>
                            </div>
                            @if($order->note)
                                <div class="mt-2 p-2 bg-yellow-50 rounded border border-yellow-200 text-yellow-800 text-[11px]">
                                    <strong>Ghi chú:</strong> {{ $order->note }}
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500">Chưa có thông tin giao hàng.</p>
                        @endif
                    </div>

                    {{-- Section 3: Thanh toán --}}
                    <div class="pt-4 text-xs">
                        <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-2">Thanh toán</p>
                        @if($order->payment)
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-gray-600">{{ $order->payment->method->label() }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $order->payment->status === PaymentStatus::Paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $order->payment->status->label() }}
                                </span>
                            </div>
                            @if($order->payment->transaction_id)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-gray-500">Mã giao dịch</span>
                                    <span class="font-mono text-gray-800">{{ $order->payment->transaction_id }}</span>
                                </div>
                            @endif
                            @if($order->payment->paid_at)
                                <div class="flex items-center justify-between text-[11px] mt-1 text-gray-500">
                                    <span>Lúc thanh toán</span>
                                    <span>{{ \Carbon\Carbon::parse($order->payment->paid_at)->format('H:i d/m/y') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- MODALS ZONE (Outside of layout flex/grid constraints) --}}
    @if($order->status !== OrderStatus::Delivered && $order->status !== OrderStatus::Cancelled)
        
        {{-- Modal: Confirm Order --}}
        @if($order->status === OrderStatus::Pending)
        <div x-data="{ open: false }" @open-modal-confirm-order.window="open = true" x-show="open" style="display: none;" class="fixed inset-0 z-[999999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" x-transition.scale.origin.bottom class="inline-block relative overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ OrderStatus::Confirmed->value }}">
                        <div class="px-6 pt-5 pb-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Xác nhận đơn</h3>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Đánh dấu đơn hàng là <strong>Confirmed</strong> và bắt đầu chuẩn bị hàng?</p>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/30 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="open = false" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-100 transition-colors">Hủy</button>
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium shadow-sm transition-colors">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal: Shipping Order --}}
        @if($order->status === OrderStatus::Confirmed)
        <div x-data="{ open: false }" @open-modal-shipping-order.window="open = true" x-show="open" style="display: none;" class="fixed inset-0 z-[999999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" x-transition.scale.origin.bottom class="inline-block relative overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ OrderStatus::Shipping->value }}">
                        <div class="px-6 pt-5 pb-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Giao hàng</h3>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Chuyển trạng thái giao cho đơn vị vận chuyển (Shipping)?</p>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/30 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="open = false" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-100 transition-colors">Hủy</button>
                            <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-medium shadow-sm transition-colors">Đồng ý</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal: Deliver Order --}}
        @if($order->status === OrderStatus::Shipping)
        <div x-data="{ open: false }" @open-modal-deliver-order.window="open = true" x-show="open" style="display: none;" class="fixed inset-0 z-[999999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" x-transition.scale.origin.bottom class="inline-block relative overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ OrderStatus::Delivered->value }}">
                        <div class="px-6 pt-5 pb-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hoàn thành</h3>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Xác nhận giao hàng thành công và kết thúc đơn hàng này?</p>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/30 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="open = false" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-100 transition-colors">Hủy</button>
                            <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium shadow-sm transition-colors">Xác nhận</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal: Cancel Order --}}
        <div x-data="{ open: false }" @open-modal-cancel-order.window="open = true" x-show="open" style="display: none;" class="fixed inset-0 z-[999999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" x-transition.scale.origin.bottom class="inline-block relative overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-sm w-full border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ OrderStatus::Cancelled->value }}">
                        <div class="px-5 pt-4 pb-3">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Xác nhận Hủy</h3>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Lý do hủy đơn <span class="text-red-500">*</span></label>
                                <textarea name="cancel_reason" required rows="2" class="w-full text-sm rounded border-gray-300 dark:bg-gray-700 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Lý do hủy..."></textarea>
                            </div>
                        </div>
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/30 flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" @click="open = false" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-100 transition-colors">Đóng</button>
                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium shadow-sm transition-colors">Hủy Đơn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
