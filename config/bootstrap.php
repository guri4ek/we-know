<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Some SAPIs omit process environment variables from PHP's superglobals.
// Preserve deployment values before Dotenv applies the local defaults.
$_ENV += getenv();

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
