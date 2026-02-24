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
        // Determine recipient: prefer MAIL_USERNAME, then mail.from.address, then MAIL_FROM_ADDRESS
        $to = env('MAIL_USERNAME') ?: config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        if (empty($to)) {
            return;
        }

        $fromAddress = env('MAIL_FROM_ADDRESS') ?: $to;
        $fromName = env('MAIL_FROM_NAME') ?: config('app.name');

        $logoPath = public_path('img/logo.png');
        $footerPath = public_path('img/rootheim.png');
        $viewData = array_merge($this->data, [
            'contactMessage' => $this->data['message'] ?? null,
        ]);

        $html = View::make('emails.contactForm', $viewData)->render();

        Mail::send([], [], function ($message) use ($to, $fromAddress, $fromName, $html, $logoPath, $footerPath) {
            $logoCid = $message->embed($logoPath);
            $footerCid = $message->embed($footerPath);

            $message->to($to)
                ->from($fromAddress, $fromName)
                ->subject('Contacto desde sitio — ' . ($this->data['name'] ?? ''))
                ->setBody($html, 'text/html');

            if (!empty($this->data['email'])) {
                try { $message->replyTo($this->data['email'], $this->data['name'] ?? null); } catch (\Throwable $e) { /* ignore */ }
            }
        });
    }
}
