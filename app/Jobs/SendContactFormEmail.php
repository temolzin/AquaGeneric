<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class SendContactFormEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Determine recipient: prefer MAIL_ADMIN, then mail.from.address, then MAIL_FROM_ADDRESS
        $to = env('MAIL_ADMIN') ?: config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        if (empty($to)) {
            return;
        }

        $logoPath = public_path('img/logo.png');
        $footerPath = public_path('img/rootheim.png');
        $viewData = array_merge($this->data, [
            'contactMessage' => $this->data['message'] ?? null,
        ]);

        $html = View::make('emails.contactForm', $viewData)->render();

        Mail::send([], [], function ($message) use ($to, $html) {
            $logoCid = $message->embed(public_path('img/logo.png'));
            $footerCid = $message->embed(public_path('img/rootheim.png'));
            $message->to($to)
                ->subject('Contacto desde sitio — ' . ($this->data['name'] ?? ''))
                ->setBody($html, 'text/html');

            if (!empty($this->data['email'])) {
                try { $message->replyTo($this->data['email'], $this->data['name'] ?? null); } catch (\Throwable $e) { /* ignore */ }
            }
        });
    }
}
