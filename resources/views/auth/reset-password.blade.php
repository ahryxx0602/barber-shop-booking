@extends('layouts.client')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
    <div style="width:100%;max-width:440px;">
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;">
            <div style="padding:32px 32px 0;text-align:center;">
                <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;margin-bottom:20px;">
                    <span style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);letter-spacing:2px;">✦ CLASSIC CUT</span>
                </a>
                <div class="v-ornament" style="max-width:260px;margin:0 auto 16px;">
                    Đặt lại mật khẩu
                </div>
                <p style="color:var(--v-muted);font-size:13px;margin-bottom:4px;">Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>

            <div style="padding:24px 32px 32px;">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email --}}
                    <div style="margin-bottom:14px;">
                        <label for="email" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Email <span style="color:var(--v-copper);">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                            required autofocus autocomplete="username"
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('email')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:14px;" x-data="{ show: false }">
                        <label for="password" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Mật khẩu mới <span style="color:var(--v-copper);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" id="password" name="password"
                                placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password"
                                class="v-input" style="height:44px;font-size:14px;padding-right:44px;" />
                            <button type="button" @click="show = !show"
                                style="position:absolute;right:0;top:0;height:44px;width:44px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:var(--v-muted);">
                                <span class="material-symbols-outlined" style="font-size:18px;" x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div style="margin-bottom:24px;">
                        <label for="password_confirmation" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Xác nhận mật khẩu <span style="color:var(--v-copper);">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Nhập lại mật khẩu mới" required autocomplete="new-password"
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('password_confirmation')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:48px;">
                        Đặt Lại Mật Khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
