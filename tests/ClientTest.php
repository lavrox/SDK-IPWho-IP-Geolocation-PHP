<?php

use GuzzleHttp\Client as HttpClient;
use IpWho\SDK\Client;
use IpWho\SDK\Exception\APIResponseException;
use IpWho\SDK\Exception\InvalidIPException;
use IpWho\SDK\Exception\RateLimitException;
use PHPUnit\Framework\TestCase;

/**
 * Runs against a local stand-in server that replays recorded API responses.
 * No API key and no network access are needed.
 */
final class ClientTest extends TestCase
{
    private const KEY = 'sk.test_secret_value';

    private static $server;
    private static $dir;
    private static $url;

    public static function setUpBeforeClass(): void
    {
        self::$dir = sys_get_temp_dir() . '/ipwho-stub-' . bin2hex(random_bytes(6));
        mkdir(self::$dir);
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        self::$url = 'http://' . $address;
        self::$server = proc_open(
            [PHP_BINARY, '-S', $address, __DIR__ . '/server.php'],
            [1 => ['file', self::$dir . '/server.out', 'w'], 2 => ['file', self::$dir . '/server.err', 'w']],
            $pipes,
            null,
            array_merge(getenv(), ['IPWHO_STUB_DIR' => self::$dir])
        );
        [$host, $port] = explode(':', $address);
        for ($i = 0; $i < 100; $i++) {
            $probe = @fsockopen($host, (int) $port);
            if ($probe) {
                fclose($probe);
                return;
            }
            usleep(50000);
        }
        self::fail('The stand-in server did not start.');
    }

    public static function tearDownAfterClass(): void
    {
        proc_terminate(self::$server);
        proc_close(self::$server);
        array_map('unlink', glob(self::$dir . '/*'));
        rmdir(self::$dir);
    }

    protected function setUp(): void
    {
        @unlink(self::$dir . '/requests.log');
        $this->reply(200, $this->fixture('lookup'));
    }

    private function fixture(string $name): string
    {
        return file_get_contents(__DIR__ . "/fixtures/{$name}.json");
    }

    private function reply(int $status, string $body): void
    {
        file_put_contents(self::$dir . '/reply.json', json_encode(['status' => $status, 'body' => $body]));
    }

    private function requests(): array
    {
        $lines = file(self::$dir . '/requests.log', FILE_IGNORE_NEW_LINES);
        return array_map(static function (string $line): array {
            return json_decode($line, true);
        }, $lines);
    }

    private function client(): Client
    {
        return new Client(self::KEY, ['baseUrl' => self::$url]);
    }

    public function testKeyIsInTheHeaderAndNeverInTheUrl(): void
    {
        $this->client()->lookup('8.8.8.8');
        $this->client()->me();
        $this->reply(200, $this->fixture('bulk'));
        $this->client()->bulk(['8.8.8.8', '1.1.1.1']);

        $requests = $this->requests();
        $this->assertCount(3, $requests);
        foreach ($requests as $request) {
            $this->assertSame('Bearer ' . self::KEY, $request['authorization']);
            $this->assertSame('ipwho-php-sdk/1.0.0', $request['userAgent']);
            $this->assertStringNotContainsString(self::KEY, $request['uri']);
            $this->assertStringNotContainsStringIgnoringCase('apikey', $request['uri']);
        }
    }

    public function testKeyIsSentWithAnInjectedHttpClient(): void
    {
        $http = new HttpClient(['base_uri' => self::$url]);
        (new Client(self::KEY, [], $http))->lookup('8.8.8.8');
        $this->assertSame('Bearer ' . self::KEY, $this->requests()[0]['authorization']);
    }

