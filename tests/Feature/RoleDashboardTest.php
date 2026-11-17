<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response2 = $this->get('/kasir/dashboard');
        $response2->assertRedirect('/login');
    }

    public function test_kasir_cannot_access_admin_dashboard(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $this->assertNotNull($kasir);

        $response = $this->actingAs($kasir)->get('/admin/dashboard');
        $response->assertRedirect('/kasir/dashboard');
    }

    public function test_kasir_can_access_kasir_dashboard_and_pos(): void
    {
        $kasir = User::where('role', 'kasir')->first();

        $response = $this->actingAs($kasir)->get('/kasir/dashboard');
        $response->assertStatus(200);

        $posResponse = $this->actingAs($kasir)->get('/kasir/pos');
        $posResponse->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard_and_pos(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $posResponse = $this->actingAs($admin)->get('/kasir/pos');
        $posResponse->assertStatus(200);
    }

    public function test_kasir_can_process_checkout(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $product = Product::where('stock', '>', 5)->first();
        $this->assertNotNull($product);

        $initialStock = $product->stock;

        $response = $this->actingAs($kasir)->postJson('/kasir/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 2],
            ],
            'customer_name' => 'Bpk Test Pelanggan',
            'pay_amount' => $product->price * 2,
            'payment_method' => 'cash',
            'discount' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('transactions', [
            'customer_name' => 'Bpk Test Pelanggan',
            'user_id' => $kasir->id,
        ]);

        // Stock must have decreased by 2
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);
    }

    public function test_authenticated_user_can_access_receipt_settings(): void
    {
        $kasir = User::where('role', 'kasir')->first();

        $response = $this->actingAs($kasir)->get('/receipt/settings');
        $response->assertStatus(200);
        $response->assertSee('80 mm Roll (Default)');
        $response->assertSee('58 x 40 mm');
    }

    public function test_consecutive_checkouts_generate_unique_invoice_codes(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $product = Product::where('stock', '>', 10)->first();

        $response1 = $this->actingAs($kasir)->postJson('/kasir/checkout', [
            'items' => [['id' => $product->id, 'qty' => 1]],
            'customer_name' => 'Pelanggan 1',
            'pay_amount' => $product->price,
            'payment_method' => 'cash',
        ]);
        $response1->assertStatus(200);

        $response2 = $this->actingAs($kasir)->postJson('/kasir/checkout', [
            'items' => [['id' => $product->id, 'qty' => 1]],
            'customer_name' => 'Pelanggan 2',
            'pay_amount' => $product->price,
            'payment_method' => 'cash',
        ]);
        $response2->assertStatus(200);

        $code1 = $response1->json('invoice_code');
        $code2 = $response2->json('invoice_code');
        $this->assertNotEquals($code1, $code2);
    }
}
