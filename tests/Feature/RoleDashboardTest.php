<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

    public function test_user_cannot_access_admin_or_kasir_pages(): void
    {
        $user = User::where('role', 'user')->first();
        $this->assertNotNull($user);

        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect('/user/products');
        $this->actingAs($user)->get('/admin/products')->assertRedirect('/user/products');
        $this->actingAs($user)->get('/kasir/dashboard')->assertRedirect('/user/products');
        $this->actingAs($user)->get('/kasir/pos')->assertRedirect('/user/products');
        $this->actingAs($user)->get('/receipt/settings')->assertRedirect('/user/products');
    }

    public function test_user_can_view_products_and_process_payment(): void
    {
        $user = User::where('role', 'user')->first();
        $product = Product::where('stock', '>', 5)->first();
        $this->assertNotNull($user);
        $this->assertNotNull($product);

        $initialStock = $product->stock;

        // Dapat melihat katalog produk, halaman keranjang & riwayat pembelian
        $this->actingAs($user)->get('/user/products')
            ->assertStatus(200)
            ->assertSee($product->name)
            ->assertSee('navbarCartBadge');

        $this->actingAs($user)->get('/user/cart')
            ->assertStatus(200)
            ->assertSee('Keranjang Belanja')
            ->assertSee('QRIS')
            ->assertSee('Virtual Account (VA)')
            ->assertDontSee('Nominal Uang');

        $this->actingAs($user)->get('/user/transactions')
            ->assertStatus(200);

        // Tidak boleh membayar dengan Tunai (cash)
        $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
            'payment_method' => 'cash',
        ])->assertStatus(422);

        // Dapat melakukan pembayaran via QRIS
        $response = $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 2],
            ],
            'payment_method' => 'qris',
            'notes' => 'Pesanan pengguna test',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Mock Midtrans Sandbox Core API untuk Virtual Account & Cek Status
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '201',
                'status_message' => 'Success, Bank Transfer transaction is created',
                'transaction_id' => 'midtrans-trx-001',
                'transaction_status' => 'pending',
                'va_numbers' => [
                    ['bank' => 'bca', 'va_number' => '235510000111222333'],
                ],
                'expiry_time' => now()->addHours(24)->format('Y-m-d H:i:s'),
            ], 200),
            'https://api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'status_code' => '200',
                'transaction_id' => 'midtrans-trx-001',
                'transaction_status' => 'settlement',
            ], 200),
        ]);

        // Dapat melakukan pembayaran via Virtual Account (VA) Midtrans Sandbox
        $vaResponse = $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
            'payment_method' => 'va',
            'va_bank' => 'bca',
        ]);

        $vaResponse->assertStatus(200);
        $vaResponse->assertJson([
            'success' => true,
            'payment_method' => 'va',
            'payment_status' => 'pending',
            'va_number' => '235510000111222333',
            'change_amount' => 0,
        ]);

        $vaTrxId = $vaResponse->json('transaction_id');

        // Cek status VA langsung ke Midtrans Sandbox -> berubah menjadi Lunas (paid)
        $this->actingAs($user)->postJson("/user/transactions/{$vaTrxId}/check-status")
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_status' => 'paid',
                'is_paid' => true,
            ]);

        $trxId = $response->json('transaction_id');
        $this->assertEquals($initialStock - 3, $product->fresh()->stock);

        // Dapat melihat struk transaksi miliknya sendiri (hanya lihat, tanpa tombol cetak)
        $this->actingAs($user)->get("/transactions/{$trxId}/receipt")
            ->assertStatus(200)
            ->assertSee('Mode Hanya Lihat')
            ->assertDontSee('Cetak Nota')
            ->assertDontSee('window.print()');

        // Tidak boleh melihat struk transaksi milik user lain (kasir)
        $otherTrx = Transaction::where('user_id', '!=', $user->id)->first();
        if ($otherTrx) {
            $this->actingAs($user)->get("/transactions/{$otherTrx->id}/receipt")
                ->assertStatus(403);
        }
    }

    public function test_midtrans_webhook_verifies_signature_and_restores_stock_on_expire(): void
    {
        $user = User::where('role', 'user')->first();
        $product = Product::where('stock', '>', 5)->first();
        $initialStock = $product->stock;

        Http::fake([
            'https://api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'midtrans-trx-exp',
                'transaction_status' => 'pending',
                'va_numbers' => [
                    ['bank' => 'bni', 'va_number' => '9881234567890'],
                ],
                'expiry_time' => now()->addHours(24)->format('Y-m-d H:i:s'),
            ], 200),
        ]);

        $res = $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [['id' => $product->id, 'qty' => 2]],
            'payment_method' => 'va',
            'va_bank' => 'bni',
        ])->assertStatus(200);

        $orderId = $res->json('invoice_code');
        $grossAmount = number_format((float) $res->json('total_price'), 2, '.', '');
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);

        // Signature palsu harus ditolak 403
        $this->postJson('/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => '202',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'expire',
            'signature_key' => 'invalid-signature',
        ])->assertStatus(403);

        // Signature SHA-512 valid dari Midtrans dengan status expire -> status expired & stok kembali utuh
        $serverKey = (string) config('services.midtrans.server_key');
        $validSignature = hash('sha512', $orderId . '202' . $grossAmount . $serverKey);

        $this->postJson('/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => '202',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'expire',
            'transaction_id' => 'midtrans-trx-exp',
            'signature_key' => $validSignature,
        ])->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_status' => 'expired',
            ]);

        $this->assertEquals($initialStock, $product->fresh()->stock);
    }

    public function test_admin_can_create_account_with_user_role(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Pelanggan Baru',
            'email' => 'pelangganbaru@mytoko.com',
            'password' => 'secret123',
            'role' => 'user',
            'phone' => '08123456789',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'pelangganbaru@mytoko.com',
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}
