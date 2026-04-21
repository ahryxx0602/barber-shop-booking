@extends(auth()->user()->role === \App\Enums\UserRole::Admin ? 'layouts.tailadmin' : 'layouts.tailbarber')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Hồ sơ cá nhân</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Quản lý thông tin tài khoản của bạn</p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        {{-- Profile Header Card --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                {{-- Avatar --}}
                <div class="w-20 h-20 overflow-hidden border-2 border-gray-200 rounded-full dark:border-gray-700 flex items-center justify-center">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" class="w-full h-full object-cover" alt="" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-brand-500">
                            <span class="text-2xl font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
                {{-- Info --}}
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ auth()->user()->name }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->email }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vai trò: <span class="capitalize font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->role }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Update Profile Info --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>
@endsection