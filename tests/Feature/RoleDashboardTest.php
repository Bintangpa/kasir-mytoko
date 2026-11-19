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

        // Mock Duitku Sandbox API untuk QRIS, Virtual Account & Cek Status
        Http::fake([
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => function ($request) {
                $body = $request->data();
                $method = $body['paymentMethod'] ?? '';
                if ($method === 'SP') {
                    return Http::response([
                        'merchantCode' => 'DS38179',
                        'reference' => 'DS3817926QRISTRX',
                        'paymentUrl' => 'https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=QRIS',
                        'qrString' => '00020101021226610016ID.CO.SHOPEE.WWW01189360091800202688450208202688450303UKE520479885303360540810000.005802ID5906Duitku6015KAB. BANGKA BAR610533315622205181341062826192640096304CF38',
                        'amount' => '10000',
                        'statusCode' => '00',
                        'statusMessage' => 'SUCCESS',
                    ], 200);
                }

                return Http::response([
                    'merchantCode' => 'DS38179',
                    'reference' => 'DS3817926MOCKTRX',
                    'paymentUrl' => 'https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=MOCK',
                    'vaNumber' => '7007014002477346',
                    'amount' => '10000',
                    'statusCode' => '00',
                    'statusMessage' => 'SUCCESS',
                ], 200);
            },
            'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus' => Http::response([
                'merchantOrderId' => '*',
                'reference' => 'DS3817926MOCKTRX',
                'amount' => '10000',
                'statusCode' => '00',
                'statusMessage' => 'SUCCESS',
            ], 200),
        ]);

        // Dapat melakukan pembayaran via QRIS Duitku Sandbox
        $response = $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 2],
            ],
            'payment_method' => 'qris',
            'notes' => 'Pesanan pengguna test QRIS',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
        ]);
        $this->assertNotEmpty($response->json('qr_string'));
        $this->assertNotEmpty($response->json('qr_image_url'));

        $trxId = $response->json('transaction_id');

        // Cek status QRIS langsung ke Duitku Sandbox -> berubah menjadi Lunas (paid)
        $this->actingAs($user)->postJson("/user/transactions/{$trxId}/check-status")
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_status' => 'paid',
                'is_paid' => true,
            ]);

        // Dapat melakukan pembayaran via Virtual Account (VA) Duitku Sandbox
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
            'va_number' => '7007014002477346',
            'change_amount' => 0,
        ]);

        $vaTrxId = $vaResponse->json('transaction_id');

        // Cek status VA langsung ke Duitku Sandbox -> berubah menjadi Lunas (paid)
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

    public function test_duitku_webhook_verifies_signature_and_restores_stock_on_expire(): void
    {
        $user = User::where('role', 'user')->first();
        $product = Product::where('stock', '>', 5)->first();
        $initialStock = $product->stock;

        Http::fake([
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
                'merchantCode' => 'DS38179',
                'reference' => 'DS3817926EXPTRX',
                'paymentUrl' => 'https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=EXP',
                'vaNumber' => '9884547011088614',
                'amount' => '10000',
                'statusCode' => '00',
                'statusMessage' => 'SUCCESS',
            ], 200),
        ]);

        $res = $this->actingAs($user)->postJson('/user/checkout', [
            'items' => [['id' => $product->id, 'qty' => 2]],
            'payment_method' => 'va',
            'va_bank' => 'bni',
        ])->assertStatus(200);

        $orderId = $res->json('invoice_code');
        $amount = (string) (int) round((float) $res->json('total_price'));
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);

        // Signature palsu harus ditolak 403
        $this->postJson('/webhooks/duitku', [
            'merchantCode' => 'DS38179',
            'amount' => $amount,
            'merchantOrderId' => $orderId,
            'resultCode' => '02',
            'signature' => 'invalid-signature',
        ])->assertStatus(403);

        // Signature MD5 valid dari Duitku dengan resultCode 02 (failed/expired) -> status expired & stok kembali utuh
        $merchantCode = (string) config('services.duitku.merchant_code', 'DS38179');
        $apiKey = (string) config('services.duitku.api_key', '3290a37b1465e4425812db7d33430b57');
        $validSignature = md5($merchantCode . $amount . $orderId . $apiKey);

        $this->postJson('/webhooks/duitku', [
            'merchantCode' => $merchantCode,
            'amount' => $amount,
            'merchantOrderId' => $orderId,
            'resultCode' => '02',
            'reference' => 'DS3817926EXPTRX',
            'signature' => $validSignature,
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
