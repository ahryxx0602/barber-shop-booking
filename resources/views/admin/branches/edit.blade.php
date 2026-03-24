@extends('layouts.tailadmin')

@section('title', 'Sửa Chi nhánh')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sửa Chi nhánh: {{ $branch->name }}</h2>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">

                <form method="POST" action="{{ route('admin.branches.update', $branch) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        {{-- Thông tin chi nhánh --}}
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider">Thông tin chi nhánh</h3>
                        </div>

                        {{-- Tên --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tên chi nhánh <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Địa chỉ --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Địa chỉ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address" value="{{ old('address', $branch->address) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 @error('address') border-red-500 @enderror">
                            @error('address')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Số điện thoại --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số điện thoại</label>
                            <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">
                        </div>

                        {{-- Mô tả --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả</label>
                            <textarea name="description" rows="3"
                                      class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500">{{ old('description', $branch->description) }}</textarea>
                        </div>

                        {{-- Ảnh chi nhánh --}}
                        <div x-data="imageUpload('{{ $branch->image ? Storage::url($branch->image) : '' }}')" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ảnh chi nhánh</label>

                            {{-- Preview --}}
                            <template x-if="preview">
                                <div class="mb-3 relative inline-block">
                                    <img :src="preview" class="w-32 h-24 object-cover rounded-lg border-2 border-brand-300 dark:border-brand-600 shadow-sm" alt="Preview">
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
                                   {{ old('is_active', $branch->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-brand-500">
                            <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Kích hoạt chi nhánh</label>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-brand-500 text-white text-sm font-medium rounded-md hover:bg-brand-600">
                            Cập nhật
                        </button>
                        <a href="{{ route('admin.branches.index') }}"
                           class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Hủy
                        </a>
                    </div>
                </form>

        </div>

        {{-- Danh sách thợ thuộc chi nhánh --}}
        @if($branch->barbers->count() > 0)
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider mb-4">
                Thợ cắt thuộc chi nhánh ({{ $branch->barbers->count() }})
            </h3>
            <div class="space-y-3">
                @foreach($branch->barbers as $barber)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        @if ($barber->user->avatar)
                            <img src="{{ Storage::url($barber->user->avatar) }}" class="w-8 h-8 object-cover rounded-full" alt="">
                        @else
                            <div class="w-8 h-8 bg-brand-100 dark:bg-brand-900 rounded-full flex items-center justify-center text-brand-700 dark:text-brand-300 font-semibold text-xs">
                                {{ strtoupper(substr($barber->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $barber->user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $barber->experience_years }} năm KN</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.barbers.edit', $barber) }}" class="text-xs text-brand-600 hover:text-brand-800 dark:text-brand-400">Sửa</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function imageUpload(existingUrl = '') {
        return {
            preview: existingUrl || null,
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
