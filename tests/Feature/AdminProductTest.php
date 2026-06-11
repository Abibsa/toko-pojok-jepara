<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
        ]);
    }

    public function test_admin_can_view_products_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.produk.index'));
        $response->assertStatus(200);
    }

    public function test_customer_cannot_view_products_list()
    {
        $response = $this->actingAs($this->customer)->get(route('admin.produk.index'));
        $response->assertStatus(403); // Or whatever logic you have (middleware 'admin' might abort 403 or redirect)
    }

    public function test_admin_can_create_product()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.produk.store'), [
            'category_id' => $this->category->id,
            'name' => 'Produk Test Baru',
            'description' => 'Deskripsi produk test baru',
            'price' => 50000,
            'wholesale_price' => 45000,
            'unit' => 'pcs',
        ]);

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Produk Test Baru',
            'price' => 50000,
        ]);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
            'price' => 50000,
            'wholesale_price' => 45000,
            'description' => 'Test',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.produk.update', $product->slug), [
            'category_id' => $this->category->id,
            'name' => 'New Name',
            'description' => 'New Description',
            'price' => 60000,
            'wholesale_price' => 55000,
            'unit' => 'pcs',
        ]);

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'price' => 60000,
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'price' => 50000,
            'wholesale_price' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.produk.destroy', $product->slug));

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
