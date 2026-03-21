<section>
    <header>
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Thông tin cá nhân
        </h4>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Họ và tên
            </label>
            <input
                id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            @error('name')
                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Email
            </label>
            <input
                id="email" name="email" type="email"
                value="{{ old('email', $user->email) }}"
                required autocomplete="username"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            @error('email')
                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800 dark:text-gray-300">
                        Email chưa được xác minh.
                        <button form="send-verification" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 underline">
                            Gửi lại email xác minh.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success-500">
                            Đã gửi link xác minh mới đến email của bạn.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"
            >
                Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-500"
                >Đã lưu.</p>
            @endif
        </div>
    </form>
</section>