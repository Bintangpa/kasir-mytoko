<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (Admin & Kasir)
        $admin = User::firstOrCreate(
            ['email' => 'admin@mytoko.com'],
            [
                'name' => 'Administrator Toko',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $kasir1 = User::firstOrCreate(
            ['email' => 'kasir@mytoko.com'],
            [
                'name' => 'Kasir 1 - Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '081298765432',
                'is_active' => true,
            ]
        );

        $kasir2 = User::firstOrCreate(
            ['email' => 'kasir2@mytoko.com'],
            [
                'name' => 'Kasir 2 - Siti Rahma',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '081311223344',
                'is_active' => true,
            ]
        );

        // 2. Kategori Produk
        $categoriesData = [
            ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan siap saji dan aneka minuman kemasan'],
            ['name' => 'Sembako & Pokok', 'description' => 'Beras, minyak goreng, gula, dan kebutuhan dapur'],
            ['name' => 'Snack & Cemilan', 'description' => 'Biskuit, keripik, cokelat dan permen'],
            ['name' => 'Perawatan & Kebersihan', 'description' => 'Sabun, sampo, deterjen, dan pembersih rumah tangga'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'description' => $cat['description']]
            );
        }

        // 3. Daftar Produk
        $productsData = [
            // Makanan & Minuman
            [
                'category' => 'Makanan & Minuman',
                'code' => 'MNM-001',
                'name' => 'Aqua Air Mineral 600ml',
                'cost_price' => 2800,
                'price' => 3500,
                'stock' => 48,
                'unit' => 'botol',
                'description' => 'Air mineral pegunungan botol 600ml',
            ],
            [
                'category' => 'Makanan & Minuman',
                'code' => 'MNM-002',
                'name' => 'Teh Botol Sosro Kotak 250ml',
                'cost_price' => 3200,
                'price' => 4500,
                'stock' => 30,
                'unit' => 'kotak',
                'description' => 'Teh melati manis kemasan kotak praktis',
            ],
            [
                'category' => 'Makanan & Minuman',
                'code' => 'MNM-003',
                'name' => 'Indomie Goreng Spesial',
                'cost_price' => 2700,
                'price' => 3500,
                'stock' => 85,
                'unit' => 'bungkus',
                'description' => 'Mi instan goreng rasa original',
            ],
            [
                'category' => 'Makanan & Minuman',
                'code' => 'MNM-004',
                'name' => 'Kopi Kapal Api Spesial Mix 10s',
                'cost_price' => 12500,
                'price' => 15000,
                'stock' => 18,
                'unit' => 'pack',
                'description' => 'Kopi bubuk instan isi 10 sachet',
            ],

            // Sembako
            [
                'category' => 'Sembako & Pokok',
                'code' => 'SMB-001',
                'name' => 'Minyak Goreng Bimoli 2 Liter',
                'cost_price' => 32000,
                'price' => 37500,
                'stock' => 12,
                'unit' => 'pouch',
                'description' => 'Minyak kelapa sawit murni kemasan 2 liter',
            ],
            [
                'category' => 'Sembako & Pokok',
                'code' => 'SMB-002',
                'name' => 'Beras Pandan Wangi Premium 5kg',
                'cost_price' => 68000,
                'price' => 76000,
                'stock' => 10,
                'unit' => 'karung',
                'description' => 'Beras pulen harum kualitas istimewa',
            ],
            [
                'category' => 'Sembako & Pokok',
                'code' => 'SMB-003',
                'name' => 'Gula Pasir Gulaku Kuning 1kg',
                'cost_price' => 15500,
                'price' => 18000,
                'stock' => 4, // Stok menipis untuk uji alert
                'unit' => 'bungkus',
                'description' => 'Gula tebu murni kemasan 1 kg',
            ],
            [
                'category' => 'Sembako & Pokok',
                'code' => 'SMB-004',
                'name' => 'Telur Ayam Negeri 1kg',
                'cost_price' => 26000,
                'price' => 29000,
                'stock' => 2, // Stok menipis
                'unit' => 'kg',
                'description' => 'Telur ayam negeri segar pilihan',
            ],

            // Snack & Cemilan
            [
                'category' => 'Snack & Cemilan',
                'code' => 'SNK-001',
                'name' => 'Chitato Sapi Panggang 68g',
                'cost_price' => 9500,
                'price' => 12000,
                'stock' => 24,
                'unit' => 'bungkus',
                'description' => 'Keripik kentang bergelombang rasa daging sapi',
            ],
            [
                'category' => 'Snack & Cemilan',
                'code' => 'SNK-002',
                'name' => 'Oreo Vanila Sandwich 133g',
                'cost_price' => 8500,
                'price' => 10500,
                'stock' => 15,
                'unit' => 'bungkus',
                'description' => 'Biskuit cokelat dengan krim vanila lezat',
            ],
            [
                'category' => 'Snack & Cemilan',
                'code' => 'SNK-003',
                'name' => 'SilverQueen Cashew 58g',
                'cost_price' => 14000,
                'price' => 17500,
                'stock' => 0, // Habis untuk uji badge Out of Stock
                'unit' => 'pcs',
                'description' => 'Cokelat susu dengan kacang mete renyah',
            ],

            // Perawatan & Kebersihan
            [
                'category' => 'Perawatan & Kebersihan',
                'code' => 'SBN-001',
                'name' => 'Lifebuoy Sabun Cair Total 10 450ml',
                'cost_price' => 21000,
                'price' => 25500,
                'stock' => 16,
                'unit' => 'pouch',
                'description' => 'Sabun mandi cair antibakteri refill 450ml',
            ],
            [
                'category' => 'Perawatan & Kebersihan',
                'code' => 'SBN-002',
                'name' => 'Sunlight Jeruk Nipis 750ml',
                'cost_price' => 14500,
                'price' => 17500,
                'stock' => 20,
                'unit' => 'pouch',
                'description' => 'Cairan pencuci piring konsentrat ekstrak jeruk nipis',
            ],
            [
                'category' => 'Perawatan & Kebersihan',
                'code' => 'SBN-003',
                'name' => 'Pepsodent Pencegah Gigi Berlubang 190g',
                'cost_price' => 13000,
                'price' => 16000,
                'stock' => 25,
                'unit' => 'tube',
                'description' => 'Pasta gigi dengan kalsium dan fluoride',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $p) {
            $cat = $categories[$p['category']];
            $createdProducts[$p['code']] = Product::firstOrCreate(
                ['code' => $p['code']],
                [
                    'category_id' => $cat->id,
                    'name' => $p['name'],
                    'cost_price' => $p['cost_price'],
                    'price' => $p['price'],
                    'stock' => $p['stock'],
                    'unit' => $p['unit'],
                    'description' => $p['description'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Sample Transaksi Sebelumnya (untuk demo grafik dan laporan Admin & Kasir)
        if (Transaction::count() === 0) {
            $sampleTransactions = [
                [
                    'date' => Carbon::today()->subDays(2)->setHour(10)->setMinute(15),
                    'user' => $kasir1,
                    'customer' => 'Ibu Ratna',
                    'method' => 'cash',
                    'pay' => 100000,
                    'items' => [
                        ['product' => 'SMB-002', 'qty' => 1], // Beras 76.000
                        ['product' => 'MNM-003', 'qty' => 4], // Indomie 4x3.500 = 14.000
                    ],
                ],
                [
                    'date' => Carbon::today()->subDays(1)->setHour(14)->setMinute(30),
                    'user' => $kasir1,
                    'customer' => 'Pak Joko',
                    'method' => 'qris',
                    'pay' => 49500,
                    'items' => [
                        ['product' => 'SMB-001', 'qty' => 1], // Bimoli 37.500
                        ['product' => 'SNK-001', 'qty' => 1], // Chitato 12.000
                    ],
                ],
                [
                    'date' => Carbon::today()->subDays(1)->setHour(16)->setMinute(45),
                    'user' => $kasir2,
                    'customer' => 'Pelanggan Umum',
                    'method' => 'cash',
                    'pay' => 50000,
                    'items' => [
                        ['product' => 'SBN-001', 'qty' => 1], // Lifebuoy 25.500
                        ['product' => 'SBN-002', 'qty' => 1], // Sunlight 17.500
                    ],
                ],
                [
                    'date' => Carbon::today()->setHour(9)->setMinute(20),
                    'user' => $kasir1,
                    'customer' => 'Mas Doni',
                    'method' => 'cash',
                    'pay' => 50000,
                    'items' => [
                        ['product' => 'MNM-001', 'qty' => 2], // Aqua 2x3.500 = 7.000
                        ['product' => 'SNK-002', 'qty' => 2], // Oreo 2x10.500 = 21.000
                        ['product' => 'MNM-004', 'qty' => 1], // Kopi 15.000
                    ],
                ],
                [
                    'date' => Carbon::today()->setHour(10)->setMinute(40),
                    'user' => $kasir1,
                    'customer' => 'Mba Rina',
                    'method' => 'qris',
                    'pay' => 32000,
                    'items' => [
                        ['product' => 'SBN-003', 'qty' => 2], // Pepsodent 2x16.000 = 32.000
                    ],
                ],
            ];

            $counter = 1;
            foreach ($sampleTransactions as $st) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($st['items'] as $itemRef) {
                    $prod = $createdProducts[$itemRef['product']];
                    $qty = $itemRef['qty'];
                    $itemSubtotal = $prod->price * $qty;
                    $subtotal += $itemSubtotal;

                    $itemsData[] = [
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'cost_price' => $prod->cost_price,
                        'price' => $prod->price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'created_at' => $st['date'],
                        'updated_at' => $st['date'],
                    ];
                }

                $invoiceCode = 'TRX-' . $st['date']->format('Ymd') . '-' . str_pad($counter++, 4, '0', STR_PAD_LEFT);
                $change = max(0, $st['pay'] - $subtotal);

                $trx = Transaction::create([
                    'invoice_code' => $invoiceCode,
                    'user_id' => $st['user']->id,
                    'customer_name' => $st['customer'],
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_price' => $subtotal,
                    'pay_amount' => $st['pay'],
                    'change_amount' => $change,
                    'payment_method' => $st['method'],
                    'notes' => 'Transaksi demo seeder',
                    'created_at' => $st['date'],
                    'updated_at' => $st['date'],
                ]);

                foreach ($itemsData as $row) {
                    $row['transaction_id'] = $trx->id;
                    TransactionItem::create($row);
                }
            }
        }
    }
}
