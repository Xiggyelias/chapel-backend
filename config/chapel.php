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

    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    |
    | Alerts, sign-in details and charter receipts are emailed when a real mail
    | transport (e.g. MAIL_MAILER=smtp) is configured and this switch is on.
    |
    */

    'email' => [
        'enabled' => (bool) env('CHAPEL_EMAIL_NOTIFICATIONS', true),
    ],

    'backup' => [
        'secondary_dir' => env('CHAPEL_BACKUP_SECONDARY_DIR'),
        'keep' => (int) env('CHAPEL_BACKUP_KEEP', 14),
    ],

];
