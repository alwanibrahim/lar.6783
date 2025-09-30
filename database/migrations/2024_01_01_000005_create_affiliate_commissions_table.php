<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('deposit_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_amount', 15, 2);
            $table->timestamps();

            $table->index(['referrer_id', 'referred_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
