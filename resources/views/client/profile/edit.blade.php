@extends('layouts.client')

@section('title', 'Chinh sua thong tin')

@section('content')
<section class="bg-bg-light min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-[640px] mx-auto">

        {{-- Header --}}
        <div class="flex items-center mb-8">
            <a href="{{ route('client.profile.show') }}" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-surface transition-colors mr-4">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold font-display tracking-tight text-warm-gray">Chinh Sua Thong Tin</h1>
        </div>

        <form action="{{ route('client.profile.update') }}" method="POST" class="bg-white border border-muted/20 shadow-sm">
            @csrf
            @method('PUT')

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-warm-gray mb-2">Ho va ten</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 border border-muted/20 text-warm-gray placeholder-muted text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-warm-gray mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 border border-muted/20 text-warm-gray placeholder-muted text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-warm-gray mb-2">So dien thoai</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="0901234567"
                        class="w-full px-4 py-3 border border-muted/20 text-warm-gray placeholder-muted text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="p-6 sm:p-8 bg-surface/30 border-t border-muted/10 flex gap-4">
                <button type="submit"
                    class="flex-1 py-3 bg-primary text-white font-bold tracking-widest uppercase text-sm hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Luu Thong Tin
                </button>
                <a href="{{ route('client.profile.show') }}"
                    class="flex items-center justify-center px-6 py-3 border border-muted/20 text-warm-gray text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    Huy
                </a>
            </div>
        </form>
    </div>
</section>
@endsection
