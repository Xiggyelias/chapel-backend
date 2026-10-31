<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Hymn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HymnController extends Controller
{
    /**
     * Display searchable Hymnal index view (The United Methodist Hymnal & Chapel Hymnody)
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $hymnal = trim($request->input('hymnal', ''));
        $lyricsFilter = $request->input('lyrics');

        $query = Hymn::query();

        if ($hymnal) {
            $query->where('hymnal_code', $hymnal);
        }

        if ($lyricsFilter === 'missing') {
            $query->missingLyrics();
        } elseif ($lyricsFilter === 'available') {
            $query->where('lyrics_status', 'available');
        }

        if ($search) {
            $cleanedNum = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanedNum) {
                if ($cleanedNum) {
                    $q->where('number', $cleanedNum)
                      ->orWhere('number', 'like', "%{$cleanedNum}%");
                }
                $q->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('tune', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('lyrics', 'like', "%{$search}%");
            });
        }

        $hymns = $query->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END, CAST(number AS INTEGER) ASC")
            ->paginate(24)
            ->withQueryString();
        $totalHymns = Hymn::count();
        $missingByHymnal = Hymn::missingLyrics()->selectRaw('hymnal_code, count(*) as total')->groupBy('hymnal_code')->pluck('total', 'hymnal_code');
        $canEdit = (bool) $request->user()?->isOffice();

        return view('hymns.index', compact('hymns', 'search', 'hymnal', 'totalHymns', 'lyricsFilter', 'missingByHymnal', 'canEdit'));
    }

    /**
     * Office: enter or correct a hymn's text from the chapel's licensed printed hymnal.
     */
    public function update(Request $request, $id)
    {
        $hymn = Hymn::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'tune' => ['nullable', 'string', 'max:100'],
            'lyrics' => ['nullable', 'string', 'max:20000'],
            'lyrics_source' => ['required_with:lyrics', 'nullable', 'string', 'max:255'],
        ], [
            'lyrics_source.required_with' => 'Record where the text came from (e.g. "Printed UMH 1989, CCLI licence 123456").',
        ]);

        $lyrics = trim(str_replace("\r\n", "\n", $validated['lyrics'] ?? ''));

        $hymn->update([
            'title' => $validated['title'],
            'author' => ($validated['author'] ?? null) ?: $hymn->author,
            'tune' => ($validated['tune'] ?? null) ?: $hymn->tune,
            'lyrics' => $lyrics,
            'lyrics_status' => $lyrics === '' ? 'not_entered' : 'available',
            'lyrics_source' => $lyrics === '' ? null : $validated['lyrics_source'],
            'lyrics_updated_by' => $request->user()->id,
        ]);

        AuditLog::record('hymn_lyrics_updated', "Updated {$hymn->hymnal_code} {$hymn->number} \"{$hymn->title}\" (" . ($lyrics === '' ? 'lyrics cleared' : 'source: ' . $validated['lyrics_source']) . ').');

        return back()->with('success', "{$hymn->hymnal_code} {$hymn->number} \"{$hymn->title}\" saved.");
    }

    /**
     * Fast API lookup by hymn number
     * e.g. /api/hymns/lookup?number=57
     */
    public function lookup(Request $request): JsonResponse
    {
        $rawInput = trim($request->input('number', ''));
        $hymnalCode = strtoupper(trim($request->input('hymnal', '')));

        if (!$rawInput) {
            return response()->json([
                'found' => false,
                'message' => 'Please enter a hymn number.',
            ], 400);
        }

        // Determine if hymnal code is in input (e.g. "UMH 57" or "MHB 1" or "Shona 11")
        if (preg_match('/shona/i', $rawInput)) {
            $hymnalCode = 'SHONA';
        } elseif (preg_match('/mhb/i', $rawInput)) {
            $hymnalCode = 'MHB';
        } elseif (preg_match('/umh/i', $rawInput)) {
            $hymnalCode = 'UMH';
        }

        // Extract just the number digits
        $number = preg_replace('/[^0-9]/', '', $rawInput);

        if (!$number) {
            // If they searched by title directly
            $hymn = Hymn::where('title', 'like', "%{$rawInput}%")
                ->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END")
                ->first();
        } else {
            $query = Hymn::where('number', $number);
            if ($hymnalCode) {
                $query->where('hymnal_code', $hymnalCode);
            } else {
                // Priority order: UMH first, then MHB, then SHONA
                $query->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END");
            }
            $hymn = $query->first();

            // If not found in specified hymnal code, search any hymnal
            if (!$hymn) {
                $hymn = Hymn::where('number', $number)
                    ->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END")
                    ->first();
            }
        }

        if (!$hymn) {
            return response()->json([
                'found' => false,
                'number' => $rawInput,
                'message' => "Hymn #{$rawInput} was not found in The United Methodist Hymnal registry.",
            ], 404);
        }

        // Fetch any alternate hymns with this same number across the other collections
        $alternates = Hymn::where('number', $hymn->number)
            ->where('id', '!=', $hymn->id)
            ->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END")
            ->get()
            ->map(function ($alt) {
                return [
                    'id' => $alt->id,
                    'hymnal_code' => $alt->hymnal_code,
                    'hymnal_name' => $alt->hymnal_name,
                    'number' => $alt->number,
                    'title' => $alt->title,
                    'full_title' => "{$alt->hymnal_code} {$alt->number}: {$alt->title}",
                    'tune' => $alt->tune,
                ];
            });

        return response()->json([
            'found' => true,
            'hymn' => [
                'id' => $hymn->id,
                'hymnal_name' => $hymn->hymnal_name,
                'hymnal_code' => $hymn->hymnal_code,
                'number' => $hymn->number,
                'title' => $hymn->title,
                'full_title' => "{$hymn->hymnal_code} {$hymn->number}: {$hymn->title}",
                'author' => $hymn->author,
                'tune' => $hymn->tune,
                'meter' => $hymn->meter,
                'scripture_ref' => $hymn->scripture_ref,
                'category' => $hymn->category,
                'lyrics' => $hymn->hasLyrics() ? $hymn->lyrics : '',
                'lyrics_available' => $hymn->hasLyrics(),
            ],
            'alternates' => $alternates,
        ]);
    }

    /**
     * Autocomplete search for dropdown or live query
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        $hymnal = trim($request->input('hymnal', ''));

        $query = Hymn::query();
        if ($hymnal) {
            $query->where('hymnal_code', $hymnal);
        }

        if (!$q) {
            $hymns = $query->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END, CAST(number AS INTEGER) ASC")
                ->take(30)
                ->get();
        } else {
            $cleanedNum = preg_replace('/[^0-9]/', '', $q);
            $hymns = $query->where(function ($sub) use ($q, $cleanedNum) {
                if ($cleanedNum) {
                    $sub->where('number', $cleanedNum)
                        ->orWhere('number', 'like', "{$cleanedNum}%");
                }
                $sub->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('lyrics', 'like', "%{$q}%");
            })
            ->orderByRaw("CASE hymnal_code WHEN 'UMH' THEN 1 WHEN 'MHB' THEN 2 WHEN 'SHONA' THEN 3 ELSE 4 END, CAST(number AS INTEGER) ASC")
            ->take(20)
            ->get();
        }

        return response()->json([
            'results' => $hymns->map(function ($h) {
                return [
                    'id' => $h->id,
                    'hymnal_code' => $h->hymnal_code,
                    'hymnal_name' => $h->hymnal_name,
                    'number' => $h->number,
                    'title' => $h->title,
                    'full_title' => "{$h->hymnal_code} {$h->number}: {$h->title}",
                    'category' => $h->category,
                    'lyrics_available' => $h->hasLyrics(),
                    'lyrics_preview' => $h->hasLyrics() ? mb_substr($h->lyrics, 0, 100) . '...' : 'Lyrics not yet entered',
                ];
            }),
        ]);
    }
}
