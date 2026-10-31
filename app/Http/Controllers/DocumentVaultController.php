<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Document;
use App\Services\PrivateFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentVaultController extends Controller
{
    /**
     * Display club folders and records.
     * Enforces club data isolation: Club executives see ONLY own club's vault.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        // Active club selection for Office; locked for club execs
        $selectedClubId = $user->isOffice() 
            ? ($request->input('club_id') ?? Club::active()->first()?->id)
            : $user->club_id;

        $selectedClub = Club::findOrFail($selectedClubId);
        $clubs = $user->isOffice() ? Club::active()->get() : collect([$selectedClub]);

        $folder = $request->input('folder', 'all');
        $query = Document::where('club_id', $selectedClubId)->with(['uploader', 'archiver']);

        if ($folder !== 'all') {
            $query->where('folder', $folder);
        }

        if (!$request->boolean('show_archived')) {
            $query->where('is_archived', false);
        }

        $documents = $query->orderBy('year', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        $folderLabels = Document::folderLabels();

        // Calculate folder stats
        $folderCounts = [];
        foreach ($folderLabels as $key => $label) {
            $folderCounts[$key] = Document::where('club_id', $selectedClubId)->where('folder', $key)->where('is_archived', false)->count();
        }

        return view('vault.index', compact('documents', 'selectedClub', 'clubs', 'folder', 'folderLabels', 'folderCounts', 'user'));
    }

    /**
     * Upload a new record to the Vault.
     * Auto-suggests and enforces standard file naming: YYYY-MM-DD_CLUB_Document-Title.pdf
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'folder' => ['required', Rule::in(array_keys(Document::folderLabels()))],
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1990', 'max:2099'],
            'file' => ['required', 'file', 'mimes:pdf,docx,xlsx,png,jpg,jpeg', 'max:25600'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Club data isolation check
        if (!$user->canAccessClub((int) $validated['club_id'])) {
            abort(403, 'Unauthorized. You may only upload records to your own club vault.');
        }

        $club = Club::findOrFail($validated['club_id']);
        $file = $request->file('file');
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());

        // Standardized naming: YYYY-MM-DD_CLUBCODE_Slugified-Title.ext
        $datePrefix = date('Y-m-d');
        $sanitizedTitle = Str::slug($validated['title']) ?: 'document';
        $standardizedFilename = "{$datePrefix}_{$club->code}_{$sanitizedTitle}.{$extension}";

        // Never overwrite an earlier record with the same name on the same day; keep each upload as its own version
        $version = Document::where('club_id', $club->id)->where('standardized_filename', $standardizedFilename)->count() + 1;
        if ($version > 1) {
            $standardizedFilename = "{$datePrefix}_{$club->code}_{$sanitizedTitle}_v{$version}.{$extension}";
        }

        $path = PrivateFileService::store($file, "vault/{$club->id}/{$validated['folder']}", $standardizedFilename);

        $doc = Document::create([
            'club_id' => $club->id,
            'folder' => $validated['folder'],
            'year' => $validated['year'],
            'title' => $validated['title'],
            'standardized_filename' => $standardizedFilename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_extension' => $extension,
            'version' => $version,
            'uploaded_by' => $user->id,
            'is_archived' => false,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('vault.index', ['club_id' => $club->id, 'folder' => $validated['folder']])
            ->with('success', "Document stored in Vault as {$standardizedFilename}. Preserved with version history.");
    }

    /**
     * Stream a vault record to an authorised viewer (own club or Office).
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        if (!Auth::user()->canAccessClub($document->club_id)) {
            abort(403, 'Club Data Isolation: You may only open records from your own club vault.');
        }

        return PrivateFileService::download($document->file_path, $document->standardized_filename);
    }

    /**
     * Archive document (Office Only: clubs can never delete records, only Chaplaincy Office can archive)
     */
    public function archive(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Per Chapel Manual: Clubs can never delete records; only the Chaplaincy Office can archive.');
        }

        $document = Document::findOrFail($id);
        $document->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => $user->id,
        ]);

        AuditLog::record('vault_document_archived', "Archived vault record '{$document->standardized_filename}' (club #{$document->club_id}).");

        return back()->with('info', "Record '{$document->standardized_filename}' has been safely archived.");
    }
}
