<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $kasir;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->kasir = User::factory()->create(['role' => 'kasir']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_view_create_product_form_with_database_photo_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertSee('multipart/form-data', false);
        $response->assertSee('Foto Produk');
        $response->assertSee('2 MB');
        $response->assertSee('Disimpan ke Database');
    }

    public function test_admin_can_create_product_with_image_saved_directly_in_database(): void
    {
        $category = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);

        // 500 KB test image (simulated binary without GD dependency)
        $file = UploadedFile::fake()->create('kopi-susu.jpg', 500, 'image/jpeg');

        $payload = [
            'category_id' => $category->id,
            'code' => 'PRD-IMG-01',
            'name' => 'Kopi Susu Spesial',
            'cost_price' => 8000,
            'price' => 15000,
            'stock' => 20,
            'unit' => 'cup',
            'description' => 'Kopi susu gula aren asli',
            'image' => $file,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('code', 'PRD-IMG-01')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image_data);
        $this->assertEquals('image/jpeg', $product->image_mime);
        $this->assertGreaterThan(0, $product->image_size);
        $this->assertTrue($product->has_image);
        $this->assertNotNull($product->image_url);
    }

    public function test_admin_cannot_upload_image_exceeding_2mb(): void
    {
        $category = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);

        // 2500 KB (exceeds 2048 KB limit)
        $oversizedFile = UploadedFile::fake()->create('huge-photo.jpg', 2500, 'image/jpeg');

        $payload = [
            'category_id' => $category->id,
            'code' => 'PRD-HUGE',
            'name' => 'Burger Jumbo',
            'cost_price' => 12000,
            'price' => 25000,
            'stock' => 10,
            'unit' => 'porsi',
            'image' => $oversizedFile,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('products', ['code' => 'PRD-HUGE']);
    }

    public function test_admin_can_update_product_and_replace_image(): void
    {
        $product = Product::create([
            'code' => 'PRD-EDIT-01',
            'name' => 'Es Teh Manis',
            'cost_price' => 2000,
            'price' => 5000,
            'stock' => 50,
            'unit' => 'gelas',
            'image_data' => base64_encode('old-image-binary-data'),
            'image_mime' => 'image/jpeg',
            'image_size' => 100,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->create('fresh-teh.png', 300, 'image/png');

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'code' => 'PRD-EDIT-01',
            'name' => 'Es Teh Manis Jumbo',
            'cost_price' => 2500,
            'price' => 6000,
            'stock' => 45,
            'unit' => 'gelas',
            'image' => $newImage,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Es Teh Manis Jumbo', $product->name);
        $this->assertEquals('image/png', $product->image_mime);
        $this->assertNotEquals(base64_encode('old-image-binary-data'), $product->image_data);
    }

    public function test_admin_can_remove_product_image(): void
    {
        $product = Product::create([
            'code' => 'PRD-DEL-IMG',
            'name' => 'Roti Bakar',
            'cost_price' => 7000,
            'price' => 12000,
            'stock' => 15,
            'unit' => 'porsi',
            'image_data' => base64_encode('sample-image-data'),
            'image_mime' => 'image/jpeg',
            'image_size' => 200,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'code' => 'PRD-DEL-IMG',
            'name' => 'Roti Bakar',
            'cost_price' => 7000,
            'price' => 12000,
            'stock' => 15,
            'unit' => 'porsi',
            'remove_image' => '1',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertNull($product->image_data);
        $this->assertNull($product->image_mime);
        $this->assertNull($product->image_size);
        $this->assertFalse($product->has_image);
    }

    public function test_authenticated_user_can_stream_database_image(): void
    {
        $binaryContent = 'fake-binary-image-data-string';
        $product = Product::create([
            'code' => 'PRD-STREAM',
            'name' => 'Jus Alpukat',
            'cost_price' => 8000,
            'price' => 15000,
            'stock' => 10,
            'unit' => 'gelas',
            'image_data' => base64_encode($binaryContent),
            'image_mime' => 'image/jpeg',
            'image_size' => strlen($binaryContent),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('products.image', $product->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
        $this->assertEquals($binaryContent, $response->getContent());
    }

    public function test_streaming_image_route_returns_404_when_no_image(): void
    {
        $product = Product::create([
            'code' => 'PRD-NO-IMG',
            'name' => 'Kerupuk Putih',
            'cost_price' => 500,
            'price' => 1000,
            'stock' => 100,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('products.image', $product->id));

        $response->assertStatus(404);
    }
}
