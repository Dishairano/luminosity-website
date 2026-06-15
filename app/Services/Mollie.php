<?php

namespace App\Services;

use App\Core\Config;

/**
 * Mollie-betalingen via de REST-API (zonder externe library; pure cURL).
 *
 * Met een geldige test-API-key (LUM_MOLLIE_KEY = test_...) maakt dit echte
 * test-betalingen aan en stuurt de klant naar de Mollie-checkout. Zonder key
 * valt het portaal terug op een GESIMULEERDE betaling (handig voor demo/dev):
 * de klant gaat dan naar een interne pagina die de factuur direct op betaald zet.
 */
final class Mollie
{
    private const BASE = 'https://api.mollie.com/v2';

    private string $key;
    private string $appUrl;

    public function __construct()
    {
        $this->key = (string) (Config::get('mollie')['key'] ?? '');
        $this->appUrl = rtrim((string) (Config::get('app')['url'] ?? ''), '/');
    }

    public function actief(): bool
    {
        return str_starts_with($this->key, 'test_') || str_starts_with($this->key, 'live_');
    }

    /**
     * Maakt een betaling voor een factuur en geeft de checkout-URL terug
     * (of een interne simulatie-URL als er geen key is).
     *
     * @return array{checkout:string, payment_id:?string}
     */
    public function betaalFactuur(array $factuur): array
    {
        $bedrag = number_format((float) $factuur['totaal'], 2, '.', '');
        $factuurId = (int) $factuur['id'];

        if (!$this->actief()) {
            // Gesimuleerde betaling (geen Mollie-key ingesteld).
            return ['checkout' => $this->appUrl . '/portaal/facturen/' . $factuurId . '/simuleer', 'payment_id' => null];
        }

        $body = [
            'amount'      => ['currency' => 'EUR', 'value' => $bedrag],
            'description' => 'Luminosity factuur ' . $factuur['nummer'],
            'redirectUrl' => $this->appUrl . '/portaal/facturen/' . $factuurId . '/terug',
            'webhookUrl'  => $this->appUrl . '/webhook/mollie',
            'metadata'    => ['factuur_id' => $factuurId],
        ];
        $res = $this->api('POST', '/payments', $body);
        return [
            'checkout'   => $res['_links']['checkout']['href'] ?? ($this->appUrl . '/portaal/facturen/' . $factuurId),
            'payment_id' => $res['id'] ?? null,
        ];
    }

    /** Haalt de actuele status van een betaling op: paid|open|failed|expired|canceled. */
    public function status(string $paymentId): array
    {
        if (!$this->actief()) {
            return ['status' => 'paid', 'method' => 'simulatie'];
        }
        $res = $this->api('GET', '/payments/' . rawurlencode($paymentId));
        return ['status' => $res['status'] ?? 'open', 'method' => $res['method'] ?? null];
    }

    private function api(string $method, string $path, ?array $body = null): array
    {
        $ch = curl_init(self::BASE . $path);
        $headers = ['Authorization: Bearer ' . $this->key, 'Content-Type: application/json'];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 20,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
        }
        $out = curl_exec($ch);
        curl_close($ch);
        $data = json_decode((string) $out, true);
        return is_array($data) ? $data : [];
    }
}
