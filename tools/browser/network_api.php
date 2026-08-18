<?php

require_once "../config.php";
require_once "browser_util.php";

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$secret = browser_exercise_secret('network_json');
echo json_encode(array(
    'ok' => true,
    'message' => 'Clue payload from the server',
    'clue' => $secret,
));
