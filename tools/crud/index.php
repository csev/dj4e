<?php
require_once "../config.php";

use \Tsugi\Util\U;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;
use \Tsugi\UI\Lessons;

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

// All the assignments we support
$assignments = array(
    '01hello.php' => 'Hello World',
    '02autos.php' => 'Autos CRUD',
    '02cats.php' => 'Cats CRUD',
    '99cats.php' => 'Cats 99',
    '99stars.php' => 'Stars 99',
    '99random.php' => 'Random 99',
    '99midterm.php' => 'Exam 99',
    '11adlist1.php' => 'AdList #1',
    '11adlist1b.php' => 'AdList #1 + Pictures',
    '12adlist2.php' => 'AdList #2 - Pictures + Comments',
    '12adlist2b.php' => 'AdList #2B - Comments only',
    '13adlist3.php' => 'AdList #3',
    '14adlist4.php' => 'AdList #4',
    '16solo.php' => 'First Solo',
    '15solo.php' => 'Random Solo (old)',
    '17solo.php' => 'Second (Random) Solo',
);

$oldsettings = Settings::linkGetAll();

$password = Settings::linkGet('password');
if ( strlen(U::get($_POST, "password", '')) > 0  ) {
    $_SESSION['assignment_password'] = U::get($_POST, "password");
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

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

$password_ok = strlen($password) < 1 || U::get($_SESSION,'assignment_password') == $password;

// Get any due date information
$dueDate = SettingsForm::getDueDate();
// Let the assignment handle the POST
if ( $password_ok && count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    require($assn);
    return;
}

$menu = false;
if ( $LAUNCH->link && $LAUNCH->user && $LAUNCH->user->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    $menu->addLeft('Student Data', 'grades.php');
    $menu->addLeft('Send Grade', 'sendgrade.php');
    $menu->addLeft('Codes', 'codes.php');
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
    console.log(html);
    iframedoc.body.innerHTML = html;
}
</script>
<?php

function webauto_check_test() {
    global $url, $first_name, $last_name, $title_name, $book_title, $full_name, $last_first, $meta, $adminpw, $userpw, $useraccount;
    global $user1account, $user1pw, $user2account, $user2pw, $check;
    if ( ! webauto_testrun($url) ) return;
    error_out('Test run - switching to sample data');
    $first_name = 'Jamal';
    $last_name = 'Michaella';
    $title_name = 'Darryl';
    $book_title = "How the Number 42 and $title_name are Connected";
    $full_name = $first_name . ' ' . $last_name;
    $last_first = $last_name . ', ' . $first_name;
    $check = '735b90b4568125ed6c3f678819b6e058';
    $meta = '<meta name="dj4e" content="'.$check.'">';
    $adminpw = 'dj4e_42_!';
    $useraccount = 'dj4e_user1';
    $userpw = 'Meow_81e728_41';
    $user1account = 'dj4e_user1';
    $user1pw = 'Meow_81e728_41';
    $user2account = 'dj4e_user2';
    $user2pw = 'Meow_42_81e728';
}

$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);

// Settings button and dialog
if ( $LAUNCH->user->instructor ) {
    SettingsForm::start();
    SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
    SettingsForm::text("password", __('Set a password to protect this assignment'));
    SettingsForm::text('delay',__('The number of seconds between retries.  Leave blank or set to zero to allow immediate retries.'));
    SettingsForm::text('delay_tries',__('The number of attmpts before the delay kicks in.  Leave blank or set to zero to trigger immediate delays.'));
    SettingsForm::dueDate();
    SettingsForm::done();
    SettingsForm::end();
}

$OUTPUT->flashMessages();

$code = $USER->id+$LINK->id+$CONTEXT->id;
if ( ! $USER->displayname || $USER->displayname == '' ) {
    // echo('<p style="color:blue;">Auto grader launched without a student name.</p>'.PHP_EOL);
} else {
    $OUTPUT->welcomeUserCourse();
}

if ( ! $password_ok ) {
?>
<p>
This autograder is password protected, please enter the instructor provided password to
unlock this assignment.
</p>
<p>
<form method="post">
    Password:
    <input type="password" name="password">
    <input type="submit">
</form>
<?php
    $OUTPUT->footer();
    return;
}

$ALL_GOOD = false;

// Since we are doing trans_sid, there is always an active buffer
// https://www.php.net/manual/en/function.ob-get-status.php
/*
Array
(
    [level] => 2
    [type] => 0
    [status] => 0
    [name] => URL-Rewriter
    [del] => 1
)
 */
// All we want to know is whether or not we are buffering - don't
// pop the buffer stack if the top one is the URL-Rewriter
function my_ob_get_status() {
    $var = ob_get_status();
    if ( is_array($var) && isset($var["name"]) && $var["name"] != "URL-Rewriter" ) {
        return true;
    }
    return false;
}

function my_error_handler($errno , $errstr, $errfile, $errline , $trace = false)
{
    global $OUTPUT, $ALL_GOOD;
    if ( my_ob_get_status() ) {
        $ob_output = ob_get_contents();
        ob_end_clean();
        echo($ob_output);
    }
    error_out("The autograder did not find something it was looking for in your HTML - test ended.");
    error_out("Usually the problem is in one of the pages returned from your application.");
    error_out("Use the 'Toggle' links above to see the pages returned by your application.");
    $message = $errfile."@".$errline." ".$errstr;
    error_log($message);
    if ( $trace ) error_log($trace);
    $detail =
        "Check the most recently retrieved page (above) and see why the autograder is uphappy.\n" .
        "\nHere is some internal detail where the autograder was unable to continue.\n".
        'Caught exception: '.$message."\n".$trace."\n";
    showHTML("Internal error detail.",$detail);
    $OUTPUT->footer();
    $ALL_GOOD = true;
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
    error_out("Fatal error handler triggered");
    if($error) {
        my_error_handler($error["type"], $error["message"], $error["file"], $error["line"]);
    } else {
        $OUTPUT->footer();
    }
    exit();
}
register_shutdown_function("fatalHandler");

if ( $assn && isset($assignments[$assn]) ) {
    ob_start();
    try {
        include($assn);
    } catch(Exception $e) { // Catch and eat expected throws...
        $message = $e->getMessage();
        if ( strpos($message,'Did not find form') === 0 ) {
            // pass
        } else if ( strpos($message,'Did not find anchor') === 0 ) {
            // pass
        } else if ( strpos($message,'Did not find form field') === 0 ) {
            // pass
        } else if ( strpos($message,'Could not retrieve HTML') === 0 ) {
            // pass
        } else { // Unexpected...
            error_log("Unexpected exception: ".get_class($e)." Message=".$e->getMessage());
            throw $e; // rethrow
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
        echo("<p>Please use settings to select an assignment for this tool.</p>\n");
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


