@extends('layouts.client')

@section('title', 'Quên mật khẩu')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
    <div style="width:100%;max-width:440px;">
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;">
            <div style="padding:32px 32px 0;text-align:center;">
                <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;margin-bottom:20px;">
                    <span style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);letter-spacing:2px;">✦ CLASSIC CUT</span>
                </a>
                <div class="v-ornament" style="max-width:240px;margin:0 auto 16px;">
                    Quên mật khẩu
                </div>
                <p style="color:var(--v-muted);font-size:13px;line-height:1.6;margin-bottom:4px;">
                    Nhập email đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu.
                </p>
            </div>

            <div style="padding:24px 32px 32px;">
                @if (session('status'))
                    <div style="padding:10px 14px;margin-bottom:16px;background:rgba(22,163,74,0.06);border:1px solid rgba(22,163,74,0.2);font-size:12px;color:#15803d;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label for="email" style="display:block;font-size:11px;font-weight:600;color:var(--v-muted);margin-bottom:5px;letter-spacing:1px;text-transform:uppercase;">
                            Email <span style="color:var(--v-copper);">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required autofocus
                            class="v-input" style="height:44px;font-size:14px;" />
                        @error('email')
                            <p style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:48px;">
                        Gửi Link Đặt Lại
                    </button>
                </form>

                <div style="text-align:center;margin-top:20px;padding-top:20px;border-top:1px solid var(--v-rule);">
                    <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--v-copper);text-decoration:none;font-weight:500;transition:color 0.2s;"
                        onmouseover="this.style.color='var(--v-ink)'" onmouseout="this.style.color='var(--v-copper)'">
                        <span class="material-symbols-outlined" style="font-size:14px;">arrow_back</span>
                        Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
