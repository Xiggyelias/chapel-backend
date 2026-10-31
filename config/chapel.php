<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Institutional Backups
    |--------------------------------------------------------------------------
    |
    | secondary_dir: optional second location (mounted drive / network share)
    | each backup archive is copied to. keep: number of daily archives retained.
    |
    */

    'backup' => [
        'secondary_dir' => env('CHAPEL_BACKUP_SECONDARY_DIR'),
        'keep' => (int) env('CHAPEL_BACKUP_KEEP', 14),
    ],

];
