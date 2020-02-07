<?php
require_once "../config.php";

use \Tsugi\Core\LTIX;
use \Tsugi\UI\Lessons;

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

// Note - cannot have any output before setcookie
$code = md5($USER->id+$LINK->id+$CONTEXT->id);
$code = substr($code, 0, 5);
if ( ! isset($_COOKIE['dj4e_secret_cookie']) ) {
    setcookie('dj4e_secret_cookie', $code, time()+3600, '/');
}

$oldgrade = $RESULT->grade;

if ( isset($_POST['cookie']) ) {
    $_SESSION['post_data'] = $_POST;
    $error = '';
    $score = 0;
    if ( $_POST['cookie'] == $code ) {
        $score += 0.5;
    } else {
        $error = 'Incorrect cookie value';
        // $error .=  '(' . $_POST['cookie'] . ' | ' . $code . ')';
    }

    if ( ! isset($_COOKIE['dj4e_destroy_cookie'])) {
        $score += 0.5;
    } else {
        if ( strlen($error) > 0 ) $error .= ' / ';
        $error .= 'You must remove dj4e_destroy_cookie every time before you press "Submit"';
    }

    $RESULT->gradeSend($score);
    if ( $score >= 1.0 ) {
        $_SESSION['success'] = 'Assignment completed';
    } else {
        $_SESSION['error'] = $error . ' Score=' . $score;
    }
    
    header('Location: '.addSession('index.php'));
    return;
}

setcookie('dj4e_destroy_cookie', '42', time()+3600, '/');

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

?>
<!-- Rendered using old school MVC index.php -->
<p>
<b>Finding and Deleteing Cookies in a Haystack</b>
</p>
<?php

if ( ! $USER->displayname || $USER->displayname == '' ) {
    echo('<p style="color:blue;">Auto grader launched without a student name.</p>'.PHP_EOL);
} else {
    $OUTPUT->welcomeUserCourse();
}

$oldcookie = '';
$oldsession = '';
if ( isset($_SESSION['post_data']) ) {
    if ( isset($_SESSION['post_data']['cookie']) ) $oldcookie = $_SESSION['post_data']['cookie'];
    if ( isset($_SESSION['post_data']['session']) ) $oldsession = $_SESSION['post_data']['session'];
}
unset($_SESSION['post_data']);

?>
<p>
This application has stored two cookies in your browser.  You need to use "Developer
Mode" in your browser to complete this assignment:
<ul>
<li>You need to find the value of the cookie named <b>dj4e_secret_cookie</b> and enter it below</li>
<li>You need to find and delete the cookie named <b>dj4e_destroy_cookie</b> <i>before</i> pressing "Submit" below.
You need to delete this cookie repeatedly because this auto grader will set it on <i>every</i> GET request.</li>
</ul>
</p>
<p>
<form method="post">
<p>
<label for="cookie">Enter Secret Cookie Value: </label>
<input type="text" id="cookie" name="cookie" size="40"
value="<?= htmlentities($oldcookie) ?>"/></p>
<input type="submit">
</form>
</p>
<?php
if ( $USER->instructor ) {
echo("\n<hr/>");
echo("\n<pre>\n");
print_r($_COOKIE);
echo("\n");
echo("\n</pre>\n");
}
$OUTPUT->footer();


