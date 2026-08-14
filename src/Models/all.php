<?php

namespace IpWho\SDK\Models;

class GeoLocation
{
    /** @var string|null */
    public $continent;
    /** @var string|null */
    public $continentCode;
    /** @var string|null */
    public $country;
    /** @var string|null */
    public $countryCode;
    /** @var string|null */
    public $capital;
    /** @var string|null */
    public $region;
    /** @var string|null */
    public $regionCode;
    /** @var string|null */
    public $city;
    /** @var string|null */
    public $postalCode;
    /** @var string|null */
    public $dialCode;
    /** @var bool|null */
    public $isInEu;
    /** @var float|null */
    public $latitude;
    /** @var float|null */
    public $longitude;
    /** @var float|null */
    public $accuracyRadius;

    public function __construct(array $data = [])
    {
        $this->continent      = $data['continent'] ?? null;
        $this->continentCode  = $data['continentCode'] ?? null;
        $this->country        = $data['country'] ?? null;
        $this->countryCode    = $data['countryCode'] ?? null;
        $this->capital        = $data['capital'] ?? null;
        $this->region         = $data['region'] ?? null;
        $this->regionCode     = $data['regionCode'] ?? null;
        $this->city           = $data['city'] ?? null;
        $this->postalCode     = $data['postal_Code'] ?? null;
        $this->dialCode       = $data['dial_code'] ?? null;
        $this->isInEu         = isset($data['is_in_eu']) ? (bool) $data['is_in_eu'] : null;
        $this->latitude       = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $this->longitude      = isset($data['longitude']) ? (float) $data['longitude'] : null;
        $this->accuracyRadius = isset($data['accuracy_radius']) ? (float) $data['accuracy_radius'] : null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class Timezone
{
    /** @var string|null */
    public $timeZone;
    /** @var string|null */
    public $abbr;
    /** @var float|null */
    public $offset;
    /** @var bool|null */
    public $isDst;
    /** @var string|null */
    public $utc;
    /** @var string|null */
    public $currentTime;

    public function __construct(array $data = [])
    {
        $this->timeZone    = $data['time_zone'] ?? null;
        $this->abbr        = $data['abbr'] ?? null;
        $this->offset      = isset($data['offset']) ? (float) $data['offset'] : null;
        $this->isDst       = isset($data['is_dst']) ? (bool) $data['is_dst'] : null;
        $this->utc         = $data['utc'] ?? null;
        $this->currentTime = $data['current_time'] ?? null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class Flag
{
    /** @var string|null */
    public $flagIcon;
    /** @var string|null */
    public $flagUnicode;

    public function __construct(array $data = [])
    {
        $this->flagIcon    = $data['flag_Icon'] ?? null;
        $this->flagUnicode = $data['flag_unicode'] ?? null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class Currency
{
    /** @var string */
    public $code = '';
    /** @var string */
    public $symbol = '';
    /** @var string */
    public $name = '';
    /** @var string */
    public $namePlural = '';
    /** @var string */
    public $hexUnicode = '';

    public function __construct(array $data = [])
    {
        $this->code       = (string) ($data['code'] ?? '');
        $this->symbol     = (string) ($data['symbol'] ?? '');
        $this->name       = (string) ($data['name'] ?? '');
        $this->namePlural = (string) ($data['name_plural'] ?? '');
        $this->hexUnicode = (string) ($data['hex_unicode'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class Connection
{
    /** @var int|null */
    public $asnNumber;
    /** @var string|null */
    public $asnOrg;
    /** @var string|null */
    public $isp;
    /** @var string|null */
    public $org;
    /** @var string|null */
    public $domain;
    /** @var string|null */
    public $connectionType;

    public function __construct(array $data = [])
    {
        $this->asnNumber      = isset($data['asn_number']) ? (int) $data['asn_number'] : null;
        $this->asnOrg         = $data['asn_org'] ?? null;
        $this->isp            = $data['isp'] ?? null;
        $this->org            = $data['org'] ?? null;
        $this->domain         = $data['domain'] ?? null;
        $this->connectionType = $data['connection_type'] ?? null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class Security
{
    /** @var bool */
    public $isVpn = false;
    /** @var bool */
    public $isTor = false;
    /** @var string  (low|medium|high) */
    public $isThreat = 'low';

    public function __construct(array $data = [])
    {
        $this->isVpn    = (bool) ($data['isVpn'] ?? false);
        $this->isTor    = (bool) ($data['isTor'] ?? false);
        $this->isThreat = (string) ($data['isThreat'] ?? 'low');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgentBrowser
{
    /** @var string */
    public $name = '';
    /** @var string */
    public $version = '';

    public function __construct(array $data = [])
    {
        $this->name    = (string) ($data['name'] ?? '');
        $this->version = (string) ($data['version'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgentEngine
{
    /** @var string */
    public $name = '';
    /** @var string */
    public $version = '';

    public function __construct(array $data = [])
    {
        $this->name    = (string) ($data['name'] ?? '');
        $this->version = (string) ($data['version'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgentOS
{
    /** @var string */
    public $name = '';
    /** @var string */
    public $version = '';

    public function __construct(array $data = [])
    {
        $this->name    = (string) ($data['name'] ?? '');
        $this->version = (string) ($data['version'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgentDevice
{
    /** @var string */
    public $type = '';
    /** @var string */
    public $vendor = '';
    /** @var string */
    public $model = '';

    public function __construct(array $data = [])
    {
        $this->type   = (string) ($data['type'] ?? '');
        $this->vendor = (string) ($data['vendor'] ?? '');
        $this->model  = (string) ($data['model'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgentCPU
{
    /** @var string */
    public $architecture = '';

    public function __construct(array $data = [])
    {
        $this->architecture = (string) ($data['architecture'] ?? '');
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

class UserAgent
{
    /** @var UserAgentBrowser|null */
    public $browser;
    /** @var UserAgentEngine|null */
    public $engine;
    /** @var UserAgentOS|null */
    public $os;
    /** @var UserAgentDevice|null */
    public $device;
    /** @var UserAgentCPU|null */
    public $cpu;

    public function __construct(array $data = [])
    {
        $this->browser = isset($data['browser']) ? new UserAgentBrowser($data['browser']) : null;
        $this->engine  = isset($data['engine']) ? new UserAgentEngine($data['engine']) : null;
        $this->os      = isset($data['os']) ? new UserAgentOS($data['os']) : null;
        $this->device  = isset($data['device']) ? new UserAgentDevice($data['device']) : null;
        $this->cpu     = isset($data['cpu']) ? new UserAgentCPU($data['cpu']) : null;
    }

    public function toArray(): array
    {
        return [
            'browser' => $this->browser ? $this->browser->toArray() : null,
            'engine'  => $this->engine ? $this->engine->toArray() : null,
            'os'      => $this->os ? $this->os->toArray() : null,
            'device'  => $this->device ? $this->device->toArray() : null,
            'cpu'     => $this->cpu ? $this->cpu->toArray() : null,
        ];
    }
}

class GeoData
{
    /** @var string */
    public $ip = '';
    /** @var GeoLocation|null */
    public $geoLocation;
    /** @var Timezone|null */
    public $timezone;
    /** @var Flag|null */
    public $flag;
    /** @var Currency|null */
    public $currency;
    /** @var Connection|null */
    public $connection;
    /** @var Security|null */
    public $security;
    /** @var UserAgent|null */
    public $userAgent;
    /** @var IpGeoResponse[]|null  Populated only for bulk responses. */
    public $responseArray;

    public function __construct(array $data = [])
    {
        $this->ip         = (string) ($data['ip'] ?? '');
        $this->geoLocation = isset($data['geoLocation']) ? new GeoLocation($data['geoLocation']) : null;
        $this->timezone   = isset($data['timezone']) ? new Timezone($data['timezone']) : null;
        $this->flag       = isset($data['flag']) ? new Flag($data['flag']) : null;
        $this->currency   = isset($data['currency']) ? new Currency($data['currency']) : null;
        $this->connection = isset($data['connection']) ? new Connection($data['connection']) : null;
        $this->security   = isset($data['security']) ? new Security($data['security']) : null;
        $this->userAgent  = isset($data['userAgent']) ? new UserAgent($data['userAgent']) : null;
        // Bulk responses carry an array of already-wrapped IpGeoResponse objects.
        $this->responseArray = isset($data['responseArray']) && is_array($data['responseArray'])
            ? $data['responseArray']
            : null;
    }

    public function toArray(): array
    {
        return [
            'ip'          => $this->ip,
            'geoLocation' => $this->geoLocation ? $this->geoLocation->toArray() : null,
            'timezone'    => $this->timezone ? $this->timezone->toArray() : null,
            'flag'        => $this->flag ? $this->flag->toArray() : null,
            'currency'    => $this->currency ? $this->currency->toArray() : null,
            'connection'  => $this->connection ? $this->connection->toArray() : null,
            'security'    => $this->security ? $this->security->toArray() : null,
            'userAgent'   => $this->userAgent ? $this->userAgent->toArray() : null,
        ];
    }
}

class IpGeoResponse
{
    /** @var bool */
    public $success;
    /** @var GeoData|null */
    public $data;
    /** @var string|null */
    public $message;

    public function __construct(array $data = [])
    {
        $this->success = (bool) ($data['success'] ?? false);
        $this->data    = isset($data['data']) && is_array($data['data']) ? new GeoData($data['data']) : null;
        $this->message = $data['message'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data'    => $this->data ? $this->data->toArray() : null,
            'message' => $this->message,
        ];
    }

    /**
     * Create from raw API JSON.
     */
    public static function fromArray(array $data): self
    {
        // Bulk response: normalise responseArray
        if (isset($data['data']['responseArray']) && is_array($data['data']['responseArray'])) {
            $wrapped = [];
            foreach ($data['data']['responseArray'] as $item) {
                $wrapped[] = self::fromArray($item);
            }
            $data['data']['responseArray'] = $wrapped;
        }
        return new self($data);
    }
}

class ErrorResponse
{
    /** @var bool */
    public $success = false;
    /** @var string */
    public $message = '';

    public function __construct(array $data = [])
    {
        $this->success = (bool) ($data['success'] ?? false);
        $this->message = (string) ($data['message'] ?? '');
    }
}
