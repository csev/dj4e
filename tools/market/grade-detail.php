<?php
require_once "../config.php";
\Tsugi\Core\LTIX::getConnection();

use \Tsugi\Util\U;
use \Tsugi\Grades\GradeUtil;
use \Tsugi\Core\Settings;
use \Tsugi\UI\SettingsForm;

session_start();

if ( ! U::get($_REQUEST, 'user_id') ) {
    die_with_error_log('user_id not specified');
}

// Get the user's grade data also checks session
$row = GradeUtil::gradeLoad($_REQUEST['user_id']);
$delay_str = Settings::linkGet('delay');
$delay = 0;
if ( is_numeric($delay_str) ) $delay = $delay_str+0;

$menu = new \Tsugi\UI\MenuSet();
$menu->addLeft(__('Back to all grades'), 'index.php');

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);
$OUTPUT->flashMessages();

// Show the basic info for this user
GradeUtil::gradeShowInfo($row, false);

if ( U::isEmpty($row['json']) ) {
    echo("<p>No submission</p>\n");
    $OUTPUT->footer();
    return;
}

// Unique detail
$json = json_decode($row['json']);
if ( is_object($json) && isset($json->url)) {
    echo("<p>Submitted URL:\n");
    echo("<a href=\"".safe_href($json->url)."\" target=\"_new\">");;
    echo(htmlentities($json->url));
    echo("</a></p>\n");
}

if ( $delay > 0 && is_object($json) && isset($json->when)) {
    $when = $json->when;
    $delta = ($when + $delay) - time();
    if ( $delta > 0 ) {
        echo("<p>Can be retried in ".SettingsForm::getDueDateDelta(($when + $delay) - time())."</p>\n");
    }
}

if ( is_object($json) && isset($json->tries)) {
    echo("<p>Tries: ".htmlentities($json->tries)."</p>\n");
}

if ( is_object($json) && isset($json->output)) {
    echo("<p>Student output:</p><hr/>\n");
    echo($json->output);
}

$OUTPUT->footer();
