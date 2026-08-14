<?php
require __DIR__ . '/vendor/autoload.php';

use IpWho\SDK\Client;
use IpWho\SDK\Exception\IpWhoException;

$KEY = getenv('IPWHO_API_KEY');
$pass = 0; $fail = 0;
function ok($c, $m) { global $pass, $fail; if ($c) { $pass++; echo "  PASS $m\n"; } else { $fail++; echo "  FAIL $m\n"; } }

$c = new Client($KEY);

// 1. lookup
$r = $c->lookup('8.8.8.8');
$d = $r->data;
$gl = $d->geoLocation; $tz = $d->timezone; $fl = $d->flag; $cu = $d->currency; $cn = $d->connection;
ok($d->ip === '8.8.8.8', "lookup ip == 8.8.8.8");
ok($gl->country === 'United States', "lookup country == United States (got {$gl->country})");
ok($cn->asnNumber === 15169, "lookup asnNumber == 15169 (got {$cn->asnNumber})");
ok($gl->dialCode !== null, "dial_code captured ({$gl->dialCode})");
ok($gl->isInEu !== null, "is_in_eu captured");
ok($tz->timeZone !== null, "time_zone captured ({$tz->timeZone})");
ok($fl->flagIcon !== null, "flag_Icon captured ({$fl->flagIcon})");
ok($fl->flagUnicode !== null, "flag_unicode captured ({$fl->flagUnicode})");
ok($cu->namePlural !== '', "name_plural captured ({$cu->namePlural})");
ok($cn->asnOrg !== null, "asn_org captured ({$cn->asnOrg})");
ok($cn->connectionType !== null, "connection_type captured ({$cn->connectionType})");

// 2. me
$me = $c->me();
ok($me->data->ip !== '', "me ip captured ({$me->data->ip})");

// 3. bulk — responseArray now on typed model
$b = $c->bulk(['8.8.8.8', '1.1.1.1']);
$ra = $b->data->responseArray ?? null;
ok(is_array($ra) && count($ra) === 2, "bulk returns 2 (got " . (is_array($ra) ? count($ra) : 'N/A') . ")");

// 4. bad key
try {
    (new Client('sk.invalid_test_key'))->lookup('8.8.8.8');
    ok(false, "bad key should raise");
} catch (IpWhoException $e) {
    ok(true, "bad key raised " . get_class($e));
} catch (\Throwable $e) {
    ok(true, "bad key raised " . get_class($e));
}

echo "\nPHP RESULT: $pass passed, $fail failed\n";
exit($fail ? 1 : 0);
