<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Map incoming data to avoid colliding with the internal $message variable in mail views
        $viewData = [
            'name' => $this->data['name'] ?? null,
            'email' => $this->data['email'] ?? null,
            'contactMessage' => $this->data['message'] ?? null,
        ];

        $subject = 'Contacto desde sitio — ' . ($viewData['name'] ?? '');

        // Try to embed images and pass their CIDs into the view when possible
        $logoCid = null;
        $footerCid = null;
        try {
            if (file_exists(public_path('img/logo.png'))) {
                $logoCid = $this->embed(public_path('img/logo.png'));
            }
            if (file_exists(public_path('img/rootheim.png'))) {
                $footerCid = $this->embed(public_path('img/rootheim.png'));
            }
        } catch (\Throwable $e) {
            // ignore embed failures; view will fall back to asset() URLs
        }

        $m = $this->subject($subject)
                ->view('emails.contactForm')
                ->with(array_merge($viewData, ['logoCid' => $logoCid, 'footerCid' => $footerCid]));

        // Determine From: prefer MAIL_ADMIN, then configured mail.from.address, then MAIL_FROM_ADDRESS
        $fromAddress = env('MAIL_ADMIN') ?: config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_ADMIN_NAME') ?: config('mail.from.name') ?: env('MAIL_FROM_NAME') ?: null;

        if (!empty($fromAddress)) {
            if ($fromName) {
                $m->from($fromAddress, $fromName);
            } else {
                $m->from($fromAddress);
            }
        }

        if (!empty($viewData['email'])) {
            $m->replyTo($viewData['email'], $viewData['name'] ?? null);
        }

        return $m;
    }
}
