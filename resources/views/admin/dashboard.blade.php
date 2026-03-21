@extends('layouts.tailadmin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tổng quan hệ thống BarberBook</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <p class="text-gray-900 dark:text-white">
            Xin chào, <strong>{{ auth()->user()->name }}</strong>!
        </p>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Đây là trang quản trị hệ thống BarberBook. Quản lý thợ, dịch vụ và người dùng tại đây.</p>
    </div>
@endsection