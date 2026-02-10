<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    // Size methods
    public function sizesIndex()
    {
        $sizes = Size::all();

        return view('admin.attributes.sizes', compact('sizes'));
    }

    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        Size::create($validated);

        return back()->with('success', 'Size added successfully');
    }

    public function destroySize(Size $size)
    {
        $size->delete();

        return back()->with('success', 'Size deleted successfully');
    }

    // Color methods
    public function colorsIndex()
    {
        $colors = Color::all();

        return view('admin.attributes.colors', compact('colors'));
    }

    public function storeColor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'hex_code' => 'nullable|string|max:10',
        ]);

        Color::create($validated);

        return back()->with('success', 'Color added successfully');
    }

    public function destroyColor(Color $color)
    {
        $color->delete();

        return back()->with('success', 'Color deleted successfully');
    }
}
