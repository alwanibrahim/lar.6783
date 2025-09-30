<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['referral_code', 'referred_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
