<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->nullable()->constrained('product_accounts')->onDelete('set null');
            $table->foreignId('invite_id')->nullable()->constrained('product_invites')->onDelete('set null');
            $table->enum('status', ['pending', 'sent', 'completed'])->default('pending');
            $table->boolean('instructions_sent')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('distributions');
    }
};
