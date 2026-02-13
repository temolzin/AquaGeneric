<?php

namespace App\Services;

use Openpay\Data\Openpay as OpenpayAPI;
use Openpay\Data\Openpay;
use App\Models\Payment;
use App\Models\OpenPayLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class OpenPayService
{
    protected $openpay;
    protected $merchantId;
    protected $sandbox;

    public function __construct()
    {
        $this->merchantId = config('openpay.merchant_id');
        $privateKey = config('openpay.private_key');
        $this->sandbox = config('openpay.sandbox');
        $country = strtoupper(config('openpay.country', 'MX'));

        OpenpayAPI::setCountry($country);
        OpenpayAPI::setEndpointUrl($country);

        $clientIp = $this->getClientIPv4Static();
        OpenpayAPI::setPublicIp($clientIp);

        $this->openpay = OpenpayAPI::getInstance($this->merchantId, $privateKey, $country, $clientIp);
        OpenpayAPI::setProductionMode(!$this->sandbox);
    }

    protected static function getClientIPv4Static()
    {
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_CF_CONNECTING_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        return $ip;
                    }
                }
            }
        }

        return '187.188.12.50';
    }

    protected function getClientIPv4()
    {
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_CF_CONNECTING_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        return $ip;
                    }
                }
            }
        }

        if ($this->sandbox) {
            return '187.188.12.50';
        }

        return null;
    }

    public function chargeWithToken($tokenId, $amount, $description, $orderId, $customer = null, $deviceSessionId = null)
    {
        try {
            $clientIp = $this->getClientIPv4();

            $chargeRequest = [
                'method' => 'card',
                'source_id' => $tokenId,
                'amount' => floatval($amount),
                'description' => $description,
                'order_id' => $orderId,
                'device_session_id' => $deviceSessionId,
            ];

            if ($customer) {
                if (is_array($customer) && $clientIp) {
                    $customer['clabe'] = $customer['clabe'] ?? null;
                }
                $chargeRequest['customer'] = $customer;
            }

            $charge = $this->openpay->charges->create($chargeRequest);

            $this->logTransaction('charge_success', 'success', $chargeRequest, $charge->serializableData, null, $charge->id);

            return [
                'success' => true,
                'transaction_id' => $charge->id,
                'authorization' => $charge->authorization ?? null,
                'status' => $charge->status,
                'card' => [
                    'type' => $charge->card->type ?? null,
                    'brand' => $charge->card->brand ?? null,
                    'card_number' => $charge->card->card_number ?? null,
                    'holder_name' => $charge->card->holder_name ?? null,
                ],
                'charge' => $charge,
            ];
        } catch (Exception $e) {
            $this->logTransaction('charge_error', 'error', $chargeRequest ?? [], null, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => method_exists($e, 'getErrorCode') ? $e->getErrorCode() : null,
            ];
        }
    }

    public function chargeWithCard($cardData, $amount, $description, $orderId, $deviceSessionId = null)
    {
        try {
            $chargeRequest = [
                'method' => 'card',
                'card' => [
                    'card_number' => $cardData['card_number'],
                    'holder_name' => $cardData['holder_name'],
                    'expiration_year' => $cardData['expiration_year'],
                    'expiration_month' => $cardData['expiration_month'],
                    'cvv2' => $cardData['cvv2'],
                ],
                'amount' => $amount,
                'description' => $description,
                'order_id' => $orderId,
                'device_session_id' => $deviceSessionId,
            ];

            $charge = $this->openpay->charges->create($chargeRequest);

            $this->logTransaction('charge_success', 'success', $chargeRequest, $charge->serializableData, null, $charge->id);

            return [
                'success' => true,
                'transaction_id' => $charge->id,
                'authorization' => $charge->authorization ?? null,
                'status' => $charge->status,
                'card' => [
                    'type' => $charge->card->type ?? null,
                    'brand' => $charge->card->brand ?? null,
                    'card_number' => $charge->card->card_number ?? null,
                    'holder_name' => $charge->card->holder_name ?? null,
                ],
                'charge' => $charge,
            ];
        } catch (Exception $e) {
            $this->logTransaction('charge_error', 'error', $chargeRequest ?? [], null, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => method_exists($e, 'getErrorCode') ? $e->getErrorCode() : null,
            ];
        }
    }

    public function getTransaction($transactionId)
    {
        try {
            $charge = $this->openpay->charges->get($transactionId);
            return [
                'success' => true,
                'charge' => $charge,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refund($transactionId, $description = null, $amount = null)
    {
        try {
            $refundData = ['description' => $description ?? 'Reembolso'];

            if ($amount) {
                $refundData['amount'] = $amount;
            }

            $refund = $this->openpay->charges->refund($transactionId, $refundData);

            $this->logTransaction('refund_success', 'success', $refundData, $refund->serializableData, null, $transactionId);

            return [
                'success' => true,
                'refund' => $refund,
            ];
        } catch (Exception $e) {
            $this->logTransaction('refund_error', 'error', $refundData ?? [], null, $e->getMessage(), $transactionId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createCustomer($customerData)
    {
        try {
            $customer = $this->openpay->customers->add([
                'name' => $customerData['name'],
                'last_name' => $customerData['last_name'] ?? '',
                'email' => $customerData['email'],
                'phone_number' => $customerData['phone_number'] ?? null,
                'address' => $customerData['address'] ?? null,
            ]);

            return [
                'success' => true,
                'customer_id' => $customer->id,
                'customer' => $customer,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function logTransaction($eventType, $status, $requestData, $responseData, $errorMessage, $transactionId = null)
    {
        try {
            OpenPayLog::create([
                'transaction_id' => $transactionId ?? ($responseData['id'] ?? null),
                'event_type' => $eventType,
                'status' => $status,
                'request_data' => $requestData,
                'response_data' => $responseData,
                'error_message' => $errorMessage,
                'ip_address' => request()->ip(),
            ]);
        } catch (Exception $e) {
            Log::error('Error logging OpenPay transaction: ' . $e->getMessage());
        }
    }

    public function verifyWebhook($requestData)
    {
        $expectedUser = config('openpay.webhook_user');
        $expectedPassword = config('openpay.webhook_password');

        if (!$expectedUser || !$expectedPassword) {
            return false;
        }

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        return $_SERVER['PHP_AUTH_USER'] === $expectedUser &&
            $_SERVER['PHP_AUTH_PW'] === $expectedPassword;
    }
}
