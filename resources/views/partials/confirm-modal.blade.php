{{-- Confirm Modal — Reusable via Alpine.js --}}
{{-- Usage: dispatch 'open-confirm-modal' event with detail object --}}
<div
  x-data="{
    show: false,
    title: '',
    message: '',
    confirmText: 'Xác nhận',
    cancelText: 'Hủy',
    formAction: '',
    formMethod: 'POST',
    csrfToken: '{{ csrf_token() }}',
    variant: 'danger',
  }"
  x-on:open-confirm-modal.window="
    show = true;
    title = $event.detail.title || 'Xác nhận';
    message = $event.detail.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
    confirmText = $event.detail.confirmText || 'Xác nhận';
    cancelText = $event.detail.cancelText || 'Hủy';
    formAction = $event.detail.action || '';
    formMethod = $event.detail.method || 'POST';
    variant = $event.detail.variant || 'danger';
  "
  x-show="show"
  x-cloak
  class="fixed inset-0 z-[99999] flex items-center justify-center"
>
  {{-- Backdrop --}}
  <div
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
    @click="show = false"
  ></div>

  {{-- Modal --}}
  <div
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
    class="relative w-full max-w-sm mx-4 bg-white rounded-2xl shadow-xl dark:bg-gray-800 overflow-hidden"
  >
    <div class="p-6 text-center">
      {{-- Icon --}}
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full"
           :class="variant === 'danger' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-amber-100 dark:bg-amber-900/30'"
      >
        {{-- Warning Icon --}}
        <svg
          class="h-6 w-6"
          :class="variant === 'danger' ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'"
          fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
      </div>

      {{-- Title --}}
      <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white" x-text="title"></h3>

      {{-- Message --}}
      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400" x-text="message"></p>

      {{-- Actions --}}
      <div class="flex items-center justify-center gap-3">
        <button
          @click="show = false"
          type="button"
          class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          x-text="cancelText"
        ></button>

        <form :action="formAction" method="POST" class="inline">
          <input type="hidden" name="_token" :value="csrfToken">
          <template x-if="formMethod === 'DELETE'">
            <input type="hidden" name="_method" value="DELETE">
          </template>
          <template x-if="formMethod === 'PUT'">
            <input type="hidden" name="_method" value="PUT">
          </template>
          <template x-if="formMethod === 'PATCH'">
            <input type="hidden" name="_method" value="PATCH">
          </template>
          <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition-colors"
            :class="variant === 'danger'
              ? 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600'
              : 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600'"
            x-text="confirmText"
          ></button>
        </form>
      </div>
    </div>
  </div>
</div>
