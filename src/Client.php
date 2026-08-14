<?php

namespace IpWho\SDK;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use IpWho\SDK\Exception\APIResponseException;
use IpWho\SDK\Exception\InvalidIPException;
use IpWho\SDK\Exception\RateLimitException;
use IpWho\SDK\Models\IpGeoResponse;

class Client
{
    private const DEFAULT_BASE_URL = 'https://api.ipwho.org';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $baseUrl;

    /** @var HttpClient */
    private $http;

    /**
     * @param string      $apiKey    Your IPWho API key (required).
     * @param array       $options
     * @param string|null $options['baseUrl']  Override base URL.
     * @param float       $options['timeout']  Request timeout in seconds (default 30).
     * @param HttpClient|null $httpClient      Inject a custom Guzzle client.
     */
    public function __construct(string $apiKey, array $options = [], ?HttpClient $httpClient = null)
    {
        if (empty($apiKey)) {
            throw new APIResponseException('apiKey is required');
        }
        $this->apiKey  = $apiKey;
        $this->baseUrl = rtrim($options['baseUrl'] ?? self::DEFAULT_BASE_URL, '/');
        $this->http    = $httpClient ?? new HttpClient([
            'base_uri' => $this->baseUrl,
            'timeout'  => $options['timeout'] ?? 30.0,
            'headers'  => [
                'Accept'     => 'application/json',
                'User-Agent' => 'ipwho-php-sdk/1.0.0',
            ],
        ]);
    }

    // ── public API ──────────────────────────────────────────────────

    /**
     * Look up geolocation for a specific IPv4 or IPv6 address.
     *
     * @param string        $ip
     * @param array         $options
     * @param string        $options['format']  json|xml|csv (default json)
     * @param string|null   $options['get']     Comma-separated fields filter
     * @return IpGeoResponse
     * @throws InvalidIPException       – 404
     * @throws RateLimitException       – 429
     * @throws APIResponseException     – other errors
     */
    public function lookup(string $ip, array $options = []): IpGeoResponse
    {
        $params = $this->buildParams($options);
        return $this->get('/ip/' . rawurlencode($ip), $params, $options['format'] ?? 'json');
    }

    /**
     * Look up geolocation for the caller's own IP address.
     *
     * @param array         $options
     * @param string        $options['format']  json|xml|csv (default json)
     * @param string|null   $options['get']     Comma-separated fields filter
     * @return IpGeoResponse
     * @throws APIResponseException
     */
    public function me(array $options = []): IpGeoResponse
    {
        $params = $this->buildParams($options);
        return $this->get('/me', $params, $options['format'] ?? 'json');
    }

    /**
     * Batch-lookup multiple IP addresses.
     *
     * @param string[] $ips  List of IPv4 or IPv6 addresses.
     * @return IpGeoResponse  Response with data.responseArray of IpGeoResponse.
     * @throws APIResponseException
     */
    public function bulk(array $ips): IpGeoResponse
    {
        if (empty($ips)) {
            throw new APIResponseException('ips must not be empty');
        }
        $bulkParam = implode(',', $ips);
        $params = ['apiKey' => $this->apiKey];
        return $this->get('/bulk/' . rawurlencode($bulkParam), $params, 'json');
    }

    // ── internal ────────────────────────────────────────────────────

    /**
     * Build query params for lookup/me endpoints.
     */
    private function buildParams(array $options): array
    {
        $params = ['apiKey' => $this->apiKey];
        $format = $options['format'] ?? 'json';
        if ($format !== 'json') {
            $params['format'] = $format;
        }
        if (!empty($options['get'])) {
            $params['get'] = $options['get'];
        }
        return $params;
    }

    /**
     * Execute GET request and parse response.
     */
    private function get(string $path, array $params, string $format): IpGeoResponse
    {
        try {
            $response = $this->http->request('GET', $path, ['query' => $params]);
        } catch (GuzzleException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $msg = $this->errorMessage($e->getResponse()->getBody()->getContents());
                if ($statusCode === 404) {
                    throw new InvalidIPException($msg, $statusCode, $e);
                }
                if ($statusCode === 429) {
                    throw new RateLimitException($msg, $statusCode, $e);
                }
                throw new APIResponseException("HTTP {$statusCode}: {$msg}", $statusCode, $e);
            }
            throw new APIResponseException("Request failed: {$e->getMessage()}", 0, $e);
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        // Rate limit
        if ($statusCode === 429) {
            throw new RateLimitException($this->errorMessage($body), $statusCode);
        }

        // Not found
        if ($statusCode === 404) {
            throw new InvalidIPException($this->errorMessage($body), $statusCode);
        }

        // Other HTTP errors
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new APIResponseException("HTTP {$statusCode}: {$this->errorMessage($body)}", $statusCode);
        }

        // Non-JSON formats: wrap raw text
        if ($format !== 'json') {
            return new IpGeoResponse([
                'success' => true,
                'data'    => ['ip' => $body],
            ]);
        }

        // JSON success path
        $payload = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new APIResponseException('Invalid JSON response');
        }

        // Logical error in success=false
        if (isset($payload['success']) && $payload['success'] === false) {
            throw new APIResponseException($payload['message'] ?? 'API returned success=false', $statusCode);
        }

        return IpGeoResponse::fromArray($payload);
    }

    /**
     * Best-effort extraction of error message.
     */
    private function errorMessage(string $body): string
    {
        $decoded = json_decode($body, true);
        if (is_array($decoded) && isset($decoded['message'])) {
            return $decoded['message'];
        }
        return $body ?: 'Unknown API error';
    }
}
