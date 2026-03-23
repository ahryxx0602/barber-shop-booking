@extends('layouts.client')

@section('title', 'Xác thực email')

@section('content')
<section style="background:var(--v-cream);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
    <div style="width:100%;max-width:440px;">
        <div style="border:1px solid var(--v-rule);background:#fff;box-shadow:5px 5px 0 var(--v-copper);overflow:hidden;">
            <div style="padding:32px 32px 0;text-align:center;">
                <a href="{{ url('/') }}" style="text-decoration:none;display:inline-block;margin-bottom:20px;">
                    <span style="font-family:var(--font-serif);font-size:22px;font-weight:700;color:var(--v-ink);letter-spacing:2px;">✦ CLASSIC CUT</span>
                </a>
                <div class="v-ornament" style="max-width:240px;margin:0 auto 16px;">
                    Xác thực email
                </div>
            </div>

            <div style="padding:24px 32px 32px;">
                {{-- Decorative icon --}}
                <div style="text-align:center;margin-bottom:20px;">
                    <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-copper);opacity:0.6;">mark_email_read</span>
                </div>

                <p style="font-size:13px;line-height:1.7;color:var(--v-muted);text-align:center;margin-bottom:20px;">
                    Cảm ơn bạn đã đăng ký! Vui lòng xác thực email bằng cách nhấn vào link trong email chúng tôi vừa gửi. Nếu chưa nhận được, nhấn nút bên dưới để gửi lại.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div style="padding:10px 14px;margin-bottom:16px;background:rgba(22,163,74,0.06);border:1px solid rgba(22,163,74,0.2);font-size:12px;color:#15803d;text-align:center;">
                        Link xác thực mới đã được gửi đến email của bạn.
                    </div>
                @endif

                <div style="display:flex;align-items:center;gap:12px;">
                    <form method="POST" action="{{ route('verification.send') }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="v-btn-primary" style="width:100%;justify-content:center;height:44px;font-size:9px;">
                            Gửi Lại Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            style="height:44px;padding:0 20px;background:none;border:1px solid var(--v-rule);cursor:pointer;font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);transition:all 0.2s;"
                            onmouseover="this.style.color='var(--v-copper)';this.style.borderColor='var(--v-copper)'" onmouseout="this.style.color='var(--v-muted)';this.style.borderColor='var(--v-rule)'">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
