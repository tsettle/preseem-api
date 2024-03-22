<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use \Preseem\Preseem;

$api = new Preseem($preseem_url,$preseem_key);

print_r($api->list('packages'));


