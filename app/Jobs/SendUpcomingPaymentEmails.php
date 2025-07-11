<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Models\User;

class SendUpcomingPaymentEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customers;
    protected $authUserId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customers, $authUserId)
    {
        $this->customers = $customers;
        $this->authUserId = $authUserId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $authUser = User::find($this->authUserId);
        $mailConfig = $authUser->locality->mailConfiguration;

        Config::set('mail.mailers.smtp.host', $mailConfig->host);
        Config::set('mail.mailers.smtp.port', $mailConfig->port);
        Config::set('mail.mailers.smtp.username', $mailConfig->username);
        Config::set('mail.mailers.smtp.password', $mailConfig->password);
        Config::set('mail.mailers.smtp.encryption', $mailConfig->encryption);
        Config::set('mail.from.address', $mailConfig->from_address);
        Config::set('mail.from.name', $mailConfig->from_name ?? config('app.name'));

        foreach ($this->customers as $customerData) {
            if (!empty($customerData['customerEmail'])) {
                Mail::send([], [], function ($message) use ($customerData, $authUser) {
                    $logoCid = $message->embed(public_path('img/logo.png'));
                    $footerCid = $message->embed(public_path('img/rootheim.png'));

                    $html = View::make('emails.upcomingPaymentAlert', array_merge($customerData, [
                        'logoCid' => $logoCid,
                        'footerCid' => $footerCid,
                        'senderEmail' => $authUser->email,
                        'senderPhone' => $authUser->phone
                    ]))->render();

                    $message->to($customerData['customerEmail'])
                        ->subject('Recordatorio de pago próximo a vencer')
                        ->setBody($html, 'text/html');
                });
            }
        }
    }
}
