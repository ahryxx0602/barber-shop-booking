@extends('layouts.client')

@section('title', 'Xác nhận mật khẩu')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
    <div style="width:100%;max-width:440px;">
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;">
            <div style="padding:32px 32px 0;text-align:center;">
                <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;margin-bottom:20px;">
                    <span style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);letter-spacing:2px;">✦ CLASSIC CUT</span>
                </a>
                <div class="v-ornament" style="max-width:260px;margin:0 auto 16px;">
                    Xác nhận mật khẩu
                </div>
            </div>

            <div style="padding:24px 32px 32px;">
                {{-- Decorative icon --}}
                <div style="text-align:center;margin-bottom:16px;">
                    <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-copper);opacity:0.6;">lock</span>
                </div>

                <p style="font-size:13px;line-height:1.7;color:var(--v-muted);text-align:center;margin-bottom:20px;">
                    Đây là khu vực bảo mật. Vui lòng nhập mật khẩu để tiếp tục.
                </p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    <div style="margin-bottom:20px;" x-data="{ show: false }">
                        <label for="password" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Mật khẩu <span style="color:var(--v-copper);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" id="password" name="password"
                                placeholder="Nhập mật khẩu" required autocomplete="current-password"
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

                    <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:48px;">
                        Xác Nhận
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
