<?php

namespace App\Console\Commands;

use App\Services\PortalNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Sends one email immediately (bypassing the queue) to prove the SMTP settings work.
 */
class MailTestCommand extends Command
{
    protected $signature = 'chapel:mail-test {to : Address to send the test email to}';

    protected $description = 'Send a test email to verify the mail (SMTP) configuration';

    public function handle(): int
    {
        $to = $this->argument('to');

        $this->line('Mailer: ' . config('mail.default') . ' • host: ' . (config('mail.mailers.smtp.host') ?? '-') . ':' . (config('mail.mailers.smtp.port') ?? '-'));
        $this->line('From:   ' . config('mail.from.address'));
        if (!PortalNotifier::emailEnabled()) {
            $this->warn('Portal email notifications are currently OFF (MAIL_MAILER is log/array or CHAPEL_EMAIL_NOTIFICATIONS=false).');
        }

        try {
            Mail::raw(
                "This is a test message from the Kwang-Lim Chapel portal.\n\nIf you received it, email notifications are configured correctly.",
                fn ($message) => $message->to($to)->subject('Kwang-Lim Chapel portal: test email')
            );
        } catch (\Throwable $e) {
            $this->error('Sending failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Test email sent to {$to}.");

        return self::SUCCESS;
    }
}
