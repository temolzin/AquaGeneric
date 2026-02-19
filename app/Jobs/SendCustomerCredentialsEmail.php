<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SendCustomerCredentialsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $customerId;
    public int $authUserId;
    public string $temporaryPassword;

    public function __construct(int $customerId, int $authUserId, string $temporaryPassword)
    {
        $this->customerId = $customerId;
        $this->authUserId = $authUserId;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function handle(): void
    {
        $tempPath = null;

        try {

            $authUser = User::find($this->authUserId);
            if (!$authUser || !$authUser->locality || !$authUser->locality->mailConfiguration) {
                return;
            }

            $mailConfig = $authUser->locality->mailConfiguration;

            Config::set('mail.mailers.smtp.host', $mailConfig->host);
            Config::set('mail.mailers.smtp.port', $mailConfig->port);
            Config::set('mail.mailers.smtp.username', $mailConfig->username);
            Config::set('mail.mailers.smtp.password', $mailConfig->password);
            Config::set('mail.mailers.smtp.encryption', $mailConfig->encryption);
            Config::set('mail.from.address', $mailConfig->username);
            Config::set('mail.from.name', $mailConfig->from_name ?? config('app.name'));

            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');

            Log::info('SMTP host applied: ' . config('mail.mailers.smtp.host'));
            Log::info('SMTP user applied: ' . config('mail.mailers.smtp.username'));

            $customer = Customer::with('user')->find($this->customerId);
            if (!$customer || !$customer->user) {
                return;
            }

            $customerEmail = $customer->email ?? $customer->user->email;
            if (empty($customerEmail)) {
                return;
            }

            $pdf = Pdf::loadView('reports.genneratepasswordforcustomer', [
                'customer' => $customer,
                'user' => $customer->user,
                'temporaryPassword' => $this->temporaryPassword,
                'showCustomerId' => false,
            ])->setPaper('A4', 'portrait');

            $pdfBinary = $pdf->output();
            $pdfName = 'DatosUsuario_' . $customer->id . '.pdf';

            $tempDir = storage_path('app/tmp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $tempPath = $tempDir . DIRECTORY_SEPARATOR . $pdfName;
            File::put($tempPath, $pdfBinary);

            Log::info('Temp PDF exists before send: ' . (File::exists($tempPath) ? 'YES' : 'NO'));
            if (File::exists($tempPath)) {
                Log::info('Temp PDF size: ' . File::size($tempPath));
            }

            $tempPassword = $this->temporaryPassword;

            app('mailer')->send([], [], function ($message) use ($customerEmail, $authUser, $customer, $tempPath, $pdfName, $tempPassword) {

                Log::info('Mail message class: ' . get_class($message));

                if (!class_exists(\Swift_Attachment::class)) {
                    Log::error('Swift_Attachment NO existe en el sistema');
                    throw new \RuntimeException('Swift_Attachment class not found');
                }

                $logoCid = null;
                $footerCid = null;

                if (file_exists(public_path('img/logo.png'))) {
                    $logoCid = $message->embed(public_path('img/logo.png'));
                }
                if (file_exists(public_path('img/rootheim.png'))) {
                    $footerCid = $message->embed(public_path('img/rootheim.png'));
                }

                $html = View::make('emails.customerCredentials', [
                    'customer' => $customer,
                    'user' => $customer->user,
                    'temporaryPassword' => $tempPassword,
                    'logoCid' => $logoCid,
                    'footerCid' => $footerCid,
                    'senderEmail' => $authUser->email,
                    'senderPhone' => $authUser->phone,
                ])->render();

                $message->to($customerEmail)
                    ->subject('Acceso al sistema - Credenciales de usuario')
                    ->setBody($html, 'text/html');

                $swiftAttachment = \Swift_Attachment::fromPath($tempPath)
                    ->setFilename($pdfName)
                    ->setContentType('application/pdf');

                $message->getSwiftMessage()->attach($swiftAttachment);
            });

        } catch (\Throwable $e) {
            Log::error('SendCustomerCredentialsEmail failed', [
                'customerId' => $this->customerId,
                'authUserId' => $this->authUserId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;

        } finally {
            if ($tempPath && File::exists($tempPath)) {
                File::delete($tempPath);
            }
        }
    }
}
