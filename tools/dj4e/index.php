<?php
require_once "../config.php";

use \Tsugi\Util\U;
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
    '01hello.php' => 'Hello World',
    '02autos.php' => 'Autos CRUD',
    '02cats.php' => 'Cats CRUD',
    '11adlist1.php' => 'AdList #1',
    '12adlist2.php' => 'AdList #2',
);

$oldsettings = Settings::linkGetAll();

$password = Settings::linkGet('password');
if ( strlen(U::get($_POST, "password")) > 0  ) {
    $_SESSION['assignment_password'] = U::get($_POST, "password");
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

$assn = Settings::linkGet('exercise');
$custom = LTIX::ltiCustomGet('exercise');
if ( $assn && isset($assignments[$assn]) ) {
    // Configured
} else if ( strlen($custom) > 0 && isset($assignments[$custom]) ) {
    Settings::linkSet('exercise', $custom);
    $assn = $custom;
}


if ( $assn === false && isset($_GET["inherit"]) && isset($CFG->lessons) ) {
    $l = new Lessons($CFG->lessons);
    if ( $l ) {
        $lti = $l->getLtiByRlid($_GET['inherit']);
        if ( isset($lti->custom) ) foreach($lti->custom as $custom ) {
            if (isset($custom->key) && isset($custom->value) && $custom->key == 'exercise' ) {
                $assn = $custom->value;
                Settings::linkSet('exercise', $assn);
            }
        }
    }
}

$password_ok = strlen($password) < 1 || U::get($_SESSION,'assignment_password') == $password;

// Get any due date information
$dueDate = SettingsForm::getDueDate();
// Let the assignment handle the POST
if ( $password_ok && count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    require($assn);
    return;
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
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();

// Settings button and dialog

if ( $USER->instructor ) {
    echo('<div style="float: right;">');
    if ( $CFG->launchactivity ) {
        echo('<a href="analytics" class="btn btn-default">Launches</a> ');
    }
    echo('<a href="grades.php" target="_blank"><button class="btn btn-info">Grade detail</button></a> '."\n");
    SettingsForm::button();
    echo('</div>');

    SettingsForm::start();
    SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
    SettingsForm::text("password", __('Set a password to protect this assignment'));
    SettingsForm::dueDate();
    SettingsForm::done();
    SettingsForm::end();
}

$OUTPUT->flashMessages();

$code = $USER->id+$LINK->id+$CONTEXT->id;
if ( ! $USER->displayname || $USER->displayname == '' ) {
    echo('<p style="color:blue;">Auto grader launched without a student name.</p>'.PHP_EOL);
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

function my_error_handler($errno , $errstr, $errfile, $errline , $trace = false)
{
    global $OUTPUT, $ALL_GOOD;
    if ( ob_get_status() ) {
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
    if ( ob_get_status() ) {
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

$OUTPUT->footerStart();
$OUTPUT->footerEnd();


