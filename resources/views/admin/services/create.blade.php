@extends('layouts.tailadmin')

@section('title', 'Thêm Dịch vụ mới')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Thêm Dịch vụ mới</h2>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5">
                        {{-- Tên dịch vụ --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tên dịch vụ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mô tả --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả</label>
                            <textarea name="description" rows="3"
                                      class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                        </div>

                        {{-- Giá và Thời gian --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Giá (VNĐ) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="price" value="{{ old('price') }}" min="0" step="1000"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                                @error('price')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Thời gian (phút) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="15" max="300"
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                                @error('duration_minutes')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Ảnh --}}
                        <div x-data="imageUpload()" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ảnh dịch vụ</label>

                            {{-- Preview --}}
                            <template x-if="preview">
                                <div class="mb-3 relative inline-block">
                                    <img :src="preview" class="w-24 h-24 object-cover rounded-lg border-2 border-brand-300 dark:border-brand-600 shadow-sm" alt="Preview">
                                    <button type="button" @click="removeImage()"
                                            class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 shadow">
                                        ✕
                                    </button>
                                </div>
                            </template>

                            {{-- Drop zone --}}
                            <div x-show="!preview"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="isDragging ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20' : 'border-gray-300 dark:border-gray-600'"
                                 class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-brand-400 dark:hover:border-brand-500"
                                 @click="$refs.fileInput.click()">
                                <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="text-brand-600 dark:text-brand-400 font-medium">Nhấp để chọn ảnh</span> hoặc kéo thả vào đây
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPG, JPEG, PNG, WEBP (tối đa 2MB)</p>
                            </div>

                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" x-ref="fileInput"
                                   @change="handleFileSelect($event)" class="hidden">

                            @error('image')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Trạng thái --}}
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', 1) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600">
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Kích hoạt dịch vụ</label>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Lưu dịch vụ
                        </button>
                        <a href="{{ route('admin.services.index') }}"
                           class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Hủy
                        </a>
                    </div>
                </form>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    function imageUpload() {
        return {
            preview: null,
            isDragging: false,
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) this.showPreview(file);
            },
            handleDrop(event) {
                this.isDragging = false;
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.$refs.fileInput.files = dataTransfer.files;
                    this.showPreview(file);
                }
            },
            showPreview(file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.preview = e.target.result; };
                reader.readAsDataURL(file);
            },
            removeImage() {
                this.preview = null;
                this.$refs.fileInput.value = '';
            }
        };
    }
</script>
@endpush
