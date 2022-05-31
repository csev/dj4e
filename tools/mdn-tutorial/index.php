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
    '01install.php' => 'Getting Django installed and up and running',
    '03skeleton.php' => 'Setting up the Skeleton site',
    '05admin.php' => 'Exploring the Admin site',
    '06home.php' => 'Django Home Page',
    '07details.php' => 'Django Details Pages - No challenge',
    '07details-full.php' => 'Django Details Pages - With challenge',
    '08sessions.php' => 'Django Sessions',
    '09users.php' => 'Django Users and Authentication',
    '10forms.php' => 'Django Forms',
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

// Get any due date information
$dueDate = SettingsForm::getDueDate();
// Let the assignment handle the POST
if ( count($_POST) > 0 && $assn && isset($assignments[$assn]) ) {
    require($assn);
    return;
}

// Test info
function webauto_check_test() {
    global $url, $first_name, $last_name, $title_name, $book_title, $full_name, $last_first, $meta, $adminpw, $userpw;
    if ( ! webauto_testrun($url) ) return;
    error_out('Test run - switching to sample data');
    $first_name = 'Jamal';
    $last_name = 'Michaella';
    $title_name = 'Darryl';
    $book_title = "How the Number 42 and $title_name are Connected";
    $full_name = $first_name . ' ' . $last_name;
    $last_first = $last_name . ', ' . $first_name;
    $meta = '<meta name="dj4e" content="735b90b4568125ed6c3f678819b6e058">';
    $adminpw = 'readony_8ffd-6c005';
    $userpw = 'readony_8ffd-6c005';
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

echo('<div style="float: right;">');
if ( $USER->instructor ) {
    if ( $CFG->launchactivity ) {
        echo('<a href="analytics" class="btn btn-default">Launches</a> ');
    }
    echo('<a href="grades.php" target="_blank"><button class="btn btn-info">Grade detail</button></a> '."\n");
}
SettingsForm::button();
echo('</div>');

SettingsForm::start();
SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
SettingsForm::dueDate();
SettingsForm::done();
SettingsForm::end();

$OUTPUT->flashMessages();

$code = $USER->id+$LINK->id+$CONTEXT->id;
if ( ! $USER->displayname || $USER->displayname == '' ) {
    echo('<p style="color:blue;">Auto grader launched without a student name.</p>'.PHP_EOL);
} else {
    $OUTPUT->welcomeUserCourse();
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
?>
<script>
function sendToIframe(id, html) {
    var iframe = document.getElementById(id);
    var iframedoc = iframe.contentDocument || iframe.contentWindow.document;
    console.log(html);
    iframedoc.body.innerHTML = html;
}
</script>
<?php
$OUTPUT->footerEnd();


