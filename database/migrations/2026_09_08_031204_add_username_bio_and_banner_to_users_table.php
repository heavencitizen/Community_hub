<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('banner')->nullable()->after('bio');
        });

        // Backfill existing users with clean, unique usernames
        $presetUsernames = [
            'superadmin@hubmedia.id' => 'superadmin',
            'eo@community.id' => 'rianmaulana',
            'user@gmail.com' => 'sitinurhaliza',
            'siti@gmail.com' => 'sitinurhaliza',
            'dimas@gmail.com' => 'dimaspratama',
            'aisyah@gmail.com' => 'aisyahputri',
        ];

        $users = DB::table('users')->get();
        $usedUsernames = [];

        foreach ($users as $user) {
            if (isset($presetUsernames[$user->email]) && ! in_array($presetUsernames[$user->email], $usedUsernames)) {
                $username = $presetUsernames[$user->email];
            } else {
                $base = Str::slug(explode('@', $user->email)[0], '');
                if (empty($base)) {
                    $base = Str::slug($user->name, '');
                }
                if (empty($base)) {
                    $base = 'user'.$user->id;
                }

                $username = $base;
                $counter = 1;
                while (in_array($username, $usedUsernames)) {
                    $username = $base.$counter;
                    $counter++;
                }
            }

            $usedUsernames[] = $username;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'bio', 'banner']);
        });
    }
};
