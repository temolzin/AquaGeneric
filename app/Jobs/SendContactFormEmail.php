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

    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    public function handle()
    {
        $contactData = $this->contactData;

        $mailConfig = MailConfiguration::whereNull('locality_id')
            ->whereNotNull('host')
            ->whereNotNull('username')
            ->whereNotNull('password')
            ->first();

        if (!$mailConfig || !$mailConfig->isComplete()) {
            throw new \Exception('No hay una configuración de correo válida disponible para enviar el mensaje de contacto.');
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
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

            $message->to(config('mail.contact_form_to'))
                ->replyTo($contactData['email'], $contactData['name'])
                ->subject('Nuevo mensaje de contacto - AquaControl')
                ->setBody($html, 'text/html');
        });
    }
}
