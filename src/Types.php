<?php

namespace SDKIpWho;

class BrowserInfo
{
    public string $name;
    public ?string $version = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->version = $data['version'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['name', 'version']));
    }
}

class EngineInfo
{
    public string $name;
    public ?string $version = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->version = $data['version'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['name', 'version']));
    }
}

class OSInfo
{
    public string $name;
    public ?string $version = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->version = $data['version'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['name', 'version']));
    }
}

class DeviceInfo
{
    public ?string $type = null;
    public ?string $vendor = null;
    public ?string $model = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->type = $data['type'] ?? null;
        $this->vendor = $data['vendor'] ?? null;
        $this->model = $data['model'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['type', 'vendor', 'model']));
    }
}

class CPUInfo
{
    public ?string $architecture = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->architecture = $data['architecture'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['architecture']));
    }
}

class UserAgent
{
    public BrowserInfo $browser;
    public EngineInfo $engine;
    public OSInfo $os;
    public DeviceInfo $device;
    public CPUInfo $cpu;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->browser = new BrowserInfo($data['browser'] ?? []);
        $this->engine = new EngineInfo($data['engine'] ?? []);
        $this->os = new OSInfo($data['os'] ?? []);
        $this->device = new DeviceInfo($data['device'] ?? []);
        $this->cpu = new CPUInfo($data['cpu'] ?? []);
        $this->extra = array_diff_key($data, array_flip(['browser', 'engine', 'os', 'device', 'cpu']));
    }
}

class Flag
{
    public string $flagIcon;
    public string $flagUnicode;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->flagIcon = $data['flagIcon'] ?? '';
        $this->flagUnicode = $data['flagUnicode'] ?? '';
        $this->extra = array_diff_key($data, array_flip(['flagIcon', 'flagUnicode']));
    }
}

class Currency
{
    public string $code;
    public string $symbol;
    public string $name;
    public ?string $namePlural = null;
    public ?string $hexUnicode = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->code = $data['code'] ?? '';
        $this->symbol = $data['symbol'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->namePlural = $data['namePlural'] ?? null;
        $this->hexUnicode = $data['hexUnicode'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['code', 'symbol', 'name', 'namePlural', 'hexUnicode']));
    }
}

class GeoLocation
{
    public string $continent;
    public string $continentCode;
    public string $country;
    public string $countryCode;
    public ?string $capital = null;
    public ?string $region = null;
    public ?string $regionCode = null;
    public ?string $city = null;
    public ?string $postalCode = null;
    public ?string $dialCode = null;
    public ?bool $isInEu = null;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?int $accuracyRadius = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->continent = $data['continent'] ?? '';
        $this->continentCode = $data['continentCode'] ?? '';
        $this->country = $data['country'] ?? '';
        $this->countryCode = $data['countryCode'] ?? '';
        $this->capital = $data['capital'] ?? null;
        $this->region = $data['region'] ?? null;
        $this->regionCode = $data['regionCode'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->postalCode = $data['postalCode'] ?? null;
        $this->dialCode = $data['dialCode'] ?? null;
        $this->isInEu = $data['isInEu'] ?? null;
        $this->latitude = $data['latitude'] ?? null;
        $this->longitude = $data['longitude'] ?? null;
        $this->accuracyRadius = $data['accuracyRadius'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['continent', 'continentCode', 'country', 'countryCode', 'capital', 'region', 'regionCode', 'city', 'postalCode', 'dialCode', 'isInEu', 'latitude', 'longitude', 'accuracyRadius']));
    }
}

class Timezone
{
    public string $timeZone;
    public ?string $abbr = null;
    public ?int $offset = null;
    public ?bool $isDst = null;
    public ?string $utc = null;
    public ?string $currentTime = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->timeZone = $data['timeZone'] ?? '';
        $this->abbr = $data['abbr'] ?? null;
        $this->offset = $data['offset'] ?? null;
        $this->isDst = $data['isDst'] ?? null;
        $this->utc = $data['utc'] ?? null;
        $this->currentTime = $data['currentTime'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['timeZone', 'abbr', 'offset', 'isDst', 'utc', 'currentTime']));
    }
}

class Connection
{
    public ?int $asnNumber = null;
    public ?string $asnOrg = null;
    public ?string $isp = null;
    public ?string $org = null;
    public ?string $domain = null;
    public ?string $connectionType = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->asnNumber = $data['asnNumber'] ?? null;
        $this->asnOrg = $data['asnOrg'] ?? null;
        $this->isp = $data['isp'] ?? null;
        $this->org = $data['org'] ?? null;
        $this->domain = $data['domain'] ?? null;
        $this->connectionType = $data['connectionType'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['asnNumber', 'asnOrg', 'isp', 'org', 'domain', 'connectionType']));
    }
}

class Security
{
    public ?bool $isVpn = null;
    public ?bool $isTor = null;
    public ?string $isThreat = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->isVpn = $data['isVpn'] ?? null;
        $this->isTor = $data['isTor'] ?? null;
        $this->isThreat = $data['isThreat'] ?? null;
        $this->extra = array_diff_key($data, array_flip(['isVpn', 'isTor', 'isThreat']));
    }
}

class IPWhoData
{
    public string $ip;
    public ?GeoLocation $geoLocation = null;
    public ?Timezone $timezone = null;
    public ?Flag $flag = null;
    public ?Currency $currency = null;
    public ?Connection $connection = null;
    public ?UserAgent $userAgent = null;
    public ?Security $security = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->ip = $data['ip'] ?? '';
        $this->geoLocation = isset($data['geoLocation']) ? new GeoLocation($data['geoLocation']) : null;
        $this->timezone = isset($data['timezone']) ? new Timezone($data['timezone']) : null;
        $this->flag = isset($data['flag']) ? new Flag($data['flag']) : null;
        $this->currency = isset($data['currency']) ? new Currency($data['currency']) : null;
        $this->connection = isset($data['connection']) ? new Connection($data['connection']) : null;
        $this->userAgent = isset($data['userAgent']) ? new UserAgent($data['userAgent']) : null;
        $this->security = isset($data['security']) ? new Security($data['security']) : null;
        $this->extra = array_diff_key($data, array_flip(['ip', 'geoLocation', 'timezone', 'flag', 'currency', 'connection', 'userAgent', 'security']));
    }
}

class IPWhoAPIResponse
{
    public bool $success;
    public ?string $message = null;
    public ?IPWhoData $data = null;
    public array $extra = [];

    public function __construct(array $data = [])
    {
        $this->success = $data['success'] ?? false;
        $this->message = $data['message'] ?? null;
        $this->data = isset($data['data']) ? new IPWhoData($data['data']) : null;
        $this->extra = array_diff_key($data, array_flip(['success', 'message', 'data']));
    }
}