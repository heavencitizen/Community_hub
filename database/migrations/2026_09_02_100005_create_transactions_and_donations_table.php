<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Admin or Ketua
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->decimal('target_amount', 14, 2)->default(0);
            $table->decimal('collected_amount', 14, 2)->default(0);
            $table->string('status')->default('active'); // active, closed
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Nullable for guest donor
            $table->string('type'); // product, auction, ticket, donation
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of product, auction, ticket/event, donation
            $table->decimal('amount', 14, 2); // Base price / bid / ticket / donation amount
            $table->decimal('platform_fee_percent', 5, 2)->default(0); // 0%, 1%, 2%, 5%
            $table->decimal('platform_fee_amount', 14, 2)->default(0); // platform fee amount
            $table->decimal('total_amount', 14, 2); // Total paid (amount + fee, or base - fee for seller)
            $table->string('payment_method'); // qris, bca_va, mandiri_va, bni_va, bri_va, gopay, ovo, dana, credit_card, bank_transfer
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->string('payment_code')->nullable(); // VA number, QR string, etc.
            $table->json('payment_details')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('donations');
    }
};
