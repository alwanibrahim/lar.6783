<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('invite_link_or_email');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'clicked', 'accepted'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_invites');
    }
};
