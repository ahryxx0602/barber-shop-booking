<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - Đang bảo trì</title>
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
            font-family: 'DM Sans', sans-serif; background: var(--v-parchment);
            color: var(--v-ink); min-height: 100vh; display: flex; align-items: center;
            justify-content: center; overflow: hidden; position: relative;
        }
        /* Vintage paper texture with diagonal stripes */
        body::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(
                -45deg,
                transparent, transparent 40px,
                rgba(176,137,104,0.04) 40px, rgba(176,137,104,0.04) 41px
            );
            pointer-events: none;
        }
        body::after {
            content: '503'; font-family: 'Cormorant Garamond', serif;
            font-size: clamp(200px, 40vw, 500px); font-weight: 300;
            color: var(--v-rule); position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -55%); z-index: 0; line-height: 1;
            opacity: 0.3; pointer-events: none;
        }
        .error-container {
            position: relative; z-index: 1; text-align: center;
            max-width: 540px; padding: 24px;
        }
        /* "Closed" sign card */
        .sign-card {
            background: var(--v-cream); border: 2px solid var(--v-copper);
            box-shadow: 6px 6px 0 var(--v-copper); padding: 40px 32px;
            margin-bottom: 32px; position: relative;
            animation: swing 4s ease-in-out infinite;
            transform-origin: top center;
        }
        @keyframes swing {
            0%, 100% { transform: rotate(-1deg); }
            50% { transform: rotate(1deg); }
        }
        .sign-card::before {
            content: ''; position: absolute; top: -12px; left: 50%;
            transform: translateX(-50%); width: 40px; height: 6px;
            background: var(--v-copper); border-radius: 3px;
        }
        .sign-label {
            font-family: 'DM Sans', sans-serif; font-size: 10px;
            font-weight: 600; letter-spacing: 4px; text-transform: uppercase;
            color: var(--v-copper); margin-bottom: 12px;
        }
        .sign-title {
            font-family: 'Playfair Display', serif; font-size: clamp(28px, 5vw, 42px);
            font-weight: 900; color: var(--v-ink); margin-bottom: 8px;
        }
        .sign-subtitle {
            font-family: 'Cormorant Garamond', serif; font-size: 20px;
            color: var(--v-copper); font-style: italic;
        }
        .v-ornament {
            display: flex; align-items: center; justify-content: center;
            gap: 16px; margin: 16px 0;
        }
        .v-ornament::before, .v-ornament::after {
            content: ''; flex: 1; max-width: 60px; height: 1px; background: var(--v-rule);
        }
        .v-ornament span { color: var(--v-copper); font-size: 14px; letter-spacing: 4px; }
        .description {
            font-size: 15px; color: var(--v-muted); line-height: 1.7; margin-bottom: 32px;
        }
        /* Barber pole animation */
        .barber-pole {
            width: 8px; height: 60px; margin: 0 auto 24px;
            background: repeating-linear-gradient(
                -45deg,
                var(--v-copper), var(--v-copper) 5px,
                var(--v-cream) 5px, var(--v-cream) 10px,
                var(--v-ink) 10px, var(--v-ink) 15px,
                var(--v-cream) 15px, var(--v-cream) 20px
            );
            background-size: 28px 28px;
            animation: pole 1s linear infinite;
            border: 1px solid var(--v-rule);
        }
        @keyframes pole {
            0% { background-position: 0 0; }
            100% { background-position: 0 28px; }
        }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .v-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px;
            font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase; text-decoration: none;
            border: 1px solid var(--v-copper); color: #fff;
            background: var(--v-ink); cursor: pointer; transition: all 0.3s;
            box-shadow: 4px 4px 0 var(--v-copper);
        }
        .v-btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 var(--v-copper); }
        .corner { position: fixed; width: 60px; height: 60px; opacity: 0.15; }
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
        <div class="barber-pole"></div>

        <div class="sign-card">
            <p class="sign-label">✦ CLASSIC CUT ✦</p>
            <h1 class="sign-title">Đang Bảo Trì</h1>
            <div class="v-ornament"><span>✦</span></div>
            <p class="sign-subtitle">Tiệm đang sửa sang, sẽ mở lại sớm thôi!</p>
        </div>

        <p class="description">
            Hệ thống đang được nâng cấp và bảo trì.
            Chúng tôi sẽ quay lại phục vụ bạn trong thời gian sớm nhất.
        </p>

        <div class="actions">
            <a href="javascript:location.reload()" class="v-btn">
                <span class="material-symbols-outlined" style="font-size:18px;">refresh</span>
                Kiểm tra lại
            </a>
        </div>
    </div>
</body>
</html>
