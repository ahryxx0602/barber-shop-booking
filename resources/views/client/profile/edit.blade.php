@extends('layouts.client')

@section('title', 'Chỉnh sửa thông tin')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;padding:48px 16px;" class="sm:px-6 lg:px-8">
    <div style="max-width:640px;margin:0 auto;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;margin-bottom:32px;">
            <a href="{{ route('client.profile.show') }}" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;color:var(--v-ink);margin-right:16px;border:1px solid var(--v-rule);transition:border-color 0.2s;"
                onmouseover="this.style.borderColor='var(--v-copper)'" onmouseout="this.style.borderColor='var(--v-rule)'">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="v-title-sm">Chỉnh Sửa Thông Tin</h1>
        </div>

        <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data"
            style="border:1px solid var(--v-rule);background:#fff;box-shadow:4px 4px 0 var(--v-copper);overflow:hidden;">
            @csrf
            @method('PUT')

            <div style="padding:24px;" class="sm:p-8">
                <div style="display:flex;flex-direction:column;gap:24px;">

                    {{-- Avatar Upload --}}
                    <div x-data="{ preview: null }">
                        <label style="display:block;font-size:13px;font-weight:500;color:var(--v-ink);margin-bottom:10px;">Ảnh đại diện</label>
                        <div style="display:flex;align-items:center;gap:16px;">
                            {{-- Current/Preview --}}
                            <div style="width:72px;height:72px;overflow:hidden;border:2px solid var(--v-rule);flex-shrink:0;position:relative;">
                                <template x-if="preview">
                                    <img :src="preview" style="width:100%;height:100%;object-fit:cover;" />
                                </template>
                                <template x-if="!preview">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" />
                                    @else
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--v-surface);">
                                            <span class="material-symbols-outlined" style="font-size:28px;color:var(--v-muted);opacity:0.5;">person</span>
                                        </div>
                                    @endif
                                </template>
                            </div>

                            <div>
                                <label for="avatar" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--v-copper);border:1px solid var(--v-rule);cursor:pointer;transition:all 0.2s;"
                                    onmouseover="this.style.borderColor='var(--v-copper)';this.style.background='var(--v-surface)'" onmouseout="this.style.borderColor='var(--v-rule)';this.style.background='transparent'">
                                    <span class="material-symbols-outlined" style="font-size:16px;">photo_camera</span>
                                    Chọn ảnh
                                </label>
                                <input type="file" name="avatar" id="avatar" accept="image/*" style="display:none;"
                                    @change="const file = $event.target.files[0]; if (file) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(file); }" />
                                <p style="margin-top:6px;font-size:11px;color:var(--v-muted);">JPG, PNG, WebP. Tối đa 2MB.</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p style="color:#dc2626;font-size:12px;margin-top:6px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" style="display:block;font-size:13px;font-weight:500;color:var(--v-ink);margin-bottom:8px;">Họ và tên</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="v-input">
                        @error('name')
                            <p style="color:#dc2626;font-size:13px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" style="display:block;font-size:13px;font-weight:500;color:var(--v-ink);margin-bottom:8px;">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="v-input">
                        @error('email')
                            <p style="color:#dc2626;font-size:13px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" style="display:block;font-size:13px;font-weight:500;color:var(--v-ink);margin-bottom:8px;">Số điện thoại</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="0901234567"
                            class="v-input">
                        @error('phone')
                            <p style="color:#dc2626;font-size:13px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="padding:20px 24px;background:var(--v-surface);border-top:1px solid var(--v-rule);display:flex;gap:16px;" class="sm:px-8">
                <button type="submit" class="v-btn-primary" style="flex:1;justify-content:center;">
                    Lưu Thông Tin
                </button>
                <a href="{{ route('client.profile.show') }}" class="v-btn-outline v-btn-sm" style="padding:0 24px;">
                    Huỷ
                </a>
            </div>
        </form>
    </div>
</section>
@endsection
