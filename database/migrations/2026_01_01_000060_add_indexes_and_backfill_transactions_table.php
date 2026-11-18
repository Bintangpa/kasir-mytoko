<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill paid_at untuk transaksi lama yang berstatus paid namun paid_at masih NULL
        DB::table('transactions')
            ->where('payment_status', 'paid')
            ->whereNull('paid_at')
            ->update(['paid_at' => DB::raw('created_at')]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('payment_status', 'transactions_payment_status_index');
            $table->index('payment_method', 'transactions_payment_method_index');
            $table->index('gateway_transaction_id', 'transactions_gateway_transaction_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_payment_status_index');
            $table->dropIndex('transactions_payment_method_index');
            $table->dropIndex('transactions_gateway_transaction_id_index');
        });
    }
};
