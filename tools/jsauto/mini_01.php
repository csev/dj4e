<?php

require_once "../config.php";
require_once "jsauto_util.php";

use \Tsugi\Util\U;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LAUNCH = LTIX::requireData();

header("Content-type:application/json");

// Get any due date information
$dueDate = SettingsForm::getDueDate();
$penalty = $dueDate->penalty;

$json_params = file_get_contents("php://input");
if (strlen($json_params) > 0 && isValidJSON($json_params)) {
    try {
        $decoded_input = json_decode($json_params);
        error_log("===== Incoming JSON: ".json_encode($decoded_input));
        if ( is_object($decoded_input) && is_object($decoded_input->step) ) {
            $step = $decoded_input->step->step ?? 0;
    } else {
        $step = 0;
    }
        $response = $decoded_input->response;
    } catch (Exception $e) {
    error_log("===== Unexpected data in JSON, resetting step to 0");
    $step = 0;
    }
} else {
    $step = 0;
}

$previousgrade = $RESULT->grade;
error_log("==== Previous grade ".$previousgrade);
$currentGrade = U::get($_SESSION, "currentgrade", 0.0);
$oldGrade = $currentGrade;

if ( $step != 0 ) participationPoints($currentGrade, 60.0);

$retval = array();
$retval['step'] = $step+1; // Can override later

$checkstep = 0;
if ( $step == $checkstep++ ) {
    $currentGrade = 0.0;
    $_SESSION["currentgrade"] = 0.0;
    $nextstep = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "searchfor", "text": "404", "message": "Check for 404 in returned text"}';

} else if ( $step == $checkstep++ ) {
    if ( $response === true ) {
        requiredPoints($currentGrade, $step, 20);
        $nextstep = '{"command": "switchurl", "text": "/", "message": "Switch to / url"}';
    } else {
        $nextstep = '{"command": "complete", "text": "finished", "message": "Test not completed"}';
    }

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "ping", "text": "42", "message": "Check for correct page load"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "switchurl", "text": "/broken", "message": "Switch to /broken url"}';

} else if ( $step == $checkstep++ ) {
    $nextstep = '{"command": "searchfor", "text": "500", "message": "Check for 500 in returned text"}';

} else if ( $step == $checkstep++ ) {
    $currentGrade = 100.0;
    $nextstep = '{"command": "complete", "text": "success", "message": "Test complete"}';

} else {
    $nextstep = '{"command": "stop", "text": "bad state", "message": "Fell into invalid state"}';
}

$retval['grade'] = $currentGrade;
$_SESSION['currentgrade'] = $currentGrade;
$nextstep = json_decode($nextstep, true);
$retval = array_merge($retval, $nextstep);

$sendGrade = webauto_compute_effective_score(100.0, $currentGrade, $penalty);
error_log("====== prev $previousgrade / send $sendGrade");
if ( $sendGrade*100.0 >= $previousgrade ) {
    $scoredetail = webauto_send_score($sendGrade);
    error_log("===== sendGrade=".$sendGrade." detail=".$scoredetail);
    $retval['detail'] = $scoredetail == true ? "Score sent ".($sendGrade*100) : htmlentities($scoredetail);
} else {
    $retval['detail'] = "Previous score of ".$previousgrade." higher than current grade ".($sendGrade*100);
}

$retval = json_encode($retval);
error_log("===== Next step: ".$retval);
echo($retval);

