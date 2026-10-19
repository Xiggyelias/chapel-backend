<?php

namespace App\Services;

use Carbon\Carbon;

class LiturgicalCalendarService
{
    /**
     * Determine the current Liturgical Season in the Methodist and Ecumenical Christian calendar.
     */
    public static function getCurrentSeason(?Carbon $date = null): array
    {
        $date = $date ?: Carbon::today();
        $month = (int) $date->format('m');
        $day = (int) $date->format('d');

        // Advent: Late November through Dec 24
        if (($month === 11 && $day >= 27) || ($month === 12 && $day <= 24)) {
            return [
                'name' => 'Season of Advent',
                'title' => 'Advent (Season of Expectation & Hope)',
                'color' => 'Liturgical Purple / Royal Blue',
                'hex' => '#6B21A8',
                'badge_class' => 'bg-purple-100 text-purple-900 border-purple-300',
                'dot_class' => 'bg-purple-600',
                'parament' => 'Purple Paraments on Pulpit & Lectern — Symbol of spiritual preparation, repentance, and longing for the Messiah.',
                'verse' => '"The people who walked in darkness have seen a great light." — Isaiah 9:2',
                'focus' => 'Preparation for Christ\'s birth, personal repentance, and renewed hope for Africa and the world.',
            ];
        }

        // Christmastide: Dec 25 through Jan 5
        if (($month === 12 && $day >= 25) || ($month === 1 && $day <= 5)) {
            return [
                'name' => 'Christmastide',
                'title' => 'Christmastide (Festival of the Incarnation)',
                'color' => 'Liturgical White & Gold',
                'hex' => '#D4AF37',
                'badge_class' => 'bg-amber-100 text-amber-900 border-amber-300',
                'dot_class' => 'bg-amber-500',
                'parament' => 'White and Gold Altar Paraments — Symbol of purity, light, joy, and the glory of God in human flesh.',
                'verse' => '"The Word became flesh and dwelt among us, full of grace and truth." — John 1:14',
                'focus' => 'Celebration of God With Us (Emmanuel), reconciliation, and joy.',
            ];
        }

        // Epiphany: Jan 6 through early February
        if ($month === 1 && $day >= 6) {
            return [
                'name' => 'Season of Epiphany',
                'title' => 'Epiphany (Manifestation of Christ to the Nations)',
                'color' => 'Liturgical Green / Gold',
                'hex' => '#15803D',
                'badge_class' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                'dot_class' => 'bg-emerald-600',
                'parament' => 'Green Paraments with Gold Accents — Manifestation of Jesus to the Gentiles and Africa.',
                'verse' => '"Arise, shine; for your light has come, and the glory of the Lord has risen upon you." — Isaiah 60:1',
                'focus' => 'Mission to all nations, intellectual enlightenment, and Pan-African educational empowerment.',
            ];
        }

        // Lent (approx February - March / April)
        if ($month >= 2 && $month <= 4) {
            // Simplified seasonal approximation for university calendar
            return [
                'name' => 'Season of Lent & Passiontide',
                'title' => 'Lent (Forty Days of Spiritual Renewal)',
                'color' => 'Liturgical Purple',
                'hex' => '#581C87',
                'badge_class' => 'bg-purple-100 text-purple-900 border-purple-300',
                'dot_class' => 'bg-purple-600',
                'parament' => 'Deep Purple Altar Vestments — Fasting, solemnity, and cross-centered discipleship.',
                'verse' => '"Create in me a clean heart, O God; and renew a right spirit within me." — Psalm 51:10',
                'focus' => 'Self-examination, university-wide prayer vigilance, fasting, and charity.',
            ];
        }

        // Eastertide (April - May)
        if ($month === 5) {
            return [
                'name' => 'Eastertide',
                'title' => 'Eastertide (Season of the Resurrection)',
                'color' => 'Liturgical White & Gold',
                'hex' => '#D4AF37',
                'badge_class' => 'bg-amber-100 text-amber-900 border-amber-300',
                'dot_class' => 'bg-amber-500',
                'parament' => 'White with Gold Brocade — Christ is Risen, victorious over death and sin.',
                'verse' => '"I am the resurrection and the life. Whoever believes in me will live." — John 11:25',
                'focus' => 'Resurrection power, student transformation, and victorious Christian living.',
            ];
        }

        // Season after Pentecost / Kingdomtide (June through November - including current month October)
        return [
            'name' => 'Season after Pentecost',
            'title' => 'Kingdomtide / Season of Pentecost (Ordinary Time)',
            'color' => 'Liturgical Green',
            'hex' => '#15803D',
            'badge_class' => 'bg-emerald-50 text-emerald-900 border-emerald-300',
            'dot_class' => 'bg-emerald-600',
            'parament' => 'Green Paraments on Altar, Pulpit & Lectern — Symbolizing life in the Spirit, spiritual growth, discipleship, and continuous kingdom service.',
            'verse' => '"I will lift up mine eyes unto the hills, from whence cometh my help. My help cometh from the Lord." — Psalm 121:1-2',
            'focus' => 'Discipleship, fellowship nurturing, university academic diligence, and pan-African ministry leadership.',
        ];
    }

