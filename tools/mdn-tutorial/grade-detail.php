<?php
require_once "../config.php";
\Tsugi\Core\LTIX::getConnection();

use \Tsugi\Util\U;
use \Tsugi\Grades\GradeUtil;

session_start();

if ( ! U::get($_REQUEST, 'user_id') ) {
    die_with_error_log('user_id not specified');
}

// Get the user's grade data also checks session
$row = GradeUtil::gradeLoad($_REQUEST['user_id']);

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();

// Show the basic info for this user
GradeUtil::gradeShowInfo($row);

if ( U::isEmpty($row['json']) ) {
    echo("<p>No submission</p>\n");
    $OUTPUT->footer();
    return;
}

// Unique detail
echo("<p>Submitted URL:</p>\n");
$json = json_decode($row['json']);
if ( is_object($json) && isset($json->url)) {
    echo("<p><a href=\"".safe_href($json->url)."\" target=\"_new\">");;
    echo(htmlent_utf8($json->url));
    echo("</a></p>\n");
}

if ( is_object($json) && isset($json->output)) {
    echo("<p>Student output:</p><hr/>\n");
    echo($json->output);
}

$OUTPUT->footer();
