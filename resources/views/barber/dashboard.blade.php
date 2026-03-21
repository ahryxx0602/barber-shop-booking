@extends('layouts.tailbarber')

@section('page', 'barberDashboard')
@section('title', 'Dashboard Thợ')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Thợ cắt</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tổng quan lịch làm việc và booking</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
        <p class="text-gray-900 dark:text-white">
            Xin chào, <strong>{{ auth()->user()->name }}</strong>! ✂️
        </p>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Đây là trang quản lý của thợ cắt tóc. Quản lý lịch làm việc và booking tại đây.</p>
    </div>
@endsection