    /**
     * Complete Ecumenical & Methodist Liturgical Colors & Seasons Reference Guide.
     */
    public static function getLiturgicalGuide(): array
    {
        return [
            [
                'season' => 'Season after Pentecost (Kingdomtide)',
                'color' => 'Liturgical Green',
                'hex' => '#16A34A',
                'meaning' => 'Spiritual growth, discipleship, mission, and the continuous living work of the Church in the world.',
                'scripture' => 'Psalm 121:1-2 &bull; Galatians 5:22-23',
                'vestments' => 'Green paraments on pulpit, lectern, and altar; green stoles for ordained clergy.',
                'is_current' => true,
            ],
            [
                'season' => 'Advent',
                'color' => 'Purple / Royal Blue',
                'hex' => '#7C3AED',
                'meaning' => 'Expectation, hope, penitence, and spiritual preparation for the coming of Jesus Christ.',
                'scripture' => 'Isaiah 9:2-7 &bull; Luke 1:26-38',
                'vestments' => 'Purple or royal blue paraments and clerical stoles; Advent wreath with 4 candles.',
                'is_current' => false,
            ],
            [
                'season' => 'Christmas & Epiphany',
                'color' => 'White & Gold',
                'hex' => '#EAB308',
                'meaning' => 'The Incarnation of God in Jesus Christ, purity, divine light, and the manifestation of God to all nations.',
                'scripture' => 'John 1:1-14 &bull; Matthew 2:1-12',
                'vestments' => 'White paraments with gold brocade or embroidery; festival stoles.',
                'is_current' => false,
            ],
            [
                'season' => 'Lent & Holy Week',
                'color' => 'Purple / Black',
                'hex' => '#581C87',
                'meaning' => 'Forty days of fasting, self-examination, repentance, and identification with Christ\'s suffering and cross.',
                'scripture' => 'Psalm 51 &bull; Matthew 4:1-11 &bull; Philippians 2:5-11',
                'vestments' => 'Deep purple paraments; black paraments or bare altars on Good Friday.',
                'is_current' => false,
            ],
            [
                'season' => 'Eastertide & Pentecost Sunday',
                'color' => 'White/Gold & Red',
                'hex' => '#DC2626',
                'meaning' => 'The Resurrection of the Lord (White/Gold); the descent of the Holy Spirit on Pentecost (Fiery Red).',
                'scripture' => 'Luke 24:1-12 &bull; Acts 2:1-21',
                'vestments' => 'White/Gold for Easter season; Red paraments and stoles on the Day of Pentecost.',
                'is_current' => false,
            ],
        ];
    }
}
