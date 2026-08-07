<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PipraPayService
{
    private string $baseUrl;

    private string $apiKey;

    private string $currency;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.piprapay.base_url'), '/');
        $this->apiKey = config('services.piprapay.api_key');
        $this->currency = config('services.piprapay.currency', 'BDT');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Create a payment charge and return the response array from PipraPay.
     * Expected shape on success: ['status' => true, 'pp_id' => ..., 'pp_url' => ...]
     */
    public function createCharge(array $data): array
    {
        $response = Http::withHeaders([
            'mh-piprapay-api-key' => $this->apiKey,
        ])->post("{$this->baseUrl}/api/create-charge", [
            'full_name' => $data['full_name'],
            'email_mobile' => $data['email_mobile'],
            'amount' => (string) $data['amount'],
            'metadata' => $data['metadata'] ?? [],
            'redirect_url' => $data['redirect_url'],
            'return_type' => 'GET',
            'cancel_url' => $data['cancel_url'],
            'webhook_url' => $data['webhook_url'],
            'currency' => $this->currency,
        ]);

        return $response->json() ?? ['status' => false, 'message' => 'Invalid response from payment gateway.'];
    }

    /**
     * Verify a payment by its pp_id. Always re-check with the gateway rather than
     * trusting the webhook/redirect payload, since PipraPay does not sign requests.
     */
    public function verifyPayment(string $ppId): array
    {
        $response = Http::withHeaders([
            'mh-piprapay-api-key' => $this->apiKey,
        ])->post("{$this->baseUrl}/api/verify-payments", [
            'pp_id' => $ppId,
        ]);

        return $response->json() ?? ['status' => false, 'message' => 'Invalid response from payment gateway.'];
    }
}
