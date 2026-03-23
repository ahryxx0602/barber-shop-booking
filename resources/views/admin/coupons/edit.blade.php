@extends('layouts.tailadmin')

@section('title', 'Sửa Mã giảm giá')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa Mã giảm giá</h2>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.coupons._form')
            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600">
                    Cập nhật
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="ml-3 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">Hủy</a>
            </div>
        </form>
    </div>
@endsection
