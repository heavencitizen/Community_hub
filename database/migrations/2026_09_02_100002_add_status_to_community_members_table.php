<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->string('role')->default('member')->after('user_id'); // captain, moderator, member
            $table->string('status')->default('active')->after('role'); // active, suspended, banned
            $table->string('suspend_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'suspend_reason']);
        });
    }
};
