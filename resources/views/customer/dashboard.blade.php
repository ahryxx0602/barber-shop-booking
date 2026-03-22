<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Xin chào, <strong>{{ auth()->user()->name }}</strong>! 👋
                    <p class="mt-2 text-gray-600">Chào mừng bạn đến với Classic Cut. Đặt lịch cắt tóc dễ dàng, nhanh
                        chóng.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>