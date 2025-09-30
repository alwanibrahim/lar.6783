<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('username');
            $table->string('password');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
            
            $table->index(['product_id', 'is_used']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_accounts');
    }
};
