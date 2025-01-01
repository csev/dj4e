<?php

require_once "../config.php";

use \Tsugi\Util\U;
use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

header("Content-type:application/json");

function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}

$json_params = file_get_contents("php://input");
if (strlen($json_params) > 0 && isValidJSON($json_params)) {
    $decoded_input = json_decode($json_params);
    error_log("Response: ".json_encode($decoded_input));
    $step = $decoded_input->step->step;
    $response = $decoded_input->response;
} else {
    $step = 0;
}

function participationPoints(&$currentGrade, $maxParticipationPoints) {
    if ( $currentGrade >= 100.0 ) return;
    if ( $currentGrade < $maxParticipationPoints) $currentGrade += 10.0;
}

function requiredPoints(&$currentGrade, $step, $points) {
    if ( $currentGrade >= 100.0 ) return;
    // Check if this step already counted
    $currentGrade += $points;
    if ( $currentGrade >= 100.0 ) $currentGrade = 100.0;
}

$currentGrade = U::get($_SESSION, "currentgrade", 0.0);
$oldGrade = $currentGrade;
$gradeSendOnce = U::get($_SESSION, "gradesendonce", 0.0);
$stepsPassed = U::get($_SESSION, "stepspassed", array());

if ( $step != 0 ) participationPoints($currentGrade, 60.0);

$ret = array();
$ret['step'] = $step+1; // Can override later

switch($step) {
case 0:
    $retval = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';
    break;

case 1:
    $text = $response->text;
    if ( $text == "42" ) requiredPoints($currentGrade, $step, 20);
    $retval = '{"command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';
    break;

case 2:
    $retval = '{"command": "searchfor", "text": "404", "message": "Check for 404 in returned text"}';
    break;

case 3:
    requiredPoints($currentGrade, $step, 20);
    $retval = '{"command": "switchurl", "text": "/", "message": "Switch to / url"}';
    break;

case 4:
    $retval = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';
    break;

case 5:
    $retval = '{"command": "switchurl", "text": "/broken", "message": "Switch to /broken url"}';
    break;

case 6:
    $retval = '{"command": "searchfor", "text": "500", "message": "Check for 500 in returned text"}';
    break;

case 7:
    requiredPoints($currentGrade, $step, 20);
    $ret['step'] = 2; // Loop around
    $retval = '{"command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';
    break;

default:

    $retval = '{"command": "stop", "text": "bad state", "message": "Fell into invalid state"}';
    break;
}

// $gradeSendOnce = U::get($_SESSION, "gradesendonce", 0.0);
// $stepsPassed = U::get($_SESSION, "stepspassed", array());

if ( $currentGrade != $oldGrade ) $_SESSION["currentgrade"] = $currentGrade;

$ret['grade'] = $currentGrade;
$command = json_decode($retval, true);
$tosend = array_merge($ret, $command);
$tosendstr = json_encode($tosend);

error_log("Next step: ".$tosendstr);
echo($tosendstr);
