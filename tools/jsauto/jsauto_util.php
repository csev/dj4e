<?php

use \Tsugi\Util\U;

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