    public function testLookupMapsTheResponse(): void
    {
        $data = $this->client()->lookup('8.8.8.8')->data;
        $this->assertSame('/ip/8.8.8.8', $this->requests()[0]['uri']);
        $this->assertSame('8.8.8.8', $data->ip);
        $this->assertSame('United States', $data->geoLocation->country);
        $this->assertSame('US', $data->geoLocation->countryCode);
        $this->assertSame('+1', $data->geoLocation->dialCode);
        $this->assertSame(15169, $data->connection->asnNumber);
        $this->assertSame('America/Chicago', $data->timezone->timeZone);
        $this->assertSame('USD', $data->currency->code);
    }

    public function testLookupAcceptsANullCurrency(): void
    {
        $this->reply(200, $this->fixture('lookup-null-currency'));
        $data = $this->client()->lookup('1.1.1.1')->data;
        $this->assertSame('1.1.1.1', $data->ip);
        $this->assertNull($data->currency);
    }

    public function testBulkReturnsTheDataForEveryAddress(): void
    {
        $this->reply(200, $this->fixture('bulk'));
        $response = $this->client()->bulk(['8.8.8.8', '1.1.1.1', '192.168.0.1', '9.9.9.9']);
        $rows = $response->data->responseArray;
        $this->assertCount(4, $rows);

        $this->assertTrue($rows[0]->success);
        $this->assertSame('8.8.8.8', $rows[0]->data->ip);
        $this->assertSame('United States', $rows[0]->data->geoLocation->country);
        $this->assertSame(15169, $rows[0]->data->connection->asnNumber);

        $this->assertTrue($rows[1]->success);
        $this->assertSame('1.1.1.1', $rows[1]->data->ip);
        $this->assertNull($rows[1]->data->currency);

        $this->assertFalse($rows[2]->success);
        $this->assertSame('192.168.0.1', $rows[2]->data->ip);
        $this->assertNull($rows[2]->data->geoLocation);
        $this->assertSame('Reserved range/Invalid IP address', $rows[2]->message);

        $this->assertFalse($rows[3]->success);
        $this->assertSame('9.9.9.9', $rows[3]->data->ip);
        $this->assertStringContainsString('quota', $rows[3]->message);

        $array = $response->toArray();
        $this->assertSame('8.8.8.8', $array['data']['responseArray'][0]['data']['ip']);
    }

    public function testFieldsAndFormatAreTheOnlyQueryParameters(): void
    {
        $this->client()->lookup('8.8.8.8', ['get' => 'geoLocation,timezone']);
        $this->assertSame('/ip/8.8.8.8?get=geoLocation%2Ctimezone', $this->requests()[0]['uri']);
        $this->reply(200, 'ip,8.8.8.8');
        $response = $this->client()->lookup('8.8.8.8', ['format' => 'csv']);
        $this->assertSame('ip,8.8.8.8', $response->data->ip);
        $this->assertSame('/ip/8.8.8.8?format=csv', $this->requests()[1]['uri']);
    }

    public function httpErrors(): array
    {
        return [
            [404, '{"success":false,"message":"Reserved range/Invalid IP address"}', InvalidIPException::class],
            [429, '{"error":"rate_limit_exceeded"}', RateLimitException::class],
            [401, '{"error":"invalid_api_key"}', APIResponseException::class],
        ];
    }

    /** @dataProvider httpErrors */
    public function testHttpErrors(int $status, string $body, string $expected): void
    {
        $this->reply($status, $body);
        try {
            $this->client()->lookup('8.8.8.8');
            $this->fail('Expected an exception.');
        } catch (\Exception $e) {
            $this->assertInstanceOf($expected, $e);
            $this->assertStringNotContainsString(self::KEY, $e->getMessage());
        }
    }

    public function testConnectionFailureThrowsAnSdkExceptionWithoutTheKey(): void
    {
        $client = new Client(self::KEY, ['baseUrl' => 'http://127.0.0.1:1', 'timeout' => 5]);
        try {
            $client->lookup('8.8.8.8');
            $this->fail('Expected an exception.');
        } catch (APIResponseException $e) {
            $this->assertStringStartsWith('Request failed:', $e->getMessage());
            $this->assertStringNotContainsString(self::KEY, $e->getMessage());
        }
    }
}
