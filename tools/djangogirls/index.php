<?php
require_once "../config.php";

use \Tsugi\Util\U;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

if ( SettingsForm::isSettingsPost() ) {
    if ( isset($_POST['delay']) && $_POST['delay'] != '' && ! is_numeric($_POST['delay']) ) {
        $_SESSION['error'] = __('Delay must be numeric');
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }
    if ( isset($_POST['delay_tries']) && $_POST['delay_tries'] != '' && ! is_numeric($_POST['delay_tries']) ) {
        $_SESSION['error'] = __('Delay_tries must be numeric');
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }
    SettingsForm::handleSettingsPost();
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// Django Girls tutorial milestones (matches assn/django-girls folder tags)
$assignments = array(
    '01_startproject.php' => '01 – startproject',
    '03_admin.php' => '03 – admin',
    '05_html.php' => '05 – html',
    '06_dynamic.php' => '06 – dynamic',
    '07_templates.php' => '07 – templates',
    '08_css.php' => '08 – css',
    '09_base.php' => '09 – base',
    '10_detail.php' => '10 – detail',
);

$LAUNCH->link->settingsDefaultsFromCustom(array('delay', 'delay_tries', 'exercise'));
$assn = Settings::linkGet('exercise');
$delay = Settings::linkGet('delay');
$delay_tries = Settings::linkGet('delay_tries');

// Load the previous attempt
$attempt = json_decode($RESULT->getJson());
$when = 0;
$tries = 0;
if ( $attempt && is_object($attempt) ) {
    if ( isset($attempt->when) ) $when = $attempt->when + 0;
    if ( isset($attempt->tries) ) $tries = $attempt->tries + 0;
}

$SECONDS_BEFORE_RETRY = 0;
if ( $delay >= 0 && $when > 0 && $tries > $delay_tries) {
    $SECONDS_BEFORE_RETRY = ($when+$delay) - time();
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
?>
<style>
a[target="_blank"]:after {
  content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAQElEQVR42qXKwQkAIAxDUUdxtO6/RBQkQZvSi8I/pL4BoGw/XPkh4XigPmsUgh0626AjRsgxHTkUThsG2T/sIlzdTsp52kSS1wAAAABJRU5ErkJggg==);
  margin: 0 3px 0 5px;
}
</style>
<script>
function sendToIframe(id, html) {
    var iframe = document.getElementById(id);
    var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
    iframedoc.body.innerHTML = html;
}
</script>
<?php

$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);

if ( $LAUNCH->user->instructor ) {
    SettingsForm::start();
    SettingsForm::select("exercise", __('Please select a milestone'), $assignments);
    SettingsForm::text('delay', __('The number of seconds between retries. Leave blank or set to zero to allow immediate retries.'));
    SettingsForm::text('delay_tries', __('The number of attempts before the delay kicks in. Leave blank or set to zero to trigger immediate delays.'));
    SettingsForm::dueDate();
    SettingsForm::done();
    SettingsForm::end();
}

$OUTPUT->flashMessages();

if ( $USER->displayname && $USER->displayname != '' ) {
    $OUTPUT->welcomeUserCourse();
}

$ALL_GOOD = false;

function my_ob_get_status() {
    $var = ob_get_status();
    if ( is_array($var) && isset($var["name"]) && $var["name"] != "URL-Rewriter" ) {
        return true;
    }
    return false;
}

function fatalHandler() {
    global $ALL_GOOD, $OUTPUT;
    if ( $ALL_GOOD ) return;
    if ( my_ob_get_status() ) {
        $ob_output = ob_get_contents();
        ob_end_clean();
        echo($ob_output);
    }
    $error = error_get_last();
    if ( $error ) {
        error_out("Fatal error: " . $error["message"]);
    }
    $OUTPUT->footer();
    exit();
}
register_shutdown_function("fatalHandler");

if ( $assn && isset($assignments[$assn]) ) {
    ob_start();
    try {
        include($assn);
    } catch(Exception $e) {
        $message = $e->getMessage();
        if ( strpos($message,'Did not find form') === 0 ||
             strpos($message,'Did not find anchor') === 0 ||
             strpos($message,'Did not find form field') === 0 ||
             strpos($message,'Could not retrieve HTML') === 0 ) {
            // expected
        } else {
            error_log("Unexpected exception: ".get_class($e)." Message=".$e->getMessage());
            throw $e;
        }
    }
    if ( my_ob_get_status() ) {
        $ob_output = ob_get_contents();
        ob_end_clean();
        echo($ob_output);
    }

    $result = array("when" => time(), "tries" => $tries+1, "submit" => $_POST, "output" => $ob_output, "url" => U::get($_GET, 'url', ''));
    $LAUNCH->result->setJson(json_encode($result));
} else {
    if ( $USER->instructor ) {
        echo("<p>Please use settings to select a milestone for this tool.</p>\n");
    } else {
        echo("<p>This tool needs to be configured - please see your instructor.</p>\n");
    }
}

$ALL_GOOD = true;
if ( my_ob_get_status() ) {
    $ob_output = ob_get_contents();
    ob_end_clean();
    echo($ob_output);
}

$OUTPUT->footerStart();
$OUTPUT->footerEnd();
