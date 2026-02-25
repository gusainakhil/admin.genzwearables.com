<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $sizes = Size::all();
        $colors = Color::all();

        return view('admin.products.create', compact('categories', 'sizes', 'colors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'base_price' => 'nullable|numeric|min:0',
            'gender' => 'nullable|in:men,women,kids,unisex',
            'is_custom' => 'boolean',
            'status' => 'required|in:active,inactive',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_custom'] = $request->has('is_custom');

        $product = Product::create($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product created successfully. Now add images and variants.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        $sizes = Size::all();
        $colors = Color::all();
        $product->load('images', 'variants.size', 'variants.color');

        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'base_price' => 'nullable|numeric|min:0',
            'gender' => 'nullable|in:men,women,kids,unisex',
            'is_custom' => 'boolean',
            'status' => 'required|in:active,inactive',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_custom'] = $request->has('is_custom');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function addImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $uploaded = $request->file('image');
        $manager = new ImageManager(new Driver);
        $image = $manager->read($uploaded->getPathname());
        $webp = $image->toWebp(80);
        $filename = 'products/'.Str::uuid().'.webp';
        \Storage::disk('public')->put($filename, (string) $webp);

        ProductImage::create([
            'product_id' => $product->id,
            'image' => $filename,
            'is_primary' => $product->images()->count() === 0,
        ]);

        return back()->with('success', 'Image added successfully');
    }

    public function deleteImage(ProductImage $image)
    {
        \Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Image deleted successfully');
    }

    public function addVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['product_id'] = $product->id;
        ProductVariant::create($validated);

        return back()->with('success', 'Variant added successfully');
    }

    public function deleteVariant(ProductVariant $variant)
    {
        $variant->update([
            'status' => 'inactive',
        ]);

        return back()->with('success', 'Variant deactivated successfully');
    }

    public function updateVariantStatus(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $variant->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Variant status updated successfully');
    }
}
