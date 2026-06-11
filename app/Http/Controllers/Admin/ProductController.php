<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stock']);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            $query->whereHas('stock', function ($q) use ($request) {
                if ($request->stock_status === 'available') {
                    $q->where('quantity', '>', 10);
                } elseif ($request->stock_status === 'low') {
                    $q->whereBetween('quantity', [1, 10]);
                } elseif ($request->stock_status === 'empty') {
                    $q->where('quantity', '<=', 0);
                }
            });
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.produk.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.produk.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = $imageName;
        }

        Product::create($validated);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $produk)
    {
        $produk->load(['category', 'stock', 'cluster']);
        return view('admin.produk.show', compact('produk'));
    }

    public function edit(Product $produk)
    {
        $categories = Category::all();
        return view('admin.produk.edit', compact('produk', 'categories'));
    }

    public function update(Request $request, Product $produk)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($produk->image && file_exists(public_path('images/products/' . $produk->image))) {
                unlink(public_path('images/products/' . $produk->image));
            }
            
            $image = $request->file('image');
            $imageName = Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = $imageName;
        }

        $produk->update($validated);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $produk)
    {
        // Delete image if exists
        if ($produk->image && file_exists(public_path('images/products/' . $produk->image))) {
            unlink(public_path('images/products/' . $produk->image));
        }
        
        $produk->delete();
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
