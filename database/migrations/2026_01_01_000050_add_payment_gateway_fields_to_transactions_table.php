<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_status')->default('paid')->after('payment_method'); // paid, pending, expired, failed
            $table->string('va_bank')->nullable()->after('payment_status'); // bca, bni, bri, mandiri, permata
            $table->string('va_number')->nullable()->after('va_bank'); // VA number / Mandiri bill_key
            $table->string('va_biller_code')->nullable()->after('va_number'); // Khusus Mandiri echannel biller_code
            $table->string('gateway_transaction_id')->nullable()->after('va_biller_code');
            $table->timestamp('payment_expired_at')->nullable()->after('gateway_transaction_id');
            $table->timestamp('paid_at')->nullable()->after('payment_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'va_bank',
                'va_number',
                'va_biller_code',
                'gateway_transaction_id',
                'payment_expired_at',
                'paid_at',
            ]);
        });
    }
};
