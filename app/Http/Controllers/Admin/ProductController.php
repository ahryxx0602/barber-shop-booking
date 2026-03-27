<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\CreateProductData;
use App\DTOs\Admin\UpdateProductData;
use App\Enums\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\Admin\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductRepositoryInterface $productRepo,
    ) {}

    /**
     * Danh sách sản phẩm + filter category + search + phân trang.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'search']);

        $products = $this->productService->getAll($filters, 10);

        // Stats cards
        $totalProducts  = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $outOfStock     = Product::where('stock_quantity', 0)->count();

        $categories = ProductCategory::cases();

        return view('admin.products.index', compact(
            'products', 'totalProducts', 'activeProducts', 'outOfStock',
            'categories', 'filters'
        ));
    }

    /**
     * Form tạo sản phẩm.
     */
    public function create(): View
    {
        $categories = ProductCategory::cases();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create(
            CreateProductData::fromRequest($request),
            $request->file('image'),
        );

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được thêm thành công.');
    }

    /**
     * Form sửa sản phẩm.
     */
    public function edit(Product $product): View
    {
        $categories = ProductCategory::cases();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update(
            $product,
            UpdateProductData::fromRequest($request),
            $request->file('image'),
        );

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    /**
     * Xóa sản phẩm (kiểm tra có order không).
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->productService->delete($product);
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được xóa.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.products.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle ẩn/hiện sản phẩm.
     */
    public function toggleActive(Product $product): RedirectResponse
    {
        $this->productRepo->update($product, ['is_active' => !$product->is_active]);

        $status = !$product->is_active ? 'kích hoạt' : 'tạm ẩn';

        return redirect()->route('admin.products.index')
            ->with('success', "Sản phẩm đã được {$status}.");
    }
}
