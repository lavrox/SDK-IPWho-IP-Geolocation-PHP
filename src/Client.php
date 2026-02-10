<?php

namespace SDKIpWho;

use SDKIpWho\{
    IPWhoData,
    GeoLocation,
    Timezone,
    Connection,
    Security
};

class Client
{
    private string $apiKey;
    private string $baseUrl = 'https://api.ipwho.org/v1';

    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new \Exception("API Key is required");
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Make HTTP request to the API
     */
    private function fetcher(string $endpoint): array
    {
        $separator = strpos($endpoint, '?') !== false ? '&' : '?';
        $url = $this->baseUrl . $endpoint . $separator;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "X-API-Key: {$this->apiKey}\r\n",
                'timeout' => 30
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \Exception("Error fetching data from API");
        }

        $body = json_decode($response, true);

        if (!isset($body['success']) || !$body['success']) {
            throw new \Exception($body['message'] ?? "Request failed");
        }

        return $body['data'] ?? [];
    }

    /**
     * Internal request method
     */
    private function request(?string $ip = null): IPWhoData
    {
        $endpoint = $ip ? "/ip/{$ip}" : "/me";
        $data = $this->fetcher($endpoint);
        return new IPWhoData($data);
    }

    /**
     * Get geolocation data for an IP address
     */
    public function getLocation(?string $ip = null): ?GeoLocation
    {
        $data = $this->request($ip);

        if ($data->geoLocation === null) {
            return null;
        }

        return $data->geoLocation;
    }

    /**
     * Get timezone data for an IP address
     */
    public function getTimezone(?string $ip = null): ?Timezone
    {
        $data = $this->request($ip);

        if ($data->timezone === null) {
            return null;
        }

        return $data->timezone;
    }

    /**
     * Get connection data for an IP address
     */
    public function getConnection(?string $ip = null): ?Connection
    {
        $data = $this->request($ip);

        if ($data->connection === null) {
            return null;
        }

        return $data->connection;
    }

    /**
     * Get security data for an IP address
     */
    public function getSecurity(?string $ip = null): ?Security
    {
        $data = $this->request($ip);

        if ($data->security === null) {
            return null;
        }

        return $data->security;
    }

    /**
     * Get all data for a specific IP address
     */
    public function getIp(string $ip): IPWhoData
    {
        return $this->request($ip);
    }

    /**
     * Get all data for the current IP address (caller's IP)
     */
    public function getMe(): IPWhoData
    {
        return $this->request();
    }
}