<?php

namespace Infrastructure\Twitch;

use RuntimeException;

class TwitchHttpClient
{
    private const int    CURL_TIMEOUT     = 10;
    private const int    HTTP_OK          = 200;
    private const string BASE_URL         = 'https://api.twitch.tv/helix';

    public function __construct(
        private string $clientId,
        private string $token
    ) {
    }

    public function get(string $path, array $params = []): array
    {
        $url      = $this->buildUrl($path, $params);
        $response = $this->executeRequest($url);

        return $response['data'] ?? [];
    }

    private function buildUrl(string $path, array $params): string
    {
        $url = self::BASE_URL . $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    private function executeRequest(string $url): array
    {
        $curl = $this->initCurl($url);

        $rawResponse = curl_exec($curl);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($curl);
        curl_close($curl);

        $this->assertRequestSucceeded($rawResponse, $curlError, $httpCode);

        return $this->decodeAndAssertResponse($rawResponse);
    }

    private function initCurl(string $url): \CurlHandle
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $this->buildAuthHeaders(),
            CURLOPT_TIMEOUT        => self::CURL_TIMEOUT,
        ]);

        return $curl;
    }

    private function buildAuthHeaders(): array
    {
        return [
            "Authorization: Bearer {$this->token}",
            "Client-Id: {$this->clientId}",
        ];
    }

    private function assertRequestSucceeded(mixed $response, string $curlError, int $httpCode): void
    {
        if ($response === false || $curlError !== '' || $httpCode !== self::HTTP_OK) {
            throw new RuntimeException("Twitch API error: HTTP $httpCode");
        }
    }

    private function decodeAndAssertResponse(string $rawResponse): array
    {
        $decoded = json_decode($rawResponse, true);

        if (isset($decoded['error'])) {
            throw new RuntimeException('Twitch API error: ' . ($decoded['message'] ?? $decoded['error']));
        }

        return $decoded;
    }
}
