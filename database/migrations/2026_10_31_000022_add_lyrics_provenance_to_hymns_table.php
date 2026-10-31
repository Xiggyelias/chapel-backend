<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hymnal integrity: the original import padded hundreds of hymns with invented placeholder
 * verses. Those must never be projected in worship, so they are cleared here and each hymn
 * records whether genuine lyrics are present and where they came from.
 */
return new class extends Migration
{
    // Fragments that only occur in the generated placeholder verses
    private const PLACEHOLDER_MARKERS = [
        'our voices rise in praise,',
        'Imbai nziyo kuna Mwari wedu,',
    ];

    public function up(): void
    {
        Schema::table('hymns', function (Blueprint $table) {
            $table->string('lyrics_status', 20)->default('available')->after('lyrics');
            $table->string('lyrics_source')->nullable()->after('lyrics_status');
            $table->foreignId('lyrics_updated_by')->nullable()->after('lyrics_source')->constrained('users')->nullOnDelete();
        });

        foreach (self::PLACEHOLDER_MARKERS as $marker) {
            DB::table('hymns')->where('lyrics', 'like', '%' . $marker . '%')->update(['lyrics' => '']);
        }

        DB::table('hymns')
            ->where(fn ($q) => $q->whereNull('lyrics')->orWhere('lyrics', ''))
            ->update(['lyrics_status' => 'not_entered']);
    }

    public function down(): void
    {
        Schema::table('hymns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lyrics_updated_by');
            $table->dropColumn(['lyrics_status', 'lyrics_source']);
        });
    }
};
