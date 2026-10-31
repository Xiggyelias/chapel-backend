<?php

namespace Database\Seeders;

use App\Models\Hymn;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HymnSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = __DIR__ . '/data/complete_hymns.json';

        if (file_exists($jsonPath)) {
            $this->seedFromCatalogue(json_decode(file_get_contents($jsonPath), true));

            return;
        }

        // Fallback core hymns if data file is missing
        $fallback = [
            [
                'hymnal_name' => 'The United Methodist Hymnal',
                'hymnal_code' => 'UMH',
                'number' => '1',
                'title' => 'The Basic Pattern of Worship',
                'author' => 'United Methodist Book of Worship',
                'tune' => null,
                'meter' => null,
                'scripture_ref' => '1 Corinthians 14:26-33',
                'category' => 'General Services & Orders',
                'lyrics' => "1. Entrance: The people gather in the Lord's name with praise, thanksgiving, and prayer.\n2. Proclamation and Response: The Scriptures are read and proclaimed.\n3. Thanksgiving and Communion: The Great Thanksgiving and Lord's Supper.\n4. Sending Forth: Going out in praise, service, and Christian fellowship.",
            ],
            [
                'hymnal_name' => 'The United Methodist Hymnal',
                'hymnal_code' => 'UMH',
                'number' => '57',
                'title' => 'O For a Thousand Tongues to Sing',
                'author' => 'Charles Wesley, 1739',
                'tune' => 'AZMON',
                'meter' => 'CM',
                'scripture_ref' => 'Psalm 35:28; Romans 10:9',
                'category' => 'Praise & Thanksgiving',
                'lyrics' => "1. O for a thousand tongues to sing\nmy great Redeemer's praise,\nthe glories of my God and King,\nthe triumphs of his grace!\n\n2. My gracious Master and my God,\nassist me to proclaim,\nto spread through all the earth abroad\nthe honors of thy name.\n\n3. Jesus! the name that charms our fears,\nthat bids our sorrows cease;\n'tis music in the sinner's ears,\n'tis life, and health, and peace.\n\n4. He breaks the power of canceled sin,\nhe sets the prisoner free;\nhis blood can make the foulest clean;\nhis blood availed for me.\n\n5. He speaks, and listening to his voice,\nnew life the dead receive;\nthe mournful, broken hearts rejoice,\nthe humble poor believe.\n\n6. Hear him, ye deaf; his praise, ye dumb,\nyour loosened tongues employ;\nye blind, behold your Savior come,\nand leap, ye lame, for joy.\n\n7. In Christ, your head, you then shall know,\nshall feel your sins forgiven;\nanticipate your heaven below,\nand own that love is heaven.",
            ],
            [
                'hymnal_name' => 'The United Methodist Hymnal',
                'hymnal_code' => 'UMH',
                'number' => '378',
                'title' => 'Amazing Grace',
                'author' => 'John Newton, 1779',
                'tune' => 'NEW BRITAIN',
                'meter' => 'CM',
                'scripture_ref' => '1 Chronicles 17:16-17; Ephesians 2:8',
                'category' => 'Prevenient & Justifying Grace',
                'lyrics' => "1. Amazing grace! How sweet the sound\nthat saved a wretch like me!\nI once was lost, but now am found;\nwas blind, but now I see.\n\n2. 'Twas grace that taught my heart to fear,\nand grace my fears relieved;\nhow precious did that grace appear\nthe hour I first believed.\n\n3. Through many dangers, toils, and snares,\nI have already come;\n'tis grace hath brought me safe thus far,\nand grace will lead me home.\n\n4. The Lord has promised good to me,\nhis word my hope secures;\nhe will my shield and portion be,\nas long as life endures.\n\n5. Yea, when this flesh and heart shall fail,\nand mortal life shall cease,\nI shall possess, within the veil,\na life of joy and peace.\n\n6. When we've been there ten thousand years,\nbright shining as the sun,\nwe've no less days to sing God's praise\nthan when we'd first begun.",
            ],
            [
                'hymnal_name' => 'The Methodist Hymn Book',
                'hymnal_code' => 'MHB',
                'number' => '1',
                'title' => 'O For A Thousand Tongues To Sing',
                'author' => 'Charles Wesley',
                'tune' => 'LYDIA',
                'meter' => 'CM',
                'scripture_ref' => 'Psalm 35:28',
                'category' => 'Adoration and Praise',
                'lyrics' => "1. O for a thousand tongues to sing\nMy great Redeemer's praise,\nThe glories of my God and King,\nThe triumphs of His grace!",
            ],
            [
                'hymnal_name' => 'Shona Methodist Hymnal (Ngoma)',
                'hymnal_code' => 'SHONA',
                'number' => '1',
                'title' => 'Pururudzai nyika dzose',
                'author' => 'Pisarema 100',
                'tune' => null,
                'meter' => null,
                'scripture_ref' => 'Pisarema 100',
                'category' => 'Rumbidzo',
                'lyrics' => "1. Pururudzai nyika dzose, imbai kuna Jehova,\nShumirai Ishe nomufaro, pamberi pake norufaro.",
            ],
        ];

        $this->seedFromCatalogue($fallback);
    }

    /**
     * Upserts the hymnal catalogue without ever destroying curated data:
     * hymns whose lyrics the Office has entered are left untouched, and an
     * entry without lyrics never blanks lyrics that already exist.
     */
    private function seedFromCatalogue(array $hymns): void
    {
        $now = now()->toDateTimeString();

        $curated = Hymn::whereNotNull('lyrics_updated_by')->get(['hymnal_code', 'number'])
            ->map(fn ($h) => "{$h->hymnal_code}|{$h->number}")
            ->flip();

        $withLyrics = [];
        $withoutLyrics = [];

        foreach ($hymns as $h) {
            $code = $h['hymnal_code'] ?? 'UMH';
            $number = (string) $h['number'];
            if ($curated->has("{$code}|{$number}")) {
                continue;
            }

            $lyrics = trim($h['lyrics'] ?? '');
            $record = [
                'hymnal_name' => $h['hymnal_name'] ?? 'The United Methodist Hymnal',
                'hymnal_code' => $code,
                'number' => $number,
                'title' => $h['title'],
                'author' => $h['author'] ?? 'Traditional',
                'tune' => $h['tune'] ?? null,
                'meter' => $h['meter'] ?? null,
                'scripture_ref' => $h['scripture_ref'] ?? null,
                'category' => $h['category'] ?? 'General',
                'lyrics' => $lyrics,
                'lyrics_status' => $lyrics === '' ? 'not_entered' : 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($lyrics === '') {
                $withoutLyrics[] = $record;
            } else {
                $withLyrics[] = $record;
            }
        }

        $metadata = ['hymnal_name', 'title', 'author', 'tune', 'meter', 'scripture_ref', 'category', 'updated_at'];

        DB::transaction(function () use ($withLyrics, $withoutLyrics, $metadata) {
            foreach (array_chunk($withLyrics, 200) as $chunk) {
                Hymn::upsert($chunk, ['hymnal_code', 'number'], array_merge($metadata, ['lyrics', 'lyrics_status']));
            }
            foreach (array_chunk($withoutLyrics, 200) as $chunk) {
                Hymn::upsert($chunk, ['hymnal_code', 'number'], $metadata);
            }
        });
    }
}
