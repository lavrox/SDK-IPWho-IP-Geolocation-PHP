# IPWho ([ipwho.org](https://www.ipwho.org)) PHP SDK

[![packagist version](https://img.shields.io/packagist/v/ipwho/ipwho-ip-geolocation-api?style=flat-square)](https://packagist.org/packages/ipwho/ipwho-ip-geolocation-api) [![license](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/lavrox/SDK-IPWho-PHP/blob/main/LICENSE)

Official PHP client for the [IPWho](https://www.ipwho.org) IP Geolocation API. One call returns the **full** payload: geolocation, timezone, flag, currency, connection (ASN/ISP), security (VPN/Tor/threat), and user-agent when present.

- Product: [ipwho.org](https://www.ipwho.org)
- API docs: [ipwho.org/docs](https://www.ipwho.org/docs)
- Get an API key: [ipwho.org/free-plan](https://www.ipwho.org/free-plan) (free [Lavrox](https://lavrox.com) account)
- Live API host: `https://api.ipwho.org`

## API key

Open a free [Lavrox](https://lavrox.com) account to get an API key for [IPWho](https://www.ipwho.org). Create your key at [ipwho.org/free-plan](https://www.ipwho.org/free-plan) — no credit card required.

## Installation

```bash
composer require ipwho/ipwho-ip-geolocation-api
```

Requires PHP >= 7.4, `ext-json`, `guzzlehttp/guzzle` ^7.

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use IpWho\SDK\Client;

$client = new Client(getenv('IPWHO_API_KEY'));

$res  = $client->lookup('8.8.8.8');                 // GET /ip/{ip}
$me   = $client->me();                              // GET /me
$bulk = $client->bulk(['8.8.8.8', '1.1.1.1']);      // GET /bulk/{a,b,c}
```

Every successful JSON call returns `IpWho\SDK\Models\IpGeoResponse`:

```
IpGeoResponse
├── success
├── message
└── data  (GeoData)
    ├── ip
    ├── geoLocation
    ├── timezone
    ├── flag
    ├── currency
    ├── connection
    ├── security
    ├── userAgent
    └── responseArray   // bulk only
```

## Reading the full response (8.8.8.8)

Live [IPWho](https://www.ipwho.org) values: United States, ASN 15169, America/Chicago, dial code +1. Nested objects may be `null`.

```php
$res  = $client->lookup('8.8.8.8');
$data = $res->data;

echo $data->ip; // 8.8.8.8

$geo = $data->geoLocation;
echo $geo->continent, $geo->continentCode; // North America, NA
echo $geo->country, $geo->countryCode;     // United States, US
echo $geo->capital, $geo->region, $geo->regionCode, $geo->city;
echo $geo->postalCode, $geo->dialCode;     // +1
echo $geo->isInEu ? 'eu' : 'not-eu';
echo $geo->latitude, $geo->longitude, $geo->accuracyRadius;

$tz = $data->timezone;
echo $tz->timeZone; // America/Chicago
echo $tz->abbr, $tz->offset, $tz->isDst, $tz->utc, $tz->currentTime;

echo $data->flag->flagIcon;    // 🇺🇸
echo $data->flag->flagUnicode; // U+1F1FA U+1F1F8

echo $data->currency->code, $data->currency->symbol, $data->currency->name;
echo $data->currency->namePlural; // US dollars
echo $data->currency->hexUnicode;

$conn = $data->connection;
echo $conn->asnNumber;      // 15169
echo $conn->asnOrg;         // Google LLC
echo $conn->isp, $conn->org, $conn->domain;
echo $conn->connectionType; // Corporate

echo $data->security->isVpn, $data->security->isTor, $data->security->isThreat;

if ($data->userAgent) {
    echo $data->userAgent->browser->name, $data->userAgent->os->name;
}

$me = $client->me();
echo $me->data->ip;

$bulk = $client->bulk(['8.8.8.8', '1.1.1.1']);
foreach ($bulk->data->responseArray as $item) {
    echo $item->data->ip, $item->data->geoLocation->country;
}
```

### Example JSON (mapped fields)

```json
{
  "success": true,
  "data": {
    "ip": "8.8.8.8",
    "geoLocation": {
      "continent": "North America",
      "continentCode": "NA",
      "country": "United States",
      "countryCode": "US",
      "dialCode": "+1",
      "isInEu": false,
      "accuracyRadius": 1000
    },
    "timezone": { "timeZone": "America/Chicago" },
    "flag": { "flagIcon": "🇺🇸", "flagUnicode": "U+1F1FA U+1F1F8" },
    "currency": { "code": "USD", "namePlural": "US dollars" },
    "connection": {
      "asnNumber": 15169,
      "asnOrg": "Google LLC",
      "connectionType": "Corporate"
    },
    "security": { "isVpn": false, "isTor": false, "isThreat": "low" }
  }
}
```

## Migrating from v1

| v1 | v2 |
|----|----|
| `getIp` / `getLocation($ip)` | `lookup($ip)` then `$res->data->geoLocation` |
| `getMe` / `getLocation()` | `me()` |
| `getTimezone` / `getConnection` / `getSecurity` | nested on `$res->data` |
| *(missing)* | `bulk($ips)` |

Client is `IpWho\SDK\Client` (was `SDKIpWho\Client`).

## API Reference

### `new Client(string $apiKey, array $options = [], ?HttpClient $httpClient = null)`

Key is required (query `apiKey`). `$options['baseUrl']` default `https://api.ipwho.org`. `$options['timeout']` default `30`.

### `lookup(string $ip, array $options = []): IpGeoResponse`

`$options['format']`: json|xml|csv. `$options['get']`: field filter.

### `me(array $options = []): IpGeoResponse`

### `bulk(array $ips): IpGeoResponse`

Results on `$res->data->responseArray`.

### Errors

`InvalidIPException` (404), `RateLimitException` (429), `APIResponseException`.

## Type Definitions

Public properties:

- **GeoLocation**: `continent`, `continentCode`, `country`, `countryCode`, `capital`, `region`, `regionCode`, `city`, `postalCode`, `dialCode`, `isInEu`, `latitude`, `longitude`, `accuracyRadius`
- **Timezone**: `timeZone`, `abbr`, `offset`, `isDst`, `utc`, `currentTime`
- **Flag**: `flagIcon`, `flagUnicode`
- **Currency**: `code`, `symbol`, `name`, `namePlural`, `hexUnicode`
- **Connection**: `asnNumber`, `asnOrg`, `isp`, `org`, `domain`, `connectionType`
- **Security**: `isVpn`, `isTor`, `isThreat` (`low`|`medium`|`high`)
- **UserAgent**: `browser`, `engine`, `os`, `device`, `cpu` (each has `name`/`version` or `type`/`vendor`/`model` / `architecture`)
- **GeoData**: `ip` plus all of the above plus `responseArray`
- **IpGeoResponse**: `success`, `data`, `message`

Wire JSON mixes casings (`postal_Code`, `flag_Icon`, `isVpn`); the SDK maps them onto these properties.

## Troubleshooting

- Empty key → `APIResponseException`. Get a key at [ipwho.org](https://www.ipwho.org).
- HTTP 403: SDK sends `ipwho-php-sdk/1.0.0`.
- HTTP 429 / 404: `RateLimitException` / `InvalidIPException`.
- Null nested objects on some IPs (city, user-agent).

## Testing

```bash
IPWHO_API_KEY=your_key php test_ipwho.php
```

The live check is `test_ipwho.php`.

## Changelog

### v2.0.0

- `lookup` / `me` / `bulk` matching [api.ipwho.org](https://api.ipwho.org)
- Full `IpGeoResponse` instead of v1 getters

## License

MIT License — see [LICENSE](LICENSE).

## Support

- Documentation: [ipwho.org/docs](https://www.ipwho.org/docs)
- Contact: [ipwho.org/contact](https://www.ipwho.org/contact)
- GitHub Issues: [lavrox/SDK-IPWho-PHP](https://github.com/lavrox/SDK-IPWho-PHP/issues)
- Website: [ipwho.org](https://www.ipwho.org)

---

[IPWho](https://www.ipwho.org) — a [Lavrox](https://lavrox.com) network API.

[Lavrox](https://lavrox.com) — Independent API infrastructure. Lower latency, lower cost.
