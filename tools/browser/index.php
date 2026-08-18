<?php
require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;
use \Tsugi\UI\Lessons;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// All the assignments we support
$assignments = array(
    'password_field.php' => 'Inspecting a Password Field',
    'css_hidden.php' => 'Finding CSS-Hidden Text',
    'data_attribute.php' => 'Reading a data-* Attribute',
    'html_comment.php' => 'Secrets in HTML Comments',
    'console_dataset.php' => 'Console: $0 and dataset',
    'network_json.php' => 'Network: Reading a JSON Response',
    'local_storage.php' => 'Application: localStorage',
);

// Prefer link settings; if unset, take LTI custom or ?inherit= / ?exercise= from lessons.
$assn = Settings::linkGet('exercise');
if ( ! $assn || ! isset($assignments[$assn]) ) {
    $rlid = false;
    if ( isset($_GET['inherit']) && is_string($_GET['inherit']) && strlen($_GET['inherit']) ) {
        $rlid = $_GET['inherit'];
    } else if ( isset($_GET['exercise']) && is_string($_GET['exercise']) && strlen($_GET['exercise']) ) {
        $rlid = $_GET['exercise'];
    }
    if ( $rlid && isset($assignments[$rlid]) ) {
        // Direct GET of a known exercise filename (handy for testing)
        $assn = $rlid;
    } else if ( $rlid && isset($CFG->lessons) ) {
        $l = new Lessons($CFG->lessons);
        $assn = $l->getCustomWithInherit('exercise', $rlid);
    } else {
        $assn = LTIX::ltiCustomGet('exercise');
    }
    if ( $assn && isset($assignments[$assn]) ) {
        Settings::linkSet('exercise', $assn);
    }
}

// Get any due date information
$dueDate = SettingsForm::getDueDate();
// Let the assignment handle the POST
if ( count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    require($assn);
    return;
}

$menu = false;
if ( $LAUNCH->link && $LAUNCH->user && $LAUNCH->user->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    $menu->addLeft('Student Data', 'grades.php');
    $menu->addLeft('Send Grade', 'sendgrade.php');
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

$code = $USER->id+$LINK->id+$CONTEXT->id;
if ( ! $USER->displayname || $USER->displayname == '' ) {
    // echo('<p style="color:blue;">Auto grader launched without a student name.</p>'.PHP_EOL);
} else {
    $OUTPUT->welcomeUserCourse();
}

$ALL_GOOD = false;
//
// Assume try / catch is in the script
if ( $assn && isset($assignments[$assn]) ) {
    ob_start();
    include($assn);
    $ob_output = ob_get_contents();
    ob_end_clean();
    echo($ob_output);

    $LAUNCH->result->setJsonKey('output', $ob_output);
} else {
    if ( $USER->instructor ) {
        echo("<p>Please use settings to select an assignment for this tool.</p>\n");
    } else {
        echo("<p>This tool needs to be configured - please see your instructor.</p>\n");
    }
}

$ALL_GOOD = true;
if ( ob_get_status() ) {
    $ob_output = ob_get_contents();
    ob_end_clean();
    echo($ob_output);
}

$OUTPUT->footer();
