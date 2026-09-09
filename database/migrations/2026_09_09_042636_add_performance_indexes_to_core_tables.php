<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('community_auctions', function (Blueprint $table) {
            $table->index('status');
            $table->index('end_time');
        });

        Schema::table('community_products', function (Blueprint $table) {
            $table->index('status');
            $table->index('price');
        });

        Schema::table('auction_bids', function (Blueprint $table) {
            $table->index(['auction_id', 'bid_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            $table->dropIndex(['auction_id', 'bid_amount']);
        });

        Schema::table('community_products', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['price']);
        });

        Schema::table('community_auctions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['end_time']);
        });
    }
};
