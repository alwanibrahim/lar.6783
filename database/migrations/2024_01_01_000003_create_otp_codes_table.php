<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->enum('type', ['email', 'sms']);
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_codes');
    }
};
