<?php

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
    // Check if this step already counted
    $currentGrade += $points;
    if ( $currentGrade >= 100.0 ) $currentGrade = 100.0;
}

