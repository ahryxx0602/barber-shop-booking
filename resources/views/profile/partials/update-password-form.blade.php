<section>
    <header>
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Đổi mật khẩu
        </h4>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Sử dụng mật khẩu dài và ngẫu nhiên để bảo mật tài khoản.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Mật khẩu hiện tại
            </label>
            <input
                id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password"
                placeholder="Nhập mật khẩu hiện tại"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-1 text-xs text-error-500">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Mật khẩu mới
            </label>
            <input
                id="update_password_password" name="password" type="password"
                autocomplete="new-password"
                placeholder="Nhập mật khẩu mới"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            @if ($errors->updatePassword->has('password'))
                <p class="mt-1 text-xs text-error-500">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Xác nhận mật khẩu mới
            </label>
            <input
                id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu mới"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1 text-xs text-error-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600"
            >
                Cập nhật mật khẩu
            </button>

            @if (session('status') === 'password-updated')
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