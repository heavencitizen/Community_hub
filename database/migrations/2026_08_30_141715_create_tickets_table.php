<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code')->unique();           // kode unik tiket (UUID/random)
            $table->unsignedBigInteger('total_price');         // price + admin_fee
            $table->string('payment_receipt')->nullable();     // path file bukti transfer/QRIS
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_scanned')->default(false);     // sudah di-scan saat masuk event
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
