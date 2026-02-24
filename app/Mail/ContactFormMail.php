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

        $fromAddress = env('MAIL_FROM_ADDRESS') ?: env('MAIL_USERNAME');
        $fromName = env('MAIL_FROM_NAME') ?: config('app.name');

        $m = $this->subject($subject)
                ->view('emails.contactForm')
                ->with(array_merge($viewData, ['logoCid' => $logoCid, 'footerCid' => $footerCid]));

        if (!empty($fromAddress)) {
            $m->from($fromAddress, $fromName);
        }

        if (!empty($viewData['email'])) {
            $m->replyTo($viewData['email'], $viewData['name'] ?? null);
        }

        return $m;
    }
}
