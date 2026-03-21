@extends('layouts.barber')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Thợ</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Xin chào, <strong>{{ auth()->user()->name }}</strong>! ✂️
                    <p class="mt-2 text-gray-600">Đây là trang quản lý của thợ cắt tóc. Quản lý lịch làm việc và booking tại đây.</p>
                </div>
            </div>
        </div>
    </div>
@endsection