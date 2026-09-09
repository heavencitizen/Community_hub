<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // post_liked, post_commented, member_joined, ticket_purchased, auction_bid, donation_received, etc.
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->string('icon')->default('fa-bell');
            $table->string('icon_color')->default('text-indigo-500');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
