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
    'mini_01.php' => 'Testing the mini_django framework out of the box',
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
$penalty = $dueDate->penalty;

$menu = false;
if ( $LAUNCH->link && $LAUNCH->user && $LAUNCH->user->instructor ) {
    $menu = new \Tsugi\UI\MenuSet();
    // $menu->addLeft('Student Data', 'grades.php');
    // $menu->addLeft('Send Grade', 'sendgrade.php');
    // if ( $CFG->launchactivity ) {
        // $menu->addRight(__('Launches'), 'analytics');
    // }
    $menu->addRight(__('Settings'), '#', /* push */ false, SettingsForm::attr());
}

// View
$OUTPUT->header();

$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);

// Settings button and dialog

if ( $LAUNCH->user->instructor ) {
    SettingsForm::start();
    SettingsForm::select("exercise", __('Please select an assignment'),$assignments);
    SettingsForm::text('delay',__('The number of seconds between retries.  Leave blank or set to zero to allow immediate retries.'));
    SettingsForm::text('delay_tries',__('The number of attmpts before the delay kicks in.  Leave blank or set to zero to trigger immediate delays.'));
    SettingsForm::dueDate();
    SettingsForm::done();
    SettingsForm::end();
}

$OUTPUT->flashMessages();

if ( ! is_string($assn) ) {
    if ( $USER->instructor ) {
        echo("<p>Please use settings to select an assignment for this tool.</p>\n");
    } else {
        echo("<p>This tool needs to be configured - please see your instructor.</p>\n");
    }
    $OUTPUT->footer();
    return;
}

$baseUrl = "http://localhost:9000";
?>
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Instructions</a></li>
    <li><a href="#tabs-2">Autograder</a></li>
    <li><a href="#tabs-3">Result Log</a></li>
  </ul>
  <div id="tabs-1">
<?php

if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}

$instructions = str_replace(".php", ".htm", $assn);
if ( file_exists($instructions) ) {
    include $instructions;
} else {
    echo("<p>Instructions go here.</p>\n");
}
?>
  </div>
  <div id="tabs-2">
<?php
if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}
?>
<p>
Url to test:
<input type="text" name="baseurl" style="width:60%;" value="<?= $baseUrl ?>"
/>
</p>
<p>
<button onclick="doNextStep();" id="nextstep" class="btn btn-primary" disabled>Run Next Step:</button>
<span id="stepinfo">
Placeholder
</span>
<button onclick="window.location.reload();" class="btn btn-normal" style="float:right;">Restart Test</button>
</p>
<p>
<span id="currentUrl">
</span>
<span id="currentStep">
</p>

<center>
<iframe style="width:95%; height:600px;" id="myframe" src="<?= $baseUrl ?>">
</iframe>
</center>

  </div>
  <div id="tabs-3">
<?php
if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}
?>
<ol id="resultlog">
<li>Test started at <?= $baseUrl ?> (<?= date('l jS \of F Y h:i:s A'); ?>)</li>
</ul>
</div>
</div>

<?php

$OUTPUT->footerStart();

?>

<script>
$( function() {
    $( "#tabs" ).tabs();
} );

var baseurl = "<?= $baseUrl ?>";

var currentUrl = baseurl;
var currentStep;
var currentStepCount = 0;

var postMessageTimeout = false;

function stopWithError(message) {
    console.error('Test halted', message);
    document.getElementById('stepinfo').textContent = message;
    document.getElementById('nextstep').disabled = true;
    document.getElementById('nextstep').classList.remove("btn-primary");
    document.getElementById('nextstep').classList.add("btn-danger");
    addResultLog(message);
}

function postMessageFail () {
    if ( postMessageTimeout ) clearTimeout(postMessageTimeout);
    postMessageTimeout = false;
    error_message = "Error communicating to autograder endpoint within frame";
    stopWithError(error_message);
}

