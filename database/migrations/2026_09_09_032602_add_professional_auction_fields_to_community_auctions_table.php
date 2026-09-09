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
            $table->string('auction_code', 32)->nullable()->unique()->after('id');
            $table->string('bidding_type', 16)->default('open')->after('status');
            $table->boolean('anti_sniping')->default(true)->after('bidding_type');
            $table->timestamp('payment_deadline')->nullable()->after('end_time');
            $table->foreignId('runner_up_id')->nullable()->after('winner_id')->constrained('users')->nullOnDelete();
            $table->decimal('runner_up_bid', 12, 2)->nullable()->after('runner_up_id');
            $table->timestamp('wanprestasi_at')->nullable()->after('payment_deadline');
            $table->text('notes')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_auctions', function (Blueprint $table) {
            $table->dropForeign(['runner_up_id']);
            $table->dropColumn([
                'auction_code',
                'bidding_type',
                'anti_sniping',
                'payment_deadline',
                'runner_up_id',
                'runner_up_bid',
                'wanprestasi_at',
                'notes',
            ]);
        });
    }
};
