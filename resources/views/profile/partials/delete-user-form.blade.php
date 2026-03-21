<section x-data="{ showDeleteModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
    <header>
        <h4 class="text-lg font-semibold text-error-500">
            Xóa tài khoản
        </h4>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Khi tài khoản bị xóa, tất cả dữ liệu sẽ bị xóa vĩnh viễn. Hãy tải về mọi dữ liệu bạn muốn giữ trước khi xóa.
        </p>
    </header>

    <div class="mt-5">
        <button
            @click="showDeleteModal = true"
            type="button"
            class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-error-500 shadow-theme-xs hover:bg-error-600"
        >
            Xóa tài khoản
        </button>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80" @click="showDeleteModal = false"></div>

        {{-- Modal Content --}}
        <div
            x-show="showDeleteModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-800 z-99999"
            @keydown.escape.window="showDeleteModal = false"
        >
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Bạn chắc chắn muốn xóa tài khoản?
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Sau khi xóa, tất cả dữ liệu sẽ bị mất vĩnh viễn. Nhập mật khẩu để xác nhận.
                </p>

                <div class="mt-5">
                    <label for="delete_password" class="sr-only">Mật khẩu</label>
                    <input
                        id="delete_password" name="password" type="password"
                        placeholder="Nhập mật khẩu để xác nhận"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    @if ($errors->userDeletion->has('password'))
                        <p class="mt-1 text-xs text-error-500">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                    >
                        Hủy
                    </button>
                    <button
                        type="submit"
                        class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-error-500 shadow-theme-xs hover:bg-error-600"
                    >
                        Xóa tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>