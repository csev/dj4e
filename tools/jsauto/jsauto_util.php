<?php

use \Tsugi\Util\U;
use \Tsugi\Grades\GradeUtil;
use \Tsugi\Core\LTIX;

function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}

function participationPoints(&$currentGrade, $maxParticipationPoints) {
    if ( $currentGrade >= 100.0 ) return;
    if ( $currentGrade < $maxParticipationPoints) $currentGrade += 10.0;
}

function requiredPoints(&$currentGrade, $step, $points) {
    if ( $currentGrade >= 100.0 ) return;
    $stepsPassed = U::get($_SESSION, "stepspassed", array());

    // Check if this step already counted
    if ( in_array($step, $stepsPassed) ) return;
    $stepsPassed[] = $step;
    $_SESSION["stepspassed"] = $stepsPassed;

    $currentGrade += $points;
    if ( $currentGrade >= 100.0 ) $currentGrade = 100.0;
}

function webauto_compute_effective_score($perfect, $passed, $penalty) {
    $score = $passed * (1.0 / $perfect);
    if ( $score < 0 ) $score = 0;
    if ( $score > 1 ) $score = 1;
    if ( $passed > $perfect ) $passed = $perfect;
    if ( $penalty == 0 ) {
        $scorestr = "Score = $score ($passed/$perfect)";
    } else {
        $scorestr = "Raw Score = $score ($passed/$perfect) ";
        $score = $score * (1.0 - $penalty);
        $scorestr .= "Effective Score = $score after ".$penalty*100.0." percent late penalty";
    }
    return $score;
}

function webauto_send_score($grade) {
    global $USER, $OUTPUT;

    if ( ! isset($_SESSION['lti']) ) {
        return 'Not setup to return a grade..';
    }

    if ( $USER->instructor ) {
        return 'Instructor grades are not sent..';
    }

    $LTI = $_SESSION['lti'];

    $old_grade = isset($LTI['grade']) ? $LTI['grade'] : 0.0;

    if ( $grade < $old_grade ) {
        $grade = $old_grade;
    }

    $debug_log = array();
    $retval = LTIX::gradeSend($grade, false, $debug_log);
    $success = false;
    if ( $retval == true ) {
        $success = "Grade sent to server (".$grade.")";
        return true;
    } else if ( is_string($retval) ) {
        return "Grade not sent: ".$retval;
    } else {
        error_log("Error sending grade:");
        error_log($retval);
        return "Internal error";
    }

}
