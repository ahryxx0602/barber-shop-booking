@extends('layouts.client')

@section('title', 'Đăng ký')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
    <div style="width:100%;max-width:440px;">
        {{-- Card --}}
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;">
            {{-- Header --}}
            <div style="padding:32px 32px 0;text-align:center;">
                <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;margin-bottom:20px;">
                    <span style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);letter-spacing:2px;">✦ CLASSIC CUT</span>
                </a>
                <div class="v-ornament" style="max-width:200px;margin:0 auto 16px;">
                    Đăng ký
                </div>
                <p style="color:var(--v-muted);font-size:13px;margin-bottom:4px;">Tạo tài khoản để đặt lịch dễ dàng hơn</p>
            </div>

            {{-- Form --}}
            <div style="padding:24px 32px 32px;">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    {{-- Name --}}
                    <div style="margin-bottom:14px;">
                        <label for="name" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Họ và tên <span style="color:var(--v-copper);">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Nguyễn Văn A" required autofocus autocomplete="name"
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('name')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div style="margin-bottom:14px;">
                        <label for="email" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Email <span style="color:var(--v-copper);">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required autocomplete="username"
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('email')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:14px;" x-data="{ show: false }">
                        <label for="reg_password" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Mật khẩu <span style="color:var(--v-copper);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" id="reg_password" name="password"
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
                            placeholder="Nhập lại mật khẩu" required autocomplete="new-password"
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('password_confirmation')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:48px;">
                        Đăng Ký
                    </button>
                </form>

                {{-- Login link --}}
                <div style="text-align:center;margin-top:20px;padding-top:20px;border-top:1px solid var(--v-rule);">
                    <p style="font-size:13px;color:var(--v-muted);">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}" style="color:var(--v-copper);text-decoration:none;font-weight:600;transition:color 0.2s;"
                            onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Back to home --}}
        <div style="text-align:center;margin-top:20px;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;transition:color 0.2s;"
                onmouseover="this.style.color='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)'">
                <span class="material-symbols-outlined" style="font-size:14px;">arrow_back</span>
                Quay lại trang chủ
            </a>
        </div>
    </div>
</section>
@endsection