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
<h1>JavaScript Autograder</h1>

<script>
var baseurl = "<?= $baseUrl ?>";
</script>

Url to test:
<input type="text" name="baseurl" style="width:60%;" value="<?= $baseUrl ?>"
/></br>

<button onclick="doNextStep();" id="nextjson" disabled>Run Next Step:</button>
<span id="stepinfo">
Placeholder
</span>

<br/>
<center>
<iframe style="width:95%; height:600px;" id="myframe" src="<?= $baseUrl ?>">
</iframe>
</center>

<?php

$OUTPUT->footerStart();

?>

<script>
var currenturl = baseurl;

var currentStep;

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
        console.log('Next Step', data);
        currentStep = data;
        document.getElementById('stepinfo').textContent = data.message;

     })
     .catch(error => {
       // Handle any errors
       console.error('Error:', error);
     });
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
    currenturl = baseurl;
    document.getElementById('myframe').src = currenturl;
    advanceStep({"text": "success"});
}

function doNextStep() {
    console.log(currentStep)
    if ( currentStep.command == 'switchurl' ) {
            currenturl = (baseurl + currentStep.text);
            console.log('Switching to', currenturl);
            document.getElementById('myframe').src = currenturl;
            advanceStep({"text": "success"});
            return;
    }
    console.log('Sending...', currentStep, currenturl);
    document.getElementById('myframe').contentWindow.postMessage(currentStep, currenturl);
    console.log('sent...');
}

console.log("loading the first step");
// Get the first currentStep
fetch('<?php echo(addSession('fw_grader.php')) ?>')
    .then(response => response.json())
    .then(step => {
        console.log('First step', step)
        currentStep = step;
        document.getElementById('nextjson').disabled = false;
        document.getElementById('stepinfo').textContent = step.message;
    });



</script>
<?php

$OUTPUT->footerEnd();

