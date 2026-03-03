<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Models\MailConfiguration;

class SendContactFormEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contactData;

    /**
     * Create a new job instance.
     *
     * @param array $contactData
     * @return void
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contactData = $this->contactData;

        $mailConfig = MailConfiguration::whereNotNull('host')
            ->whereNotNull('username')
            ->whereNotNull('password')
            ->first();

        if (!$mailConfig || !$mailConfig->isComplete()) {
            throw new \Exception('No hay una configuración de correo válida disponible para enviar el mensaje de contacto.');
        }

        Config::set('mail.mailers.smtp.host', $mailConfig->host);
        Config::set('mail.mailers.smtp.port', $mailConfig->port);
        Config::set('mail.mailers.smtp.username', $mailConfig->username);
        Config::set('mail.mailers.smtp.password', $mailConfig->password);
        Config::set('mail.mailers.smtp.encryption', $mailConfig->encryption);
        Config::set('mail.from.address', $mailConfig->username);
        Config::set('mail.from.name', $mailConfig->from_name ?? 'AquaControl');

        app('mail.manager')->forgetMailers();

        Mail::send([], [], function ($message) use ($contactData) {
            $logoCid = $message->embed(public_path('img/logo.png'));
            $footerCid = $message->embed(public_path('img/rootheim.png'));

            $html = View::make('emails.contactForm', array_merge($contactData, [
                'logoCid' => $logoCid,
                'footerCid' => $footerCid,
            ]))->render();

            $message->to(env('CONTACT_FORM_TO_EMAIL'))
                ->replyTo($contactData['email'], $contactData['name'])
                ->subject('Nuevo mensaje de contacto - AquaControl')
                ->setBody($html, 'text/html');
        });
    }
}
