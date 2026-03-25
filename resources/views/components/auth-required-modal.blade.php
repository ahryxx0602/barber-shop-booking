{{-- Modal yêu cầu đăng nhập --}}
<div id="auth-required-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;"
    onclick="if(event.target===this)closeAuthModal()">
    <div style="background:var(--v-cream);max-width:400px;width:90%;padding:32px 28px;text-align:center;box-shadow:6px 6px 0 var(--v-copper);border:1px solid var(--v-rule);position:relative;animation:authModalIn 0.25s ease-out;">
        {{-- Nút đóng --}}
        <button onclick="closeAuthModal()" type="button"
            style="position:absolute;top:12px;right:12px;background:none;border:none;cursor:pointer;color:var(--v-muted);font-size:20px;line-height:1;"
            aria-label="Đóng">&times;</button>

        {{-- Icon --}}
        <div style="margin-bottom:16px;">
            <span class="material-symbols-outlined" style="font-size:48px;color:var(--v-copper);">lock</span>
        </div>

        {{-- Tiêu đề --}}
        <h3 style="font-family:var(--font-serif);font-size:1.25rem;font-weight:700;color:var(--v-ink);margin-bottom:8px;">
            Vui lòng đăng nhập
        </h3>

        {{-- Mô tả --}}
        <p id="auth-modal-message" style="font-size:13px;color:var(--v-muted);line-height:1.6;margin-bottom:24px;">
            Bạn cần đăng nhập để sử dụng tính năng này.
        </p>

        {{-- Nút hành động --}}
        <div style="display:flex;gap:10px;justify-content:center;">
            <a href="{{ route('login') }}" class="v-btn-primary" style="flex:1;max-width:160px;font-size:10px;text-decoration:none;display:flex;align-items:center;justify-content:center;height:40px;">
                <span class="material-symbols-outlined" style="font-size:14px;margin-right:6px;">login</span>
                Đăng nhập
            </a>
            <a href="{{ route('register') }}" class="v-btn-secondary" style="flex:1;max-width:160px;font-size:10px;text-decoration:none;display:flex;align-items:center;justify-content:center;height:40px;">
                Đăng ký
            </a>
        </div>
    </div>
</div>

<style>
@keyframes authModalIn {
    from { opacity:0; transform:scale(0.95) translateY(10px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>

<script>
function openAuthModal(message) {
    const modal = document.getElementById('auth-required-modal');
    if (message) {
        document.getElementById('auth-modal-message').textContent = message;
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
    const modal = document.getElementById('auth-required-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Đóng modal khi bấm Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAuthModal();
});
</script>
