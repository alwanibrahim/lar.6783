<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // hapus kolom stock lama
            if (Schema::hasColumn('product_variants', 'stock')) {
                $table->dropColumn('stock');
            }

            // tambahkan kolom status stok (enum)
            $table->enum('status', ['READY', 'NOT_READY'])
                ->default('READY')
                ->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->unsignedInteger('stock')->default(0);
        });
    }
};