function advanceStep(responseObject) {

    const bodystr = JSON.stringify({
          step: currentStep,
          response: responseObject,
    });

    console.debug("Body ", bodystr);

    fetch('<?php echo(addSession($assn)) ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: bodystr,
      })
      .then(response => response.text())
      .then(body => {
          try {
              return JSON.parse(body);
          } catch (error) {
              console.error("Error Parsing JSON response from server:");
              console.error(body);
              throw error;
          }
      })
      .then(data => {
        // Handle the response data
        console.debug('Retrieved next step', data);
        addResultLog("Completed");
        currentStep = data;
        currentStepCount += 1;
        addResultLog("Ready");
        document.getElementById('stepinfo').textContent = data.message + " (" + data.grade + " points)";
        document.getElementById('nextstep').disabled = false;
        if ( currentStep.command == 'complete' ) {
            addResultLog("Complete");
            document.getElementById('nextstep').disabled = true;
            if ( currentStep.text != "success" ) {
                document.getElementById('nextstep').classList.remove("btn-primary");
                document.getElementById('nextstep').classList.add("btn-warning");
            }
            return;
        }
     })
     .catch(error => {
        // Handle any errors
        document.getElementById('stepinfo').textContent = 'Network Error:' + error;
        document.getElementById('nextstep').disabled = true;
        document.getElementById('nextstep').classList.remove("btn-primary");
        document.getElementById('nextstep').classList.add("btn-danger");
        console.error('Error:', error);
     });
}

function addResultLog(message) {
    const resultId = "result_" + currentStepCount;
    if ( $('#'+resultId).length == 0 ) {
        $("#resultlog").append("<li id=\""+resultId+"\">last item</li>");
    }
    var result = message;
    if ( currentStep ) {
        result = message + ": " + currentStep.message;
        if ( (currentStep.detail ?? false) && (currentStep.command ?? null) == "complete") {
            result = result + " (" + currentStep.detail + ")";
        } else {
            result = result + " (" + currentStep.grade + " points)";
        }
    }
    document.getElementById(resultId).textContent = result;
}

window.addEventListener(
  "message",
  (event) => {
    console.log('Reieved autograder respnse in parent frame', event, currentStep);

    if ( postMessageTimeout ) clearTimeout(postMessageTimeout);
    postMessageTimeout = false;

    advanceStep(event.data);

  },
  false,
);

function newUrl(newurl) {
    console.log("Switching to new url", newurl);
    baseurl = newurl;
    currentUrl = baseurl;
    document.getElementById('myframe').src = currentUrl;
    advanceStep({"text": "success"});
}

function doNextStep() {
    console.debug('doNextStep, currentStep=', currentStep)
    if ( currentStep.command == 'switchurl' ) {
            currentUrl = (baseurl + currentStep.text);
            console.log('Switching to', currentUrl);
            document.getElementById('myframe').src = currentUrl;
            document.getElementById('currentUrl').textContent = currentUrl;
            advanceStep({"text": "success"});
            addResultLog("Complete");
            return;
    }
    console.log('Sending message to auto graded iframe', currentUrl, currentStep);
    addResultLog("In Progress");
    try {
        document.getElementById('myframe').contentWindow.postMessage(currentStep, currentUrl);
        postMessageTimeout = setTimeout(postMessageFail, 5000);
        document.getElementById('nextstep').disabled = true;
    } catch (error) {
        stopWithError("Error sending message to autograder within frame:" + error);
    }
}

console.log("Loading the first autograder step");
// Get the first currentStep
currentStep = false;
fetch('<?php echo(addSession($assn)) ?>')
    .then(response => response.text())
    .then(body => {
        try {
            return JSON.parse(body);
        } catch (error) {
            console.log("Error Parsing JSON response from server:");
            console.log(body);
            throw error;
        }
    })
    .then(step => {
        console.log('First step loaded', step)
        currentStep = step;
        addResultLog("Ready");
        document.getElementById('stepinfo').textContent = step.message;
        document.getElementById('nextstep').disabled = false;
        document.getElementById('currentUrl').textContent = baseurl;
    }).catch(error => {
        stopWithError('Network Error:' + error);
    });



</script>
<?php

$OUTPUT->footerEnd();

