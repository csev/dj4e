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

$retval = array();
$retval['step'] = $step+1; // Can override later

$checkstep = 0;

if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';

} else if ( $step == $checkstep++ ) {
    $text = $response->text;
    if ( $text == "42" ) requiredPoints($currentGrade, $step, 20);
    $nextstep = '{"command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "searchfor", "text": "404", "message": "Check for 404 in returned text"}';

} else if ( $step == $checkstep++ ) {
    requiredPoints($currentGrade, $step, 20);
    $nextstep = '{"command": "switchurl", "text": "/", "message": "Switch to / url"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "switchurl", "text": "/broken", "message": "Switch to /broken url"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "searchfor", "text": "500", "message": "Check for 500 in returned text"}';

} else if ( $step == $checkstep++ ) {
    requiredPoints($currentGrade, $step, 20);
    $retval['step'] = 2; // Loop around
    $nextstep = '{"command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';

} else {
    $nextstep = '{"command": "stop", "text": "bad state", "message": "Fell into invalid state"}';
}

// $gradeSendOnce = U::get($_SESSION, "gradesendonce", 0.0);
// $stepsPassed = U::get($_SESSION, "stepspassed", array());

if ( $currentGrade != $oldGrade ) $_SESSION["currentgrade"] = $currentGrade;

$retval['grade'] = $currentGrade;
$nextstep = json_decode($nextstep, true);
$retval = array_merge($retval, $nextstep);

$retval = json_encode($retval);
error_log("Next step: ".$retval);
echo($retval);
