<?php
require_once "../config.php";

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
    'single_lite.php' => 'Command Line SQL',
    '04models.php' => 'MDN Models Tutorial',
    '05many.php' => 'Many-to-Many Exercise',
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

// Get any due date information
$dueDate = SettingsForm::getDueDate();

// Let the assignment handle the POST
if ( count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    include($assn);
    return;
}

$menu = false;
if ( $LAUNCH->link && $LAUNCH->user && $LAUNCH->user->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    $menu->addLeft('Student Data', 'grades.php');
    if ( $CFG->launchactivity ) {
        $menu->addRight(__('Launches'), 'analytics');
    }
    $menu->addRight(__('Settings'), '#', /* push */ false, SettingsForm::attr());
}

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);

// Settings dialog
SettingsForm::start();
SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
SettingsForm::dueDate();
SettingsForm::done();
SettingsForm::end();

$OUTPUT->flashMessages();

$OUTPUT->welcomeUserCourse();

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

