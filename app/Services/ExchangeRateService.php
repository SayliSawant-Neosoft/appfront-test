<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http; // Laravel's HTTP client


class ExchangeRateService
{
    protected $baseUrl = 'https://open.er-api.com/v6/latest/';

    /**
     * Public function to get USD to EUR rate (cached for 1 hour)
     */
    public function getUsdToEurRate(): ?float
    {
        return Cache::remember('usd_to_eur_rate', 3600, function () {
            $rates = $this->fetchRates('USD');
            return $rates['EUR'] ?? null;
        });
    }

    /**
     * Private function to fetch rates using cURL
     */
    private function fetchRates(string $baseCurrency): ?array
    {
        $url = $this->baseUrl . $baseCurrency;
        // Send HTTP GET request
        $data = Http::get($url);
        // Check if the response was successful
        if ($data->successful()) {
            return $data->json()['rates'];
        } else {
            \Log::error("Failed to fetch exchange rate", ['status' => $data->status()]);
            return null;
        }
       return env('EXCHANGE_RATE', 0.85);
}
}
?>