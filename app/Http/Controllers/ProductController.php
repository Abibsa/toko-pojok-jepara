<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stock', 'cluster']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Stock filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'available':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 10);
                    });
                    break;
                case 'limited':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    });
                    break;
                case 'out_of_stock':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '<=', 0);
                    });
                    break;
            }
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->whereHas('cluster', function($q) use ($request) {
                $q->where('priority_level', $request->priority);
            });
        }

        $sortBy = $request->get('sort', 'name');
        $sortOrder = strtolower($request->get('order')) === 'desc' ? 'desc' : 'asc';
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'stock':
                $query->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
                      ->orderBy('stocks.quantity', $sortOrder)
                      ->select('products.*');
                break;
            default:
                $query->orderBy('name', $sortOrder);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('produk.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stock', 'cluster']);
        
        // Get related products from same category
        $relatedProducts = Product::with(['stock', 'cluster'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('stock', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->limit(4)
            ->get();

        return view('produk.show', compact('product', 'relatedProducts'));
    }

    public function byCategory(Category $category, Request $request)
    {
        $query = Product::with(['stock', 'cluster'])
            ->where('category_id', $category->id);

        // Search within category
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Stock filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'available':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 10);
                    });
                    break;
                case 'limited':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    });
                    break;
                case 'out_of_stock':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '<=', 0);
                    });
                    break;
            }
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('produk.index', compact('products', 'categories'));
    }
}