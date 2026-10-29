<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $kind,
        private readonly array $details,
        private readonly bool $agencyCopy = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isQuote = $this->kind === 'quote';
        $subject = $this->agencyCopy
            ? ($isQuote ? 'New quote request received' : 'New contact inquiry received')
            : ($isQuote ? 'We received your quote request' : 'We received your inquiry');

        $mail = (new MailMessage())
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->greeting($this->agencyCopy ? 'A new inquiry is ready for review.' : 'Thanks for getting in touch.')
            ->line('Name: '.($this->details['full_name'] ?? ''))
            ->line('Email: '.($this->details['email'] ?? ''));

        if ($this->agencyCopy) {
            $mail->line('Company: '.($this->details['company_name'] ?? '—'));
            if ($isQuote) {
                $mail->line('Service: '.($this->details['service'] ?? '—'))
                    ->line('Budget: '.($this->details['budget_range'] ?? '—'))
                    ->line('Description: '.($this->details['project_description'] ?? ''));
            } else {
                $mail->line('Subject: '.($this->details['subject'] ?? ''))
                    ->line('Message: '.($this->details['message'] ?? ''));
            }

            return $mail->action('Open lead dashboard', rtrim((string) config('app.frontend_url'), '/').'/admin/leads');
        }

        return $mail
            ->line('Our team will review your message and follow up using the contact details you provided.')
            ->line('Please keep this email for your records.');
    }
}
