<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * The portal previously shared one published default password across every account.
 * Any account still using it must set its own password at next sign-in.
 */
return new class extends Migration
{
    private const LEGACY_DEFAULT_PASSWORD = 'Chapel@2026!';

    public function up(): void
    {
        if (app()->environment('local', 'testing')) {
            return;
        }

        DB::table('users')->select(['id', 'password'])->orderBy('id')->each(function ($user) {
            if ($user->password && Hash::check(self::LEGACY_DEFAULT_PASSWORD, $user->password)) {
                DB::table('users')->where('id', $user->id)->update(['must_change_password' => true]);
            }
        });
    }

    public function down(): void
    {
        // Flags are cleared as each user sets a new password; nothing to reverse.
    }
};
