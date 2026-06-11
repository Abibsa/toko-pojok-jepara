<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Toko Pojok',
            'email' => 'admin@tokopojok.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Raya Pojok No. 123, Jepara'
        ]);

        // Create Customer Users
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = User::create([
                'name' => "Customer $i",
                'email' => "customer$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '0812345678' . sprintf('%02d', $i),
                'address' => "Alamat Customer $i, Jepara"
            ]);
        }

        // Create Categories
        $categories = [
            ['name' => 'Sembako', 'description' => 'Kebutuhan pokok sehari-hari'],
            ['name' => 'Minuman', 'description' => 'Berbagai jenis minuman'],
            ['name' => 'Snack', 'description' => 'Makanan ringan dan cemilan'],
            ['name' => 'Kebersihan', 'description' => 'Produk kebersihan rumah tangga'],
            ['name' => 'Bumbu Dapur', 'description' => 'Bumbu dan rempah masakan'],
            ['name' => 'Perawatan Diri', 'description' => 'Produk perawatan pribadi']
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description']
            ]);
        }

        // Create Products with varied stock
        $products = [
            // Sembako
            ['name' => 'Beras Premium 5kg', 'category' => 'Sembako', 'price' => 75000, 'wholesale' => 70000, 'stock' => 25, 'image' => 'beras-premium-5kg.jpg'],
            ['name' => 'Minyak Goreng 2L', 'category' => 'Sembako', 'price' => 35000, 'wholesale' => 32000, 'stock' => 8, 'image' => 'minyak-goreng-2l.jpg'], // Kritis
            ['name' => 'Gula Pasir 1kg', 'category' => 'Sembako', 'price' => 15000, 'wholesale' => 14000, 'stock' => 0, 'image' => 'gula-pasir-1kg.jpg'], // Habis
            ['name' => 'Tepung Terigu 1kg', 'category' => 'Sembako', 'price' => 12000, 'wholesale' => 11000, 'stock' => 15, 'image' => 'tepung-terigu-1kg.jpg'],
            ['name' => 'Telur Ayam 1kg', 'category' => 'Sembako', 'price' => 28000, 'wholesale' => 26000, 'stock' => 5, 'image' => 'telur-ayam-1kg.jpg'], // Kritis
            
            // Minuman
            ['name' => 'Air Mineral 600ml', 'category' => 'Minuman', 'price' => 3000, 'wholesale' => 2500, 'stock' => 50, 'image' => 'air-mineral-600ml.jpg'],
            ['name' => 'Teh Botol Sosro', 'category' => 'Minuman', 'price' => 5000, 'wholesale' => 4500, 'stock' => 30, 'image' => 'teh-botol-sosro.jpg'],
            ['name' => 'Kopi Kapal Api', 'category' => 'Minuman', 'price' => 2000, 'wholesale' => 1800, 'stock' => 0, 'image' => 'kopi-kapal-api.jpg'], // Habis
            ['name' => 'Susu UHT 250ml', 'category' => 'Minuman', 'price' => 8000, 'wholesale' => 7500, 'stock' => 20, 'image' => 'susu-uht-250ml.jpg'],
            ['name' => 'Jus Buah Kemasan', 'category' => 'Minuman', 'price' => 12000, 'wholesale' => 11000, 'stock' => 7, 'image' => 'jus-buah-kemasan.jpg'], // Kritis
            
            // Snack
            ['name' => 'Keripik Singkong', 'category' => 'Snack', 'price' => 8000, 'wholesale' => 7000, 'stock' => 40, 'image' => 'keripik-singkong.jpg'],
            ['name' => 'Biskuit Marie', 'category' => 'Snack', 'price' => 6000, 'wholesale' => 5500, 'stock' => 25, 'image' => 'biskuit-marie.jpg'],
            ['name' => 'Permen Mint', 'category' => 'Snack', 'price' => 3000, 'wholesale' => 2700, 'stock' => 0, 'image' => 'permen-mint.jpg'], // Habis
            ['name' => 'Coklat Batang', 'category' => 'Snack', 'price' => 15000, 'wholesale' => 14000, 'stock' => 12, 'image' => 'coklat-batang.jpg'],
            ['name' => 'Kacang Goreng', 'category' => 'Snack', 'price' => 10000, 'wholesale' => 9000, 'stock' => 6, 'image' => 'kacang-goreng.jpg'], // Kritis
            
            // Kebersihan
            ['name' => 'Sabun Cuci Piring', 'category' => 'Kebersihan', 'price' => 8000, 'wholesale' => 7500, 'stock' => 18, 'image' => 'sabun-cuci-piring.jpg'],
            ['name' => 'Deterjen Bubuk 1kg', 'category' => 'Kebersihan', 'price' => 25000, 'wholesale' => 23000, 'stock' => 9, 'image' => 'deterjen-bubuk-1kg.jpg'], // Kritis
            ['name' => 'Pembersih Lantai', 'category' => 'Kebersihan', 'price' => 12000, 'wholesale' => 11000, 'stock' => 22, 'image' => 'pembersih-lantai.jpg'],
            ['name' => 'Tissue Toilet', 'category' => 'Kebersihan', 'price' => 15000, 'wholesale' => 14000, 'stock' => 0, 'image' => 'tissue-toilet.jpg'], // Habis
            ['name' => 'Sabun Mandi Cair', 'category' => 'Kebersihan', 'price' => 18000, 'wholesale' => 16500, 'stock' => 14, 'image' => 'sabun-mandi-cair.jpg'],
            
            // Bumbu Dapur
            ['name' => 'Garam Dapur 250g', 'category' => 'Bumbu Dapur', 'price' => 3000, 'wholesale' => 2800, 'stock' => 35, 'image' => 'garam-dapur-250g.jpg'],
            ['name' => 'Merica Bubuk', 'category' => 'Bumbu Dapur', 'price' => 8000, 'wholesale' => 7500, 'stock' => 16, 'image' => 'merica-bubuk.jpg'],
            ['name' => 'Bawang Putih 250g', 'category' => 'Bumbu Dapur', 'price' => 12000, 'wholesale' => 11000, 'stock' => 8, 'image' => 'bawang-putih-250g.jpg'], // Kritis
            ['name' => 'Cabai Merah 250g', 'category' => 'Bumbu Dapur', 'price' => 15000, 'wholesale' => 14000, 'stock' => 5, 'image' => 'cabai-merah-250g.jpg'], // Kritis
            ['name' => 'Kemiri 100g', 'category' => 'Bumbu Dapur', 'price' => 10000, 'wholesale' => 9500, 'stock' => 20, 'image' => 'kemiri-100g.jpg'],
            
            // Perawatan Diri
            ['name' => 'Shampo Sachet', 'category' => 'Perawatan Diri', 'price' => 2000, 'wholesale' => 1800, 'stock' => 45, 'image' => 'shampo-sachet.jpg'],
            ['name' => 'Pasta Gigi', 'category' => 'Perawatan Diri', 'price' => 12000, 'wholesale' => 11000, 'stock' => 0, 'image' => 'pasta-gigi.jpg'], // Habis
            ['name' => 'Sikat Gigi', 'category' => 'Perawatan Diri', 'price' => 8000, 'wholesale' => 7500, 'stock' => 28, 'image' => 'sikat-gigi.jpg'],
            ['name' => 'Deodorant Spray', 'category' => 'Perawatan Diri', 'price' => 25000, 'wholesale' => 23000, 'stock' => 7, 'image' => 'deodorant-spray.jpg'], // Kritis
            ['name' => 'Sabun Batang', 'category' => 'Perawatan Diri', 'price' => 5000, 'wholesale' => 4500, 'stock' => 32, 'image' => 'sabun-batang.jpg']
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            $product = Product::create([
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => 'Deskripsi untuk ' . $productData['name'],
                'image' => $productData['image'],
                'price' => $productData['price'],
                'wholesale_price' => $productData['wholesale'],
                'unit' => 'pcs'
            ]);

            // Create stock
            Stock::create([
                'product_id' => $product->id,
                'quantity' => $productData['stock']
            ]);

            // Create initial stock history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity_before' => 0,
                'quantity_after' => $productData['stock'],
                'quantity_change' => $productData['stock'],
                'note' => 'Stok awal',
                'user_id' => $admin->id
            ]);
        }

        // Create dummy orders for K-Means data
        $products = Product::all();
        
        for ($i = 1; $i <= 100; $i++) {
            $customer = $customers[array_rand($customers)];
            $orderDate = now()->subDays(rand(1, 30));
            
            $order = Order::create([
                'user_id' => $customer->id,
                'order_code' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'total_amount' => 0,
                'payment_method' => 'transfer',
                'payment_status' => 'paid',
                'shipping_address' => $customer->address,
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);

            // Add random items to order
            $itemCount = rand(1, 5);
            $totalAmount = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 10);
                $price = $quantity >= 5 ? $product->wholesale_price : $product->price;
                $subtotal = $price * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal
                ]);
                
                $totalAmount += $subtotal;
            }
            
            $order->update(['total_amount' => $totalAmount]);
        }

        echo "Database seeded successfully!\n";
        echo "Admin: admin@tokopojok.com / password\n";
        echo "Customer: customer1@example.com / password\n";
    }
}
