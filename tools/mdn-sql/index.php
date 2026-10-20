<?php
require_once "../config.php";
require_once "../crud/assignment_lock.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// All the assignments we support
$assignments = array(
    '04models.php' => 'MDN Models Tutorial',
    '05many.php' => 'Many-to-Many Exercise',
    'single_lite.php' => 'Command Line SQL',
);

$oldsettings = Settings::linkGetAll();

$assn = Settings::linkGet('exercise');

$custom = LTIX::ltiCustomGet('exercise');
if ( $assn && isset($assignments[$assn]) ) {
    // Configured
} else if ( strlen($custom) > 0 && isset($assignments[$custom]) ) {
    Settings::linkSet('exercise', $custom);
    $assn = $custom;
}

if ( isset($LAUNCH->link) && is_object($LAUNCH->link) ) {
    $LAUNCH->link->settingsDefaultsFromCustom(array('lock_until'));
}
$dj4e_lock = dj4e_assignment_lock();
$dj4e_lock_choices = dj4e_lock_catalog();

// Get any due date information
$dueDate = SettingsForm::getDueDate();

// Let the assignment handle the POST. A locked student cannot submit.
if ( $dj4e_lock['open'] && count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    if ( is_object($RESULT) ) $RESULT->recordAttempt();
    include($assn);
    return;
}

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

// Settings button and dialog

echo('<span style="float: right;">');
if ( $USER->instructor ) {
    if ( $CFG->launchactivity ) {
        echo('<a href="analytics" class="btn btn-default">Launches</a> ');
        echo('<a href="sendgrade.php" class="btn btn-default">Grade Send</a> ');
    }
    echo('<a href="grades.php" target="_blank"><button class="btn btn-info">Grade detail</button></a> '."\n");
SettingsForm::button();
}
echo('</span>');

SettingsForm::start();
SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
if ( count($dj4e_lock_choices['choices']) > 0 ) {
    SettingsForm::note(__('Lock this autograder until the selected assignment is complete with a score of at least 80%.'));
    SettingsForm::select("lock_until", __('No lock'), $dj4e_lock_choices['choices']);
}
SettingsForm::dueDate();
SettingsForm::done();
SettingsForm::end();

$OUTPUT->flashMessages();

$OUTPUT->welcomeUserCourse();

if ( ! $dj4e_lock['open'] ) {
    echo(dj4e_assignment_lock_html($dj4e_lock));
    $OUTPUT->footer();
    return;
}

if ( $dj4e_lock['configured'] && ! $dj4e_lock['met'] ) {
    echo(dj4e_assignment_lock_instructor_html($dj4e_lock));
}

if ( $assn && isset($assignments[$assn]) ) {
    include($assn);
} else {
    if ( $USER->instructor ) {
        echo("<p>Please use settings to select an assignment for this tool.</p>\n");
    } else {
        echo("<p>This tool needs to be configured - please see your instructor.</p>\n");
    }
}
        

$OUTPUT->footer();

