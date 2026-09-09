<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('location');
            $table->dateTime('event_date');
            $table->unsignedBigInteger('price')->default(0);        // dalam Rupiah (0 = gratis)
            $table->unsignedBigInteger('admin_fee')->default(3000); // komisi platform Rp3.000
            $table->unsignedInteger('quota')->default(100);
            $table->string('banner')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
