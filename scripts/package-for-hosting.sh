#!/bin/bash
# ============================================================
# Classic Cut — Script đóng gói cho InfinityFree
# ============================================================
# Script này sẽ:
#   1. Clone project sang thư mục tạm
#   2. Cài vendor (production only)
#   3. Build assets (npm run build)
#   4. Xóa mọi file dev không cần thiết
#   5. Nén thành file ZIP sẵn sàng upload
#
# Cách dùng:
#   chmod +x scripts/package-for-hosting.sh
#   ./scripts/package-for-hosting.sh
# ============================================================

set -e  # Dừng ngay nếu có lỗi

# --- Kiểm tra dependencies ---
if ! command -v zip &> /dev/null; then
    echo "❌ Lệnh 'zip' chưa được cài đặt!"
    echo "   Chạy: sudo apt install zip -y"
    exit 1
fi

# --- Cấu hình ---
PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BUILD_DIR="/tmp/classic-cut-build"
OUTPUT_FILE="$PROJECT_DIR/classic-cut-deploy.zip"

echo ""
echo "╔══════════════════════════════════════════════════╗"
echo "║   🏗️  Classic Cut — Đóng gói cho InfinityFree   ║"
echo "╚══════════════════════════════════════════════════╝"
echo ""

# --- Bước 1: Clone project ---
echo "📁 [1/5] Clone project sang thư mục tạm..."
rm -rf "$BUILD_DIR"
git clone "$PROJECT_DIR" "$BUILD_DIR" --depth=1
echo "   ✅ Clone xong → $BUILD_DIR"

cd "$BUILD_DIR"

# --- Bước 2: Cài dependencies ---
echo ""
echo "📦 [2/5] Cài composer dependencies (production only)..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet
echo "   ✅ Composer done (no-dev)"

echo ""
echo "📦 [3/5] Cài npm dependencies & build assets..."
npm ci --silent 2>/dev/null || npm install --silent
npm run build
echo "   ✅ Assets built → public/build/"

# --- Bước 3: Xóa mọi thứ không cần thiết ---
echo ""
echo "🧹 [4/5] Dọn dẹp file dev..."

# Node modules (200MB+)
rm -rf node_modules
echo "   ✗ node_modules/"

# Git history
rm -rf .git
echo "   ✗ .git/"

# Tests
rm -rf tests
echo "   ✗ tests/"

# Dev config files
rm -f phpunit.xml .phpunit.result.cache
rm -f .editorconfig .gitattributes .gitignore
echo "   ✗ phpunit.xml, .editorconfig, .gitattributes, .gitignore"

# Frontend tooling (đã build xong, không cần nữa)
rm -f vite.config.js tailwind.config.js postcss.config.js
rm -f package.json package-lock.json
echo "   ✗ vite.config.js, tailwind.config.js, postcss.config.js, package*.json"

# Documentation
rm -f README.md GEMINI.md LICENSE Caching.md Monitoring.md Security.md TestReport.md
rm -rf docs example .agent
echo "   ✗ *.md, docs/, example/, .agent/"

# Storage cleanup
rm -f storage/logs/*.log
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
echo "   ✗ storage/logs, cache, sessions, views"

# Đảm bảo các thư mục storage tồn tại (Laravel cần)
mkdir -p storage/logs
mkdir -p storage/framework/{cache/data,sessions,views}
touch storage/logs/.gitkeep
touch storage/framework/cache/data/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep
echo "   ✅ Giữ lại cấu trúc storage/"

# Rename .env.production → .env (sẵn sàng dùng)
if [ -f .env.production ]; then
    cp .env.production .env
    echo "   ✅ .env.production → .env (cần sửa DB credentials trên hosting)"
fi

# --- Bước 4: Nén ZIP ---
echo ""
echo "📦 [5/5] Nén thành file ZIP..."
cd /tmp
rm -f "$OUTPUT_FILE"
zip -r "$OUTPUT_FILE" classic-cut-build/ -q
echo "   ✅ Đã tạo: $OUTPUT_FILE"

# --- Hiển thị kết quả ---
ZIP_SIZE=$(du -h "$OUTPUT_FILE" | cut -f1)
echo ""
echo "╔══════════════════════════════════════════════════╗"
echo "║   ✅  Đóng gói hoàn tất!                        ║"
echo "╠══════════════════════════════════════════════════╣"
echo "   📄 File: $OUTPUT_FILE"
echo "   📏 Dung lượng: $ZIP_SIZE"
echo "╠══════════════════════════════════════════════════╣"
echo "║   📋  Các bước tiếp theo:                       ║"
echo "╠══════════════════════════════════════════════════╣"
echo "   1. Upload file ZIP lên htdocs/ qua FTP/File Manager"
echo "   2. Giải nén và di chuyển file lên htdocs/"
echo "   3. Sửa .env: DB_HOST, DB_DATABASE, DB_USERNAME,"
echo "      DB_PASSWORD, APP_URL, DEPLOY_TOKEN"
echo "   4. Truy cập: https://domain/deploy?token=YOUR_TOKEN"
echo "   5. Chạy lần lượt: clear-cache → migrate → storage-link"
echo "      → config-cache → optimize"
echo "   6. ⚠️  XÓA routes/deploy-helpers.php sau khi xong!"
echo "╚══════════════════════════════════════════════════╝"
echo ""

# Dọn thư mục tạm
rm -rf "$BUILD_DIR"
echo "🧹 Đã xóa thư mục tạm $BUILD_DIR"
echo "🎉 Done!"
