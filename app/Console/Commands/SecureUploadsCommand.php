<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Document;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Moves records uploaded before files were made private off the public disk, where they
 * were reachable at guessable /storage/... URLs. Paths are unchanged, so database
 * references keep working; downloads then go through the authorised routes only.
 */
class SecureUploadsCommand extends Command
{
    protected $signature = 'chapel:secure-uploads {--dry-run : List the files that would be moved without moving them}';

    protected $description = 'Move legacy uploads from the public disk to private storage';

    public function handle(): int
    {
        $paths = collect()
            ->merge(Document::pluck('file_path'))
            ->merge(ChapelRequest::pluck('attachment_path'))
            ->merge(ChapelRequest::pluck('vc_go_ahead_attachment'))
            ->merge(Club::pluck('constitution_path'))
            ->merge(Report::pluck('photos')->flatten())
            ->filter()
            ->unique()
            ->values();

        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;

        foreach ($paths as $path) {
            if (!$public->exists($path)) {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("would move: {$path}");
                $moved++;
                continue;
            }

            if (!$private->exists($path)) {
                $private->writeStream($path, $public->readStream($path));
            }

            if ($private->exists($path) && $private->size($path) === $public->size($path)) {
                $public->delete($path);
                $moved++;
            } else {
                $this->error("Copy verification failed, left in place: {$path}");
            }
        }

        $this->info(($this->option('dry-run') ? 'Would secure ' : 'Secured ') . "{$moved} file(s).");

        return self::SUCCESS;
    }
}
