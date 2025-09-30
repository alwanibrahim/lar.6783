<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deposits', function (Blueprint $table) {
            // tambah kolom reference unik
            $table->string('reference')->unique()->after('status');

            // tambah kolom payment_method
            $table->string('payment_method')->after('reference');

            // tambah kolom payment_url (opsional untuk simpan link Tripay)
            $table->string('payment_url')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['reference', 'payment_method', 'payment_url']);
        });
    }
};
