@extends('admin.layout')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="w-full space-y-6">
    <!-- Product Details -->
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Product Details</h3>
        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Short Description</label>
                    <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="255"
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Category</label>
                    <select name="category_id" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Base Price</label>
                    <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Gender</label>
                    <select name="gender" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                        <option value="">Select Gender</option>
                        <option value="men" {{ old('gender', $product->gender) == 'men' ? 'selected' : '' }}>Men</option>
                        <option value="women" {{ old('gender', $product->gender) == 'women' ? 'selected' : '' }}>Women</option>
                        <option value="kids" {{ old('gender', $product->gender) == 'kids' ? 'selected' : '' }}>Kids</option>
                        <option value="unisex" {{ old('gender', $product->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Description</label>
                    <div id="description-editor" class="bg-white border border-rose-200/60 rounded-2xl min-h-[200px] mb-4"></div>
                    <textarea name="description" id="description-input" class="hidden">{!! old('description', $product->description) !!}</textarea>
                </div>
                <br>
                <!-- SEO & OG Meta Tags -->
                <div class="col-span-2 rounded-2xl border border-amber-200/60 bg-amber-50/30 p-6 mt-4">
                    <h4 class="text-sm font-semibold text-stone-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-search text-amber-600"></i>
                        SEO & Social Media Meta Tags
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="255"
                                placeholder="SEO optimized title (60 chars recommended)"
                                class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Meta Description</label>
                            <textarea name="meta_description" rows="3" maxlength="500"
                                placeholder="SEO meta description (160 chars recommended)"
                                class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Open Graph Title</label>
                            <input type="text" name="og_title" value="{{ old('og_title', $product->og_title) }}" maxlength="255"
                                placeholder="Title for social media sharing (Facebook, LinkedIn, etc.)"
                                class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Open Graph Description</label>
                            <textarea name="og_description" rows="3" maxlength="500"
                                placeholder="Description for social media sharing"
                                class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">{{ old('og_description', $product->og_description) }}</textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Open Graph Image URL</label>
                            <input type="text" name="og_image" value="{{ old('og_image', $product->og_image) }}" maxlength="500"
                                placeholder="https://example.com/image.jpg (1200x630px recommended)"
                                class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                            <p class="mt-1 text-xs text-stone-500">
                                <i class="fas fa-info-circle"></i> Full URL of the image to show when shared on social media
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-14">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="mt-14">
                    <label class="flex items-center gap-3 mt-8">
                        <input type="checkbox" name="is_custom" value="1" {{ old('is_custom', $product->is_custom) ? 'checked' : '' }} 
                            class="h-4 w-4 rounded border-rose-200 text-rose-600 focus:ring-rose-300">
                        <span class="text-sm font-semibold text-stone-700">Is Custom Product</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-6">
                <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center rounded-full border border-rose-200/70 bg-white px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:border-rose-300 hover:text-stone-800">
                    Back
                </a>
            </div>
        </form>
    </div>

    <!-- Product Images -->
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Product Images</h3>
        
        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data" class="mb-6">
            @csrf
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[240px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Add Image</label>
                    <input type="file" name="image" accept="image/*" 
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm file:mr-4 file:rounded-full file:border-0 file:bg-rose-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-rose-700 hover:file:bg-rose-100" required>
                </div>
                <button type="submit" class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-6 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                    Upload
                </button>
            </div>
        </form>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach($product->images as $image)
                <div class="relative rounded-2xl border border-rose-100 bg-white p-2">
                    <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded">
                    @if($image->is_primary)
                        <span class="absolute top-2 left-2 rounded-full bg-emerald-500 text-white text-xs px-3 py-1">Primary</span>
                    @endif
                    <form action="{{ route('admin.products.images.destroy', $image) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-full bg-rose-500 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-rose-600">
                            Delete
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Product Variants -->
    <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-6">
        <h3 class="text-lg font-semibold text-stone-900 mb-4">Product Variants</h3>
        
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="mb-6 rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-7">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Size</label>
                    <select name="size_id" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="">Select</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Color</label>
                    <select name="color_id" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="">Select</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">SKU</label>
                    <input type="text" name="sku" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Price</label>
                    <input type="number" step="0.01" name="price" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Stock</label>
                    <input type="number" name="stock_qty" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-rose-200/60 bg-white px-4 py-2 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600">
                        Add Variant
                    </button>
                </div>
            </div>
        </form>

        <table class="w-full">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Size</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Color</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100/70">
                @foreach($product->variants as $variant)
                <tr class="transition hover:bg-rose-50/60">
                    <td class="px-4 py-3 font-semibold text-stone-900">{{ $variant->size->name }}</td>
                    <td class="px-4 py-3 text-stone-700">
                        <span class="inline-flex items-center">
                            @if($variant->color->hex_code)
                                <span class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $variant->color->hex_code }}"></span>
                            @endif
                            {{ $variant->color->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-stone-700">{{ $variant->sku }}</td>
                    <td class="px-4 py-3 text-stone-700">₹{{ number_format($variant->price, 2) }}</td>
                    <td class="px-4 py-3 text-stone-700">{{ $variant->stock_qty }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $variant->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ ucfirst($variant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.products.variants.status', $variant) }}" method="POST" onsubmit="return confirm('Update variant status?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $variant->status === 'active' ? 'inactive' : 'active' }}">
                            <button type="submit" class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] {{ $variant->status === 'active' ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition">
                                {{ $variant->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <style>
        #description-editor { min-height: 260px; margin-bottom: 1rem; }
        #description-editor.ql-container { min-height: 200px; }
        #description-editor .ql-editor { min-height: 160px; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('description-input');
            const quill = new Quill('#description-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            if (input.value) {
                quill.root.innerHTML = input.value;
            }

            quill.on('text-change', function () {
                input.value = quill.root.innerHTML;
            });
        });
    </script>
@endpush
