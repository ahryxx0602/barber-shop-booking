@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Xin chào, <strong>{{ auth()->user()->name }}</strong>! 🛡️
                    <p class="mt-2 text-gray-600">Đây là trang quản trị hệ thống BarberBook. Quản lý thợ, dịch vụ và người dùng tại đây.</p>
                </div>
            </div>
        </div>
    </div>
@endsection