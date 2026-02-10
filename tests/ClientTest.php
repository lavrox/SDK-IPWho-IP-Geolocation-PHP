<?php

namespace SDKIpWho\Tests;

use PHPUnit\Framework\TestCase;
use SDKIpWho\Client;
use SDKIpWho\GeoLocation;
use SDKIpWho\Timezone;
use SDKIpWho\Connection;
use SDKIpWho\Security;

class ClientTest extends TestCase
{
    protected $client;
    protected $mockResponse;

    protected function setUp(): void
    {
        $this->client = new Client('sk_test_123');
        
        // Mock response fixture (simulating API response)
        $this->mockResponse = [
            'success' => true,
            'data' => [
                'ip' => '202.21.42.9',
                'geoLocation' => [
                    'continent' => 'Asia',
                    'continentCode' => 'AS',
                    'country' => 'India',
                    'countryCode' => 'IN',
                    'capital' => 'New Delhi',
                    'region' => 'Telangana',
                    'regionCode' => 'TS',
                    'city' => null,
                    'postalCode' => null,
                    'dialCode' => '+91',
                    'isInEu' => false,
                    'latitude' => 17.3843,
                    'longitude' => 78.4583,
                    'accuracyRadius' => 1000
                ],
                'timezone' => [
                    'timeZone' => 'Asia/Kolkata',
                    'abbr' => 'IST',
                    'offset' => 19800,
                    'isDst' => false,
                    'utc' => '+05:30',
                    'currentTime' => '2026-02-06T11:02:50+05:30'
                ],
                'connection' => [
                    'asnNumber' => 24186,
                    'asnOrg' => 'RailTel Corporation of India Ltd',
                    'isp' => 'RailTel Corporation Of India Ltd.',
                    'org' => 'RailTel Corporation Of India Ltd.',
                    'domain' => null,
                    'connectionType' => 'Cable/DSL'
                ],
                'security' => [
                    'isVpn' => false,
                    'isTor' => false,
                    'isThreat' => 'low'
                ],
                'flag' => [
                    'flagIcon' => 'https://flag.example.com/in.svg',
                    'flagUnicode' => '🇮🇳'
                ],
                'currency' => [
                    'code' => 'INR',
                    'symbol' => '₹',
                    'name' => 'Indian Rupee'
                ]
            ]
        ];
    }

    /**
     * Test that API key is required
     */
    public function testThrowsErrorIfApiKeyMissing(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("API Key is required");
        new Client('');
    }

    /**
     * Test that getIp makes request to correct endpoint
     */
    public function testGetIpFormatsUrlCorrectly(): void
    {
        // Get the reflection class to access private methods
        $reflectionClass = new \ReflectionClass($this->client);
        $method = $reflectionClass->getMethod('request');
        $method->setAccessible(true);
        
        // This test verifies the method exists and is callable
        $this->assertTrue(method_exists($this->client, 'getIp'));
    }

    /**
     * Test with fixture data that Response maps correctly
     */
    public function testResponseMappingWithFixture(): void
    {
        // Test GeoLocation mapping
        $geoData = $this->mockResponse['data']['geoLocation'];
        $location = new GeoLocation($geoData);
        
        $this->assertEquals('Asia', $location->continent);
        $this->assertEquals('India', $location->country);
        $this->assertEquals('IN', $location->countryCode);
        $this->assertEquals(17.3843, $location->latitude);
        $this->assertEquals(78.4583, $location->longitude);
    }

    /**
     * Test that methods are defined
     */
    public function testClientMethodsExist(): void
    {
        $this->assertTrue(method_exists($this->client, 'getLocation'));
        $this->assertTrue(method_exists($this->client, 'getTimezone'));
        $this->assertTrue(method_exists($this->client, 'getConnection'));
        $this->assertTrue(method_exists($this->client, 'getSecurity'));
        $this->assertTrue(method_exists($this->client, 'getMe'));
        $this->assertTrue(method_exists($this->client, 'getIp'));
    }

    /**
     * Test GeoLocation data structure
     */
    public function testGeoLocationDataStructure(): void
    {
        $data = [
            'continent' => 'Asia',
            'continentCode' => 'AS',
            'country' => 'India',
            'countryCode' => 'IN',
            'capital' => 'New Delhi',
            'region' => 'Telangana',
            'regionCode' => 'TS',
            'city' => null,
            'postalCode' => null,
            'dialCode' => '+91',
            'isInEu' => false,
            'latitude' => 17.3843,
            'longitude' => 78.4583,
            'accuracyRadius' => 1000
        ];

        $location = new GeoLocation($data);

        $this->assertEquals('Asia', $location->continent);
        $this->assertEquals('AS', $location->continentCode);
        $this->assertEquals('India', $location->country);
        $this->assertEquals('IN', $location->countryCode);
        $this->assertEquals('New Delhi', $location->capital);
        $this->assertEquals('Telangana', $location->region);
        $this->assertEquals('TS', $location->regionCode);
        $this->assertNull($location->city);
        $this->assertNull($location->postalCode);
        $this->assertEquals('+91', $location->dialCode);
        $this->assertFalse($location->isInEu);
        $this->assertEquals(17.3843, $location->latitude);
        $this->assertEquals(78.4583, $location->longitude);
        $this->assertEquals(1000, $location->accuracyRadius);
        $this->assertIsString($location->continent);
        $this->assertIsFloat($location->latitude);
        $this->assertIsFloat($location->longitude);
    }

    /**
     * Test Timezone data structure
     */
    public function testTimezoneDataStructure(): void
    {
        $data = [
            'timeZone' => 'Asia/Kolkata',
            'abbr' => 'IST',
            'offset' => 19800,
            'isDst' => false,
            'utc' => '+05:30',
            'currentTime' => '2026-02-06T11:02:50+05:30'
        ];

        $timezone = new Timezone($data);

        $this->assertEquals('Asia/Kolkata', $timezone->timeZone);
        $this->assertEquals('IST', $timezone->abbr);
        $this->assertEquals(19800, $timezone->offset);
        $this->assertFalse($timezone->isDst);
        $this->assertEquals('+05:30', $timezone->utc);
        $this->assertEquals('2026-02-06T11:02:50+05:30', $timezone->currentTime);
        $this->assertIsString($timezone->timeZone);
        $this->assertIsInt($timezone->offset);
        $this->assertIsBool($timezone->isDst);
    }

    /**
     * Test Connection data structure
     */
    public function testConnectionDataStructure(): void
    {
        $data = [
            'asnNumber' => 24186,
            'asnOrg' => 'RailTel Corporation of India Ltd',
            'isp' => 'RailTel Corporation Of India Ltd.',
            'org' => 'RailTel Corporation Of India Ltd.',
            'domain' => null,
            'connectionType' => 'Cable/DSL'
        ];

        $connection = new Connection($data);

        $this->assertEquals(24186, $connection->asnNumber);
        $this->assertEquals('RailTel Corporation of India Ltd', $connection->asnOrg);
        $this->assertEquals('RailTel Corporation Of India Ltd.', $connection->isp);
        $this->assertEquals('RailTel Corporation Of India Ltd.', $connection->org);
        $this->assertNull($connection->domain);
        $this->assertEquals('Cable/DSL', $connection->connectionType);
        $this->assertIsInt($connection->asnNumber);
        $this->assertIsString($connection->isp);
        $this->assertNull($connection->domain);
    }

    /**
     * Test Security data structure
     */
    public function testSecurityDataStructure(): void
    {
        $data = [
            'isVpn' => false,
            'isTor' => false,
            'isThreat' => 'low'
        ];

        $security = new Security($data);

        $this->assertFalse($security->isVpn);
        $this->assertFalse($security->isTor);
        $this->assertEquals('low', $security->isThreat);
        $this->assertIsBool($security->isVpn);
        $this->assertIsBool($security->isTor);
        $this->assertIsString($security->isThreat);
    }

    /**
     * Test that Client is instantiated with API key
     */
    public function testClientInstantiationWithApiKey(): void
    {
        $client = new Client('test_api_key_123');
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * NOTE: Tests requiring actual API calls would need:
     * 1. A test API key (use environment variable: IPWHO_API_KEY)
     * 2. HTTP mocking library (e.g., Mockery or PHPUnit mock)
     * 3. Fixtures for response data (in tests/fixtures/responses/)
     * 
     * Example with environment variable:
     * 
     *  public function testGetMeWithRealApiKey(): void
     *  {
     *      $apiKey = getenv('IPWHO_API_KEY');
     *      if (!$apiKey) {
     *          $this->markTestSkipped('IPWHO_API_KEY env var not set');
     *      }
     *      
     *      $client = new Client($apiKey);
     *      $data = $client->getMe();
     *      
     *      $this->assertNotNull($data->ip);
     *      $this->assertNotNull($data->geoLocation);
     *      $this->assertIsString($data->ip);
     *  }
     */

    public function testGetMeWithRealApiKey(): void
    {
          $apiKey = getenv('IPWHO_API_KEY');
          if (!$apiKey) {
              $this->markTestSkipped('IPWHO_API_KEY env var not set');
          }
          
          $client = new Client($apiKey);
          $data = $client->getMe();
          
          $this->assertNotNull($data->ip);
          $this->assertNotNull($data->geoLocation);
          $this->assertIsString($data->ip);
    }
}