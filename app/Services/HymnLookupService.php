<?php

namespace App\Services;

use App\Models\Hymn;
use Illuminate\Database\Eloquent\Collection;

class HymnLookupService
{
    /**
     * Lookup a single hymn by its Methodist Hymnal number.
     */
    public function findByNumber(int $number): ?Hymn
    {
        return Hymn::where('number', $number)->first();
    }

    /**
     * Search hymns by number, title, lyrics, or category.
     */
    public function search(string $query, int $limit = 10): Collection
    {
        $term = trim($query);

        if (empty($term)) {
            return Hymn::orderBy('number')->take($limit)->get();
        }

        // If numeric search, exact or prefix match number
        if (is_numeric($term)) {
            return Hymn::where('number', $term)
                ->orWhere('number', 'like', "{$term}%")
                ->take($limit)
                ->get();
        }

        return Hymn::where('title', 'like', "%{$term}%")
            ->orWhere('lyrics', 'like', "%{$term}%")
            ->orWhere('category', 'like', "%{$term}%")
            ->orWhere('tune', 'like', "%{$term}%")
            ->take($limit)
            ->get();
    }
}
