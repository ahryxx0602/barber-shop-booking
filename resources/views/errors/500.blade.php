<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Lỗi hệ thống</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --v-cream: #f5f0e8; --v-parchment: #ede5d0; --v-ink: #1c1713;
            --v-ink-soft: #3d3228; --v-muted: #8a7a6a; --v-copper: #b08968;
            --v-copper-dk: #8b6340; --v-surface: #ede8df; --v-rule: rgba(176,137,104,0.3);
        }
        body {
            font-family: 'DM Sans', sans-serif; background: var(--v-ink);
            color: var(--v-cream); min-height: 100vh; display: flex; align-items: center;
            justify-content: center; overflow: hidden; position: relative;
        }
        body::before {
            content: '500'; font-family: 'Cormorant Garamond', serif;
            font-size: clamp(200px, 40vw, 500px); font-weight: 300;
            color: rgba(176,137,104,0.08); position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -55%); z-index: 0; line-height: 1;
            pointer-events: none;
        }
        .error-container {
            position: relative; z-index: 1; text-align: center;
            max-width: 520px; padding: 24px;
        }
        /* Broken mirror effect */
        .error-icon {
            width: 90px; height: 90px; margin: 0 auto 24px;
            border: 2px solid var(--v-copper); position: relative;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .error-icon::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 40%, rgba(176,137,104,0.1) 40%, rgba(176,137,104,0.1) 41%, transparent 41%),
                        linear-gradient(225deg, transparent 60%, rgba(176,137,104,0.08) 60%, rgba(176,137,104,0.08) 61%, transparent 61%);
        }
        .error-icon .material-symbols-outlined {
            font-size: 40px; color: var(--v-copper);
            animation: flicker 3s ease-in-out infinite;
        }
        @keyframes flicker {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
            52% { opacity: 1; }
            54% { opacity: 0.5; }
            56% { opacity: 1; }
        }
        .v-ornament {
            display: flex; align-items: center; justify-content: center;
            gap: 16px; margin: 20px 0;
        }
        .v-ornament::before, .v-ornament::after {
            content: ''; flex: 1; max-width: 80px; height: 1px;
            background: rgba(176,137,104,0.3);
        }
        .v-ornament span { color: var(--v-copper); font-size: 14px; letter-spacing: 4px; }
        h1 {
            font-family: 'Playfair Display', serif; font-size: clamp(28px, 5vw, 42px);
            font-weight: 700; color: var(--v-cream); margin-bottom: 8px;
        }
        .subtitle {
            font-family: 'Cormorant Garamond', serif; font-size: 20px;
            color: var(--v-copper); font-style: italic; margin-bottom: 16px;
        }
        .description {
            font-size: 15px; color: var(--v-muted); line-height: 1.7; margin-bottom: 32px;
        }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .v-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px;
            font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase; text-decoration: none;
            border: 1px solid var(--v-copper); color: var(--v-ink);
            background: var(--v-copper); cursor: pointer; transition: all 0.3s;
            box-shadow: 4px 4px 0 rgba(176,137,104,0.3);
        }
        .v-btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 rgba(176,137,104,0.3); background: var(--v-copper-dk); }
        .v-btn-outline {
            display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px;
            font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase; text-decoration: none;
            border: 1px solid rgba(176,137,104,0.3); color: var(--v-cream);
            background: transparent; cursor: pointer; transition: all 0.2s;
        }
        .v-btn-outline:hover { border-color: var(--v-copper); color: var(--v-copper); }
        .corner { position: fixed; width: 60px; height: 60px; opacity: 0.1; }
        .corner::before, .corner::after { content: ''; position: absolute; background: var(--v-copper); }
        .corner-tl { top: 24px; left: 24px; }
        .corner-tl::before { top: 0; left: 0; width: 100%; height: 1px; }
        .corner-tl::after { top: 0; left: 0; width: 1px; height: 100%; }
        .corner-br { bottom: 24px; right: 24px; }
        .corner-br::before { bottom: 0; right: 0; width: 100%; height: 1px; }
        .corner-br::after { bottom: 0; right: 0; width: 1px; height: 100%; }
    </style>
</head>
<body>
    <div class="corner corner-tl"></div>
    <div class="corner corner-br"></div>

    <div class="error-container">
        <div class="error-icon">
            <span class="material-symbols-outlined">broken_image</span>
        </div>

        <div class="v-ornament"><span>✦ ✦ ✦</span></div>

        <h1>Lỗi hệ thống</h1>
        <p class="subtitle">Gương đã vỡ rồi...</p>
        <p class="description">
            Có lỗi xảy ra phía máy chủ. Đội ngũ kỹ thuật đã được thông báo.
            Xin vui lòng thử lại sau hoặc quay về trang chủ.
        </p>

        <div class="actions">
            <a href="/" class="v-btn">
                <span class="material-symbols-outlined" style="font-size:18px;">home</span>
                Trang chủ
            </a>
            <a href="javascript:location.reload()" class="v-btn-outline">
                <span class="material-symbols-outlined" style="font-size:18px;">refresh</span>
                Thử lại
            </a>
        </div>
    </div>
</body>
</html>
