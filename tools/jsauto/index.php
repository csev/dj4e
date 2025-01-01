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
    'tutorial01.php' => 'Writing your first Django app, (part 1)',
    'tutorial02.php' => 'Models and administration (part 2)',
    'tutorial03.php' => 'Writing your first Django app (part 3)',
    'tutorial04.php' => 'Writing your first Django app (part 4)',
    'hello05.php' => 'Hello world / sessions',
    'guess.php' => 'A guessing game',
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

$baseUrl = "http://localhost:9000";
?>
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">AutoGrader</a></li>
    <li><a href="#tabs-2">Instructions</a></li>
    <li><a href="#tabs-3">Result Log</a></li>
  </ul>
  <div id="tabs-1">
<p>
Url to test:
<input type="text" name="baseurl" style="width:60%;" value="<?= $baseUrl ?>"
/>
</p>
<p>
<button onclick="doNextStep();" id="nextjson" disabled>Run Next Step:</button>
<span id="stepinfo">
Placeholder
</span>
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
  <div id="tabs-2">
Instructions go here.
  </div>
  <div id="tabs-3">
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

function advanceStep(responseObject) {

    const bodystr = JSON.stringify({
          step: currentStep,
          response: responseObject,
    });

    console.log("Body ", bodystr);

    fetch('<?php echo(addSession('fw_grader.php')) ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: bodystr,
      })
      .then(response => response.json())
      .then(data => {
        // Handle the response data
        addResultLog("Completed");
        console.log('Next Step', data);
        currentStep = data;
        currentStepCount += 1;
        addResultLog("Ready");
        document.getElementById('stepinfo').textContent = data.message;

     })
     .catch(error => {
       // Handle any errors
       console.error('Error:', error);
     });
}

function addResultLog(message) {
    const resultId = "result_" + currentStepCount;
    if ( $('#'+resultId).length == 0 ) {
        $("#resultlog").append("<li id=\""+resultId+"\">last item</li>");
    }
    const result = message + ": " + currentStep.message;
    document.getElementById(resultId).textContent = result;
}

window.addEventListener(
  "message",
  (event) => {
    console.log('in parent', event, currentStep);

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
    console.log(currentStep)
    if ( currentStep.command == 'switchurl' ) {
            currentUrl = (baseurl + currentStep.text);
            console.log('Switching to', currentUrl);
            document.getElementById('myframe').src = currentUrl;
            document.getElementById('currentUrl').textContent = currentUrl;
            advanceStep({"text": "success"});
            addResultLog("Complete");
            return;
    }
    console.log('Sending...', currentStep, currentUrl);
    addResultLog("In Progress");
    document.getElementById('myframe').contentWindow.postMessage(currentStep, currentUrl);
    console.log('sent...');
}

console.log("loading the first step");
// Get the first currentStep
fetch('<?php echo(addSession('fw_grader.php')) ?>')
    .then(response => response.json())
    .then(step => {
        console.log('First step', step)
        currentStep = step;
        addResultLog("Ready");
        document.getElementById('nextjson').disabled = false;
        document.getElementById('stepinfo').textContent = step.message;
        document.getElementById('currentUrl').textContent = baseurl;
    });



</script>
<?php

$OUTPUT->footerEnd();

