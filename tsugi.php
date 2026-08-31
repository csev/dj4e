<?php

use \Tsugi\Core\LTIX;

// Chrome Private Prefetch Proxy — before session so we do not set cookies.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ( $path === '/.well-known/traffic-advice' || $path === '/.well-known/traffic-advice/' ) {
    header('Content-Type: application/trafficadvice+json');
    header('Cache-Control: public, max-age=86400');
    $advice = __DIR__ . '/.well-known/traffic-advice';
    if ( is_readable($advice) ) {
        readfile($advice);
    } else {
        echo '[{"user_agent":"prefetch-proxy","fraction":1.0}]';
    }
    return;
}

define('COOKIE_SESSION', true);
require_once "tsugi/config.php";

$launch = LTIX::session_start();

// Make PHP paths pretty .../install => install.php
$router = new Tsugi\Util\FileRouter();
$file = $router->fileCheck();
if ( $file ) {
    require_once($file);
    return;
}

// Pull in the Tsugi LMS (/lessons, /map, /badges ...)
$app = new \Tsugi\Controllers\Tsugi($launch);
$app['debug'] = true;

$app->run();
