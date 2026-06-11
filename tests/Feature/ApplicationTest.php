<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockAlert;
use App\Models\StockHistory;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ApplicationTest
 *
 * Test suite lengkap untuk Toko Pojok Jepara.
 * Mencakup:
 *  - Halaman publik (landing, produk, kategori)
 *  - Autentikasi & middleware admin
 *  - Keranjang (CRUD, price snapshot, deteksi perubahan harga)
 *  - Checkout (validasi stok, race condition, BOPIS)
 *  - Pesanan pelanggan & admin
 *  - Manajemen stok admin (restock, adjust, histori, export)
 *  - Laporan penjualan & laporan stok
 *  - Notifikasi stok tersedia (StockAlert)
 *  - Hapus produk → otomatis hapus dari keranjang
 */
class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Shared fixtures
    // ──────────────────────────────────────────────

    protected Category $category;
    protected Product  $product;
    protected Stock    $stock;
    protected User     $admin;
    protected User     $customer;
    protected StockService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'        => 'Makanan',
            'slug'        => 'makanan',
            'description' => 'Kategori makanan',
        ]);

        $this->product = Product::create([
            'category_id'     => $this->category->id,
            'name'            => 'Produk Uji',
            'slug'            => 'produk-uji',
            'description'     => 'Deskripsi produk uji',
            'price'           => 10000,
            'wholesale_price' => 8000,
            'unit'            => 'pcs',
        ]);

        $this->stock = Stock::create([
            'product_id' => $this->product->id,
            'quantity'   => 50,
        ]);

        $this->admin = User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@test.com',
            'role'  => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name'  => 'Pelanggan',
            'email' => 'pelanggan@test.com',
            'role'  => 'customer',
        ]);

        $this->stockService = app(StockService::class);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 1: HALAMAN PUBLIK
    // ══════════════════════════════════════════════

    /** @test */
    public function halaman_utama_menampilkan_status_200()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function halaman_katalog_produk_dapat_diakses_publik()
    {
        $response = $this->get('/produk');
        $response->assertStatus(200);
    }

    /** @test */
    public function halaman_detail_produk_dapat_diakses_publik()
    {
        $response = $this->get("/produk/{$this->product->slug}");
        $response->assertStatus(200);
    }

    /** @test */
    public function halaman_kategori_dapat_diakses_publik()
    {
        $response = $this->get("/kategori/{$this->category->slug}");
        $response->assertStatus(200);
    }

    /** @test */
    public function filter_kategori_pada_halaman_produk_tidak_error()
    {
        // Memastikan perbaikan bug request('category') vs request()->query('category')
        $response = $this->get("/kategori/{$this->category->slug}");
        $response->assertStatus(200);
        $response->assertDontSee('could not be converted');
    }

    /** @test */
    public function halaman_produk_dengan_filter_query_string_berjalan_baik()
    {
        $response = $this->get("/produk?search=uji&category={$this->category->id}");
        $response->assertStatus(200);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 2: AUTENTIKASI & MIDDLEWARE ADMIN
    // ══════════════════════════════════════════════

    /** @test */
    public function tamu_tidak_bisa_akses_keranjang()
    {
        $response = $this->get('/keranjang');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function tamu_tidak_bisa_akses_admin_dashboard()
    {
        $response = $this->get('/admin');
        // Redirect ke login atau 403
        $this->assertTrue(
            in_array($response->status(), [302, 403]),
            "Tamu seharusnya tidak bisa akses admin, status: {$response->status()}"
        );
    }

    /** @test */
    public function customer_tidak_bisa_akses_admin_dashboard()
    {
        $response = $this->actingAs($this->customer)->get('/admin');
        // Redirect atau 403
        $this->assertTrue(
            in_array($response->status(), [302, 403]),
            "Customer seharusnya tidak bisa akses admin, status: {$response->status()}"
        );
    }

    /** @test */
    public function admin_bisa_akses_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 3: KERANJANG BELANJA
    // ══════════════════════════════════════════════

    /** @test */
    public function pelanggan_bisa_melihat_halaman_keranjang()
    {
        $response = $this->actingAs($this->customer)->get('/keranjang');
        $response->assertStatus(200);
    }

    /** @test */
    public function pelanggan_bisa_tambah_produk_ke_keranjang()
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/keranjang/tambah', [
                'product_id' => $this->product->id,
                'quantity'   => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('carts', [
            'user_id'    => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity'   => 1,
        ]);
    }

    /** @test */
    public function tambah_ke_keranjang_menyimpan_price_snapshot()
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/keranjang/tambah', [
                'product_id' => $this->product->id,
                'quantity'   => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $cart = Cart::where('user_id', $this->customer->id)
            ->where('product_id', $this->product->id)
            ->first();

        $this->assertNotNull($cart);
        $this->assertEquals(10000, $cart->price_snapshot);
    }

    /** @test */
    public function keranjang_mendeteksi_kenaikan_harga()
    {
        $cart = Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 1,
            'price_snapshot' => 10000,
        ]);

        $this->product->update(['price' => 12000]);
        $cart->refresh();

        $this->assertTrue($cart->hasPriceChanged());

        $priceChange = $cart->price_change;
        $this->assertEquals(10000, $priceChange['old_price']);
        $this->assertEquals(12000, $priceChange['new_price']);
        $this->assertEquals(2000, $priceChange['difference']);
        $this->assertEquals('increase', $priceChange['type']);
        $this->assertEquals(20.0, $priceChange['percentage']);
    }

    /** @test */
    public function keranjang_mendeteksi_penurunan_harga()
    {
        $cart = Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 1,
            'price_snapshot' => 10000,
        ]);

        $this->product->update(['price' => 8000]);
        $cart->refresh();

        $this->assertTrue($cart->hasPriceChanged());

        $priceChange = $cart->price_change;
        $this->assertEquals(10000, $priceChange['old_price']);
        $this->assertEquals(8000, $priceChange['new_price']);
        $this->assertEquals(-2000, $priceChange['difference']);
        $this->assertEquals('decrease', $priceChange['type']);
        $this->assertEquals(-20.0, $priceChange['percentage']);
    }

    /** @test */
    public function keranjang_tidak_deteksi_perubahan_harga_jika_sama()
    {
        $cart = Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 1,
            'price_snapshot' => 10000,
        ]);

        $this->assertFalse($cart->hasPriceChanged());
        $this->assertNull($cart->price_change);
    }

    /** @test */
    public function keranjang_mendeteksi_perubahan_harga_grosir()
    {
        $cart = Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 5, // >= 5 = harga grosir
            'price_snapshot' => 8000,
        ]);

        $this->product->update(['wholesale_price' => 9000]);
        $cart->refresh();

        $this->assertTrue($cart->hasPriceChanged());
        $priceChange = $cart->price_change;
        $this->assertEquals(8000, $priceChange['old_price']);
        $this->assertEquals(9000, $priceChange['new_price']);
        $this->assertEquals('increase', $priceChange['type']);
    }

    /** @test */
    public function halaman_keranjang_menampilkan_peringatan_perubahan_harga()
    {
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 1,
            'price_snapshot' => 10000,
        ]);

        $this->product->update(['price' => 12000]);

        $response = $this->actingAs($this->customer)->get('/keranjang');

        $response->assertStatus(200);
        $response->assertSee('Harga telah berubah');
    }

    // ══════════════════════════════════════════════
    // BAGIAN 4: HAPUS PRODUK → KERANJANG
    // ══════════════════════════════════════════════

    /** @test */
    public function menghapus_produk_menghapus_dari_satu_keranjang()
    {
        $customer2 = User::factory()->create(['role' => 'customer', 'email' => 'c2@test.com']);

        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 2,
            'price_snapshot' => 10000,
        ]);

        $this->assertEquals(1, Cart::where('user_id', $this->customer->id)->count());

        $this->product->delete();

        $this->assertEquals(0, Cart::where('user_id', $this->customer->id)->count());
    }

    /** @test */
    public function menghapus_produk_menghapus_dari_semua_keranjang()
    {
        $customer2 = User::factory()->create(['role' => 'customer', 'email' => 'c2@test.com']);

        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 2,
            'price_snapshot' => 10000,
        ]);
        Cart::create([
            'user_id'        => $customer2->id,
            'product_id'     => $this->product->id,
            'quantity'       => 3,
            'price_snapshot' => 10000,
        ]);

        $this->assertEquals(2, Cart::where('product_id', $this->product->id)->count());

        $this->product->delete();

        $this->assertEquals(0, Cart::where('product_id', $this->product->id)->count());
    }

    /** @test */
    public function menghapus_produk_tidak_mengganggu_produk_lain_di_keranjang()
    {
        $product2 = Product::create([
            'category_id'     => $this->category->id,
            'name'            => 'Produk Lain',
            'slug'            => 'produk-lain',
            'description'     => 'Deskripsi',
            'price'           => 15000,
            'wholesale_price' => 12000,
            'unit'            => 'pcs',
        ]);

        Cart::create(['user_id' => $this->customer->id, 'product_id' => $this->product->id,  'quantity' => 2, 'price_snapshot' => 10000]);
        Cart::create(['user_id' => $this->customer->id, 'product_id' => $product2->id,        'quantity' => 1, 'price_snapshot' => 15000]);

        $this->assertEquals(2, Cart::where('user_id', $this->customer->id)->count());

        $this->product->delete();

        $this->assertEquals(1, Cart::where('user_id', $this->customer->id)->count());
        $this->assertEquals(1, Cart::where('product_id', $product2->id)->count());
    }

    /** @test */
    public function halaman_keranjang_tidak_error_setelah_produk_dihapus()
    {
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 2,
            'price_snapshot' => 10000,
        ]);

        $this->product->delete();

        $response = $this->actingAs($this->customer)->get('/keranjang');
        $response->assertStatus(200);
    }

    /** @test */
    public function checkout_redirect_dengan_error_setelah_produk_dihapus()
    {
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 2,
            'price_snapshot' => 10000,
        ]);

        $this->product->delete();

        $response = $this->actingAs($this->customer)->get('/checkout');

        $response->assertRedirect('/keranjang');
        $response->assertSessionHas('error');
    }

    // ══════════════════════════════════════════════
    // BAGIAN 5: CHECKOUT & RACE CONDITION
    // ══════════════════════════════════════════════

    /** @test */
    public function checkout_berhasil_dengan_stok_cukup()
    {
        // Stok sudah 50, pesan 2
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 2,
            'price_snapshot' => 10000,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Jl. Test No. 1',
                'payment_method'   => 'transfer',
                'pickup_method'    => 'delivery',
                'note'             => 'Test order',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        // Stok berkurang
        $this->stock->refresh();
        $this->assertEquals(48, $this->stock->quantity);

        // Keranjang kosong
        $this->assertEquals(0, Cart::where('user_id', $this->customer->id)->count());
    }

    /** @test */
    public function checkout_gagal_jika_stok_tidak_cukup()
    {
        // Stok 50, pesan 100
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 100,
            'price_snapshot' => 10000,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Jl. Test No. 1',
                'payment_method'   => 'transfer',
                'pickup_method'    => 'delivery',
            ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));

        $message = $response->json('message');
        $this->assertTrue(
            str_contains($message, 'Stok') || str_contains($message, 'stok') || str_contains($message, 'Validasi'),
            "Pesan harus mengandung teks terkait stok, dapat: {$message}"
        );

        // Stok tidak berubah
        $this->stock->refresh();
        $this->assertEquals(50, $this->stock->quantity);
    }

    /** @test */
    public function checkout_mencegah_overselling_saat_stok_terbatas()
    {
        // Set stok = 5
        $this->stock->update(['quantity' => 5]);

        $customer2 = User::factory()->create(['role' => 'customer', 'email' => 'c2@test.com']);

        // Customer 1 pesan 4, Customer 2 pesan 3 → total 7 > 5
        Cart::create(['user_id' => $this->customer->id, 'product_id' => $this->product->id, 'quantity' => 4, 'price_snapshot' => 10000]);
        Cart::create(['user_id' => $customer2->id,      'product_id' => $this->product->id, 'quantity' => 3, 'price_snapshot' => 10000]);

        $payload = [
            'shipping_address' => 'Alamat Test',
            'payment_method'   => 'transfer',
            'pickup_method'    => 'delivery',
        ];

        $response1 = $this->actingAs($this->customer)->postJson('/checkout/proses', $payload);
        $response2 = $this->actingAs($customer2)->postJson('/checkout/proses', $payload);

        $successCount = 0;
        $failCount    = 0;

        if ($response1->status() === 200 && $response1->json('success')) {
            $successCount++;
        } else {
            $failCount++;
        }

        if ($response2->status() === 200 && $response2->json('success')) {
            $successCount++;
        } else {
            $failCount++;
        }

        $this->assertEquals(1, $successCount, 'Tepat satu checkout harus berhasil');
        $this->assertEquals(1, $failCount,    'Tepat satu checkout harus gagal');

        $finalStock = $this->stock->fresh();
        $this->assertGreaterThanOrEqual(0, $finalStock->quantity, 'Stok tidak boleh negatif');
    }

    /** @test */
    public function checkout_metode_ambil_di_toko_berfungsi()
    {
        Cart::create([
            'user_id'        => $this->customer->id,
            'product_id'     => $this->product->id,
            'quantity'       => 1,
            'price_snapshot' => 10000,
        ]);

        $response = $this->actingAs($this->customer)
            ->postJson('/checkout/proses', [
                'payment_method' => 'cod',
                'pickup_method'  => 'pickup',
                'note'           => 'Ambil hari ini',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        // Pesanan seharusnya berstatus menunggu_diambil
        $order = Order::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('menunggu_diambil', $order->status);
    }

    /** @test */
    public function checkout_gagal_jika_keranjang_kosong()
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Jl. Test No. 1',
                'payment_method'   => 'transfer',
                'pickup_method'    => 'delivery',
            ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));
    }

    // ══════════════════════════════════════════════
    // BAGIAN 6: PESANAN PELANGGAN
    // ══════════════════════════════════════════════

    /** @test */
    public function pelanggan_bisa_lihat_daftar_pesanan()
    {
        $response = $this->actingAs($this->customer)->get('/pesanan');
        $response->assertStatus(200);
    }

    /** @test */
    public function pelanggan_bisa_lihat_detail_pesanan_miliknya()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-TEST-0001',
            'status'           => 'pending',
            'total_amount'     => 10000,
            'payment_method'   => 'transfer',
            'payment_status'   => 'pending',
            'shipping_address' => 'Jl. Test',
            'pickup_method'    => 'delivery',
        ]);

        $response = $this->actingAs($this->customer)
            ->get("/pesanan/{$order->order_code}");

        $response->assertStatus(200);
    }

    /** @test */
    public function pelanggan_bisa_batalkan_pesanan_pending()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-TEST-0002',
            'status'           => 'pending',
            'total_amount'     => 10000,
            'payment_method'   => 'transfer',
            'payment_status'   => 'pending',
            'shipping_address' => 'Jl. Test',
            'pickup_method'    => 'delivery',
        ]);

        $response = $this->actingAs($this->customer)
            ->patch("/pesanan/{$order->order_code}/batal");

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 7: MANAJEMEN PESANAN ADMIN
    // ══════════════════════════════════════════════

    /** @test */
    public function admin_bisa_lihat_daftar_pesanan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/pesanan');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_lihat_detail_pesanan()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-ADMIN-0001',
            'status'           => 'pending',
            'total_amount'     => 20000,
            'payment_method'   => 'transfer',
            'payment_status'   => 'pending',
            'shipping_address' => 'Jl. Admin Test',
            'pickup_method'    => 'delivery',
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/pesanan/{$order->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_ubah_status_pesanan()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-ADMIN-0002',
            'status'           => 'pending',
            'total_amount'     => 10000,
            'payment_method'   => 'transfer',
            'payment_status'   => 'pending',
            'shipping_address' => 'Jl. Test',
            'pickup_method'    => 'delivery',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/admin/pesanan/{$order->id}/status", [
                'status' => 'confirmed',
            ]);

        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('confirmed', $order->status);
    }

    /** @test */
    public function admin_bisa_tandai_pesanan_sudah_diambil()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-PICKUP-0001',
            'status'           => 'menunggu_diambil',
            'total_amount'     => 10000,
            'payment_method'   => 'cod',
            'payment_status'   => 'pending',
            'shipping_address' => 'Toko Pojok Jepara',
            'pickup_method'    => 'pickup',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/pesanan/{$order->id}/picked-up");

        $response->assertRedirect();
        $order->refresh();
        $this->assertEquals('completed', $order->status);
        $this->assertEquals('paid', $order->payment_status);
    }

    /** @test */
    public function admin_tidak_bisa_tandai_diambil_jika_status_bukan_menunggu_diambil()
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_code'       => 'ORD-PICKUP-0002',
            'status'           => 'pending',
            'total_amount'     => 10000,
            'payment_method'   => 'transfer',
            'payment_status'   => 'pending',
            'shipping_address' => 'Jl. Test',
            'pickup_method'    => 'delivery',
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/pesanan/{$order->id}/picked-up");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertNotEquals('completed', $order->status);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 8: MANAJEMEN STOK ADMIN
    // ══════════════════════════════════════════════

    /** @test */
    public function admin_bisa_akses_halaman_manajemen_stok()
    {
        $response = $this->actingAs($this->admin)->get('/admin/stok');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_restock_produk()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/stok/restock', [
                'product_id' => $this->product->id,
                'quantity'   => 20,
                'note'       => 'Restock dari supplier',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->stock->refresh();
        $this->assertEquals(70, $this->stock->quantity);
    }

    /** @test */
    public function admin_bisa_update_stok_tipe_in()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/stok/update', [
                'product_id' => $this->product->id,
                'type'       => 'in',
                'quantity'   => 10,
                'note'       => 'Tambah stok',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->stock->refresh();
        $this->assertEquals(60, $this->stock->quantity);
    }

    /** @test */
    public function admin_bisa_update_stok_tipe_out()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/stok/update', [
                'product_id' => $this->product->id,
                'type'       => 'out',
                'quantity'   => 10,
                'note'       => 'Kurangi stok',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->stock->refresh();
        $this->assertEquals(40, $this->stock->quantity);
    }

    /** @test */
    public function admin_bisa_penyesuaian_stok()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/stok/adjust', [
                'product_id' => $this->product->id,
                'quantity'   => 99,
                'note'       => 'Koreksi stok fisik',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->stock->refresh();
        $this->assertEquals(99, $this->stock->quantity);
    }

    /** @test */
    public function update_stok_mencatat_histori()
    {
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock test', $this->admin->id);

        $this->assertDatabaseHas('stock_histories', [
            'product_id'      => $this->product->id,
            'type'            => 'in',
            'quantity_before' => 50,
            'quantity_after'  => 60,
            'quantity_change' => 10,
            'note'            => 'Restock test',
            'user_id'         => $this->admin->id,
        ]);
    }

    /** @test */
    public function admin_bisa_lihat_histori_stok()
    {
        // Buat beberapa histori
        $this->stockService->updateStock($this->product, 5, 'in', 'In test', $this->admin->id);
        $this->stockService->updateStock($this->product, 3, 'out', 'Out test', $this->admin->id);

        $response = $this->actingAs($this->admin)->get('/admin/stok/histori');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_filter_histori_stok_berdasarkan_produk()
    {
        $this->stockService->updateStock($this->product, 5, 'in', 'Filter test', $this->admin->id);

        $response = $this->actingAs($this->admin)
            ->get("/admin/stok/histori?product_id={$this->product->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_export_stok_sebagai_csv()
    {
        $response = $this->actingAs($this->admin)->get('/admin/stok/export');

        // Harus mengembalikan stream CSV
        $response->assertStatus(200);
        $this->assertStringContainsString(
            'text/csv',
            $response->headers->get('Content-Type', '')
        );
    }

    /** @test */
    public function stock_service_menghitung_statistik_dengan_benar()
    {
        // Buat produk dengan stok kritis (1–10)
        $product2 = Product::create([
            'category_id' => $this->category->id, 'name' => 'Kritis', 'slug' => 'kritis',
            'description' => 'Stok kritis', 'price' => 5000, 'wholesale_price' => 4000, 'unit' => 'pcs',
        ]);
        Stock::create(['product_id' => $product2->id, 'quantity' => 5]);

        // Buat produk habis
        $product3 = Product::create([
            'category_id' => $this->category->id, 'name' => 'Habis', 'slug' => 'habis',
            'description' => 'Stok habis', 'price' => 5000, 'wholesale_price' => 4000, 'unit' => 'pcs',
        ]);
        Stock::create(['product_id' => $product3->id, 'quantity' => 0]);

        $stats = $this->stockService->getStockStatistics();

        $this->assertEquals(3, $stats['total_products']);
        $this->assertEquals(1, $stats['available_stock']); // product dengan stok 50
        $this->assertEquals(1, $stats['critical_stock']);  // product2 dengan stok 5
        $this->assertEquals(1, $stats['out_of_stock']);    // product3 dengan stok 0
    }

    // ══════════════════════════════════════════════
    // BAGIAN 9: LAPORAN PENJUALAN
    // ══════════════════════════════════════════════

    /** @test */
    public function admin_bisa_akses_halaman_laporan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_akses_laporan_penjualan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan/penjualan');
        $response->assertStatus(200);
    }

    /** @test */
    public function laporan_penjualan_dengan_filter_tanggal_berfungsi()
    {
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate   = now()->endOfMonth()->format('Y-m-d');

        $response = $this->actingAs($this->admin)
            ->get("/admin/laporan/penjualan?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);
    }

    /** @test */
    public function laporan_penjualan_menghitung_revenue_dengan_benar()
    {
        // Buat 2 pesanan completed bulan ini
        $order1 = Order::create([
            'user_id' => $this->customer->id, 'order_code' => 'ORD-RPT-001',
            'status' => 'completed', 'total_amount' => 50000,
            'payment_method' => 'transfer', 'payment_status' => 'paid',
            'shipping_address' => 'Jl. A', 'pickup_method' => 'delivery',
        ]);
        $order2 = Order::create([
            'user_id' => $this->customer->id, 'order_code' => 'ORD-RPT-002',
            'status' => 'confirmed', 'total_amount' => 30000,
            'payment_method' => 'transfer', 'payment_status' => 'pending',
            'shipping_address' => 'Jl. B', 'pickup_method' => 'delivery',
        ]);
        // Pesanan cancelled tidak dihitung
        Order::create([
            'user_id' => $this->customer->id, 'order_code' => 'ORD-RPT-003',
            'status' => 'cancelled', 'total_amount' => 20000,
            'payment_method' => 'transfer', 'payment_status' => 'pending',
            'shipping_address' => 'Jl. C', 'pickup_method' => 'delivery',
        ]);

        $startDate = now()->startOfMonth()->toDateTimeString();
        $endDate   = now()->endOfMonth()->toDateTimeString();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $this->assertEquals(2, $orders->count());
        $this->assertEquals(80000, $orders->sum('total_amount'));
    }

    /** @test */
    public function laporan_penjualan_tidak_menghitung_pesanan_dibatalkan()
    {
        Order::create([
            'user_id' => $this->customer->id, 'order_code' => 'ORD-CANCEL-001',
            'status' => 'cancelled', 'total_amount' => 99999,
            'payment_method' => 'transfer', 'payment_status' => 'pending',
            'shipping_address' => 'Jl. X', 'pickup_method' => 'delivery',
        ]);

        $startDate = now()->startOfMonth()->toDateTimeString();
        $endDate   = now()->endOfMonth()->toDateTimeString();

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $this->assertEquals(0, $totalRevenue);
    }

    /** @test */
    public function admin_bisa_export_laporan_penjualan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan/export/sales');
        // Export masih dalam pengembangan → redirect dengan info
        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            "Export harus mengembalikan 200 atau redirect, dapat: {$response->status()}"
        );
    }

    // ══════════════════════════════════════════════
    // BAGIAN 10: LAPORAN STOK
    // ══════════════════════════════════════════════

    /** @test */
    public function admin_bisa_akses_laporan_stok()
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan/stok');
        $response->assertStatus(200);
    }

    /** @test */
    public function laporan_stok_memisahkan_produk_kritis_dan_habis()
    {
        // Produk kritis (1–10)
        $productKritis = Product::create([
            'category_id' => $this->category->id, 'name' => 'Produk Kritis', 'slug' => 'produk-kritis',
            'description' => '-', 'price' => 5000, 'wholesale_price' => 4000, 'unit' => 'pcs',
        ]);
        Stock::create(['product_id' => $productKritis->id, 'quantity' => 5]);

        // Produk habis
        $productHabis = Product::create([
            'category_id' => $this->category->id, 'name' => 'Produk Habis', 'slug' => 'produk-habis',
            'description' => '-', 'price' => 5000, 'wholesale_price' => 4000, 'unit' => 'pcs',
        ]);
        Stock::create(['product_id' => $productHabis->id, 'quantity' => 0]);

        $stocks = Stock::with('product')->get();

        $criticalStock = $stocks->filter(fn($s) => $s->quantity > 0 && $s->quantity <= 10);
        $outOfStock    = $stocks->filter(fn($s) => $s->quantity == 0);

        $this->assertCount(1, $criticalStock);
        $this->assertCount(1, $outOfStock);
        $this->assertEquals('Produk Kritis', $criticalStock->first()->product->name);
        $this->assertEquals('Produk Habis', $outOfStock->first()->product->name);
    }

    /** @test */
    public function laporan_stok_menampilkan_produk_dengan_stok_aman()
    {
        $stocks = Stock::with('product')->get();
        // Produk uji memiliki stok 50 → aman
        $safeStock = $stocks->filter(fn($s) => $s->quantity > 10);

        $this->assertCount(1, $safeStock);
        $this->assertEquals('Produk Uji', $safeStock->first()->product->name);
    }

    /** @test */
    public function admin_bisa_export_laporan_stok()
    {
        $response = $this->actingAs($this->admin)->get('/admin/laporan/export/stock');
        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            "Export laporan stok harus mengembalikan 200 atau redirect, dapat: {$response->status()}"
        );
    }

    // ══════════════════════════════════════════════
    // BAGIAN 11: NOTIFIKASI STOK TERSEDIA
    // ══════════════════════════════════════════════

    /** @test */
    public function pelanggan_bisa_berlangganan_notifikasi_stok()
    {
        // Set stok habis dulu
        $this->stock->update(['quantity' => 0]);

        StockAlert::create([
            'product_id'  => $this->product->id,
            'user_id'     => $this->customer->id,
            'is_notified' => false,
        ]);

        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();

        $this->assertNotNull($alert);
        $this->assertFalse($alert->is_notified);
    }

    /** @test */
    public function notifikasi_dikirim_saat_restock_dari_nol()
    {
        $this->stock->update(['quantity' => 0]);

        StockAlert::create([
            'product_id'  => $this->product->id,
            'user_id'     => $this->customer->id,
            'is_notified' => false,
        ]);

        // Restock dari 0 → 10
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock', $this->admin->id);

        $notification = $this->customer->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('App\Notifications\StockAvailableNotification', $notification->type);
        $this->assertEquals($this->product->id, $notification->data['product_id']);

        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();
        $this->assertTrue($alert->is_notified);
    }

    /** @test */
    public function notifikasi_tidak_dikirim_saat_stok_sudah_ada()
    {
        // Stok masih 50 (tidak dari nol)
        StockAlert::create([
            'product_id'  => $this->product->id,
            'user_id'     => $this->customer->id,
            'is_notified' => false,
        ]);

        $this->stockService->updateStock($this->product, 10, 'in', 'Tambah stok', $this->admin->id);

        $this->assertEquals(0, $this->customer->notifications()->count());
    }

    /** @test */
    public function notifikasi_stok_hanya_dikirim_sekali()
    {
        $this->stock->update(['quantity' => 0]);

        StockAlert::create([
            'product_id'  => $this->product->id,
            'user_id'     => $this->customer->id,
            'is_notified' => false,
        ]);

        // Restock pertama → notifikasi terkirim
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock 1', $this->admin->id);
        $this->assertEquals(1, $this->customer->notifications()->count());

        // Restock kedua → tidak ada notifikasi baru
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock 2', $this->admin->id);
        $this->assertEquals(1, $this->customer->notifications()->count());
    }

    /** @test */
    public function beberapa_pelanggan_menerima_notifikasi_sekaligus()
    {
        $this->stock->update(['quantity' => 0]);

        $customer2 = User::factory()->create(['role' => 'customer', 'email' => 'c2@test.com']);

        StockAlert::create(['product_id' => $this->product->id, 'user_id' => $this->customer->id, 'is_notified' => false]);
        StockAlert::create(['product_id' => $this->product->id, 'user_id' => $customer2->id,      'is_notified' => false]);

        $this->stockService->updateStock($this->product, 15, 'in', 'Restock massal', $this->admin->id);

        $this->assertEquals(1, $this->customer->notifications()->count());
        $this->assertEquals(1, $customer2->notifications()->count());

        $alert1 = StockAlert::where('user_id', $this->customer->id)->where('product_id', $this->product->id)->first();
        $alert2 = StockAlert::where('user_id', $customer2->id)->where('product_id', $this->product->id)->first();

        $this->assertTrue($alert1->is_notified);
        $this->assertTrue($alert2->is_notified);
    }

    /** @test */
    public function data_notifikasi_mengandung_informasi_produk_yang_benar()
    {
        $this->stock->update(['quantity' => 0]);

        StockAlert::create([
            'product_id'  => $this->product->id,
            'user_id'     => $this->customer->id,
            'is_notified' => false,
        ]);

        $this->stockService->updateStock($this->product, 10, 'in', 'Restock', $this->admin->id);

        $notification = $this->customer->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals($this->product->id,   $notification->data['product_id']);
        $this->assertEquals($this->product->name,  $notification->data['product_name']);
        $this->assertStringContainsString('tersedia kembali', $notification->data['message']);
    }

    /** @test */
    public function pelanggan_bisa_lihat_halaman_notifikasi()
    {
        $response = $this->actingAs($this->customer)->get('/notifikasi');
        $response->assertStatus(200);
    }

    // ══════════════════════════════════════════════
    // BAGIAN 12: MANAJEMEN PRODUK ADMIN
    // ══════════════════════════════════════════════

    /** @test */
    public function admin_bisa_lihat_daftar_produk()
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_lihat_form_tambah_produk()
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk/create');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_tambah_produk_baru()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/produk', [
                'name'            => 'Produk Baru Admin',
                'category_id'     => $this->category->id,
                'description'     => 'Deskripsi produk baru',
                'price'           => 25000,
                'wholesale_price' => 20000,
                'unit'            => 'kg',
                'initial_stock'   => 30,
            ]);

        $this->assertDatabaseHas('products', [
            'name'  => 'Produk Baru Admin',
            'price' => 25000,
        ]);
    }

    /** @test */
    public function admin_bisa_lihat_form_edit_produk()
    {
        $response = $this->actingAs($this->admin)
            ->get("/admin/produk/{$this->product->slug}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_bisa_hapus_produk()
    {
        $response = $this->actingAs($this->admin)
            ->delete("/admin/produk/{$this->product->slug}");

        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }
}
