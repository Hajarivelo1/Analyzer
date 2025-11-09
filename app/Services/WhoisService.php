<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhoisService
{
    public function lookup(string $domain): array
    {
        $customerId = config('services.whois.customer_id');
        $apiKey = config('services.whois.api_key');

        $response = Http::withBasicAuth($customerId, $apiKey)
    ->withoutVerifying() // ⬅️ désactive la vérification SSL
    ->get('https://jsonwhoisapi.com/api/v1/whois', [
        'identifier' => $domain
    ]);


        return $response->json();
    }
}
