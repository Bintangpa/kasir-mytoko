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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'image')) {
                $table->dropColumn('image');
            }
            $table->longText('image_data')->nullable()->after('unit');
            $table->string('image_mime', 50)->nullable()->after('image_data');
            $table->unsignedInteger('image_size')->nullable()->after('image_mime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_data', 'image_mime', 'image_size']);
            $table->string('image')->nullable()->after('unit');
        });
    }
};
