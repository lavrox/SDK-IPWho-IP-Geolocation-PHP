# IPWho PHP SDK

The official PHP SDK for the [IPWho IP Geolocation API by ipwho.org](https://ipwho.org/) - Get detailed IP geolocation, timezone, connection, and security information with full type safety.

## Installation

Install via Composer:

```bash
composer install
```

Or add to your `composer.json`:

```json
{
  "require": {
    "ipwho/sdk-ipwho-php": "^1.0"
  }
}
```

Then run:

```bash
composer install
```

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use SDKIpWho\Client;

$client = new Client('your-api-key');

// Get caller's location (uses your IP by default)
$location = $client->getLocation();
print_r($location);

// Get location for a specific IP
$specificLocation = $client->getLocation('8.8.8.8');
print_r($specificLocation);
```

## Authentication

Initialize the client with your API key:

```php
$client = new Client('your-api-key');
```

Get your API key by signing up [here](https://www.ipwho.org/signup).

The SDK automatically includes your API key in the `X-API-Key` header for all requests. Missing API keys will throw an error immediately.

## Methods

All methods accept an optional `ip` parameter. When omitted, the SDK uses the caller's IP address (calls `/me` endpoint). When provided, it queries the specific IP (calls `/ip/{ip}` endpoint).

### `getLocation(?string $ip = null): ?GeoLocation`

Get geographical location data for an IP address.

**Parameters:**
- `$ip` (optional) - IP address to query. Defaults to caller's IP if omitted.

**Returns:** `GeoLocation` object or `null` if unavailable.

**Example:**
```php
// Get your own location
$myLocation = $client->getLocation();

// Get location for specific IP
$location = $client->getLocation('1.1.1.1');

print_r($location);
// GeoLocation {
//   continent: "Oceania",
//   continentCode: "OC",
//   country: "Australia",
//   countryCode: "AU",
//   capital: "Canberra",
//   region: "Queensland",
//   regionCode: "QLD",
//   city: "South Brisbane",
//   postalCode: "4101",
//   dialCode: "+61",
//   isInEu: false,
//   latitude: -27.4766,
//   longitude: 153.0166,
//   accuracyRadius: 500
// }
```

### `getTimezone(?string $ip = null): ?Timezone`

Get timezone information for an IP address.

**Parameters:**
- `$ip` (optional) - IP address to query. Defaults to caller's IP if omitted.

**Returns:** `Timezone` object or `null` if unavailable.

**Example:**
```php
// Get your own timezone
$myTimezone = $client->getTimezone();

// Get timezone for specific IP
$timezone = $client->getTimezone('8.8.8.8');

print_r($timezone);
// Timezone {
//   timeZone: "America/Los_Angeles",
//   abbr: "PST",
//   offset: -28800,
//   isDst: false,
//   utc: "-08:00",
//   currentTime: "2026-02-07T10:30:00-08:00"
// }
```

### `getConnection(?string $ip = null): ?Connection`

Get ISP and network connection details for an IP address.

**Parameters:**
- `$ip` (optional) - IP address to query. Defaults to caller's IP if omitted.

**Returns:** `Connection` object or `null` if unavailable.

**Example:**
```php
// Get your own connection details
$myConnection = $client->getConnection();

// Get connection details for specific IP
$connection = $client->getConnection('1.1.1.1');

print_r($connection);
// Connection {
//   asnNumber: 13335,
//   asnOrg: "Cloudflare, Inc.",
//   isp: "Cloudflare",
//   org: "APNIC Research and Development",
//   domain: "cloudflare.com",
//   connectionType: "Data Center/Web Hosting/Transit"
// }
```

### `getSecurity(?string $ip = null): ?Security`

Get security information including VPN, Tor, and threat detection.

**Parameters:**
- `$ip` (optional) - IP address to query. Defaults to caller's IP if omitted.

**Returns:** `Security` object or `null` if unavailable.

**Example:**
```php
// Check your own IP security status
$mySecurity = $client->getSecurity();

// Check security for specific IP
$security = $client->getSecurity('8.8.8.8');

print_r($security);
// Security {
//   isVpn: false,
//   isTor: false,
//   isThreat: "low"
// }
```

### `getMe(): IPWhoData`

Get complete IP information for the caller's IP address.

**Returns:** Full `IPWhoData` object with all available information.

**Example:**
```php
$myData = $client->getMe();

print_r($myData);
// IPWhoData {
//   ip: "203.0.113.1",
//   geoLocation: { ... },
//   timezone: { ... },
//   flag: { ... },
//   currency: { ... },
//   connection: { ... },
//   security: { ... },
//   userAgent: { ... }
// }
```

### `getIp(string $ip): IPWhoData`

Get complete IP information for a specific IP address.

**Parameters:**
- `$ip` (required) - IP address to query.

**Returns:** Full `IPWhoData` object with all available information.

**Example:**
```php
$data = $client->getIp('8.8.8.8');

print_r($data);
// IPWhoData {
//   ip: "8.8.8.8",
//   geoLocation: { ... },
//   timezone: { ... },
//   flag: { ... },
//   currency: { ... },
//   connection: { ... },
//   security: { ... }
// }
```

## Type Classes

The SDK provides full type classes for all responses:

### `GeoLocation`
```php
{
    continent: string;           // Continent name
    continentCode: string;       // Continent code
    country: string;             // Country name
    countryCode: string;         // ISO 3166-1 alpha-2 country code
    capital: ?string;            // Capital city name
    region: ?string;             // Region name
    regionCode: ?string;         // Region code
    city: ?string;               // City name
    postalCode: ?string;         // Postal code
    dialCode: ?string;           // International dial code
    isInEu: ?bool;               // Is in European Union
    latitude: ?float;            // Latitude coordinate
    longitude: ?float;           // Longitude coordinate
    accuracyRadius: ?int;        // Accuracy radius in kilometers
}
```

### `Timezone`
```php
{
    timeZone: string;            // IANA timezone identifier
    abbr: ?string;               // Timezone abbreviation
    offset: ?int;                // UTC offset in seconds
    isDst: ?bool;                // Is in daylight saving time
    utc: ?string;                // UTC offset string
    currentTime: ?string;        // Current time in ISO 8601 format
}
```

### `Connection`
```php
{
    asnNumber: ?int;                // Autonomous System Number
    asnOrg: ?string;                // ASN organization name
    isp: ?string;                   // Internet Service Provider
    org: ?string;                   // Organization name
    domain: ?string;                // Domain name
    connectionType: ?string;        // Connection type description
}
```

### `Security`
```php
{
    isVpn: ?bool;              // Whether IP is using VPN
    isTor: ?bool;              // Whether IP is Tor exit node
    isThreat: ?string;         // Threat level: "low", "medium", "high"
}
```

### `Currency`
```php
{
    code: string;              // Currency code (ISO 4217)
    symbol: string;            // Currency symbol
    name: string;              // Currency name
    namePlural: ?string;       // Plural form of currency name
    hexUnicode: ?string;       // Hex unicode for symbol
}
```

### `Flag`
```php
{
    flagIcon: string;          // URL to flag icon
    flagUnicode: string;       // Unicode flag emoji
}
```

### `UserAgent`
```php
{
    browser: BrowserInfo;      // Browser information
    engine: EngineInfo;        // Engine information
    os: OSInfo;                // Operating system information
    device: DeviceInfo;        // Device information
    cpu: CPUInfo;              // CPU information
}
```

### `IPWhoData`
Full response object containing all available data:
```php
{
    ip: string;                // IP address
    geoLocation: ?GeoLocation; // Geolocation information
    timezone: ?Timezone;       // Timezone information
    flag: ?Flag;               // Flag information
    currency: ?Currency;       // Currency information
    connection: ?Connection;   // Connection information
    userAgent: ?UserAgent;     // User agent information (if available)
    security: ?Security;       // Security information
}
```

## Error Handling

The SDK throws exceptions for:
- Missing API key during initialization
- Failed API requests
- Invalid API responses

**Example:**
```php
try {
    $client = new Client('your-api-key');
    $location = $client->getLocation('8.8.8.8');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

For issues, feature requests, or questions, please visit:
- [GitHub Issues](https://github.com/lavrox/SDK-IPWho-PHP/issues)
- [IPWho Documentation](https://ipwho.org/docs)