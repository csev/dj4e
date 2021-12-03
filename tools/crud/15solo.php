<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";

use Goutte\Client;

$code = $USER->id+$CONTEXT->id;
$timecode = $code;
$timecode = $timecode + intval((time() / (60*60*24)));

$user1account = 'dj4e_user1';
$user1pw = "Meow_" . substr(getMD5(),1,6). '_41';

$now = date('H:i:s');

$code = 'timecode_'.substr($timecode.'', 1, 6);
$reverse = ($timecode % 2 == 0 ) ? 'yes' : 'no';
$cases = array('none', 'upper', 'casefold', 'title');
$case = $cases[($timecode/100) % count($cases)];

$field1 = 'Hello world';
$field2 = $names[$timecode % count($names)] .' ' . ($timecode % 100);

$sample = "http://localhost:8000/solo";
$sample = "https://chucklist.dj4e.com/solo";

// Compute result
$result = trim($field1) . ' ' . trim($field2);
if ($reverse == 'yes') $result = strrev($result);

if ( $case == 'upper' ) $result = strtoupper($result);
else if ( $case == 'casefold' ) $result = strtolower($result);
else if ( $case == 'title' ) $result = ucwords($result);

?>
<form method="post" action="<?= $sample ?>/launch/" target="_blank">
<input type="hidden" name="code" value="<?= $code ?>">
<input type="hidden" name="case" value="<?= $case ?>">
<input type="hidden" name="reverse" value="<?= $reverse ?>">
Your Assignment: <input type="submit" value="Replicate this application">
</form>
<p>
The application that you need to implement will change every 24 hours in small ways so you need to look carefully at
the set of steps in the sample application.  The changes will only be the time code calue and how you concatenate the 
two strings. The rest of the application will be unchanged.  You can log into the sample application using:
<pre>
dj4e_user1 / Meow_81e728_41
</pre>
<p>
<b>Your</b> application must require a log in before it can be used.  The auto grader
will use the following account to log in to your application:
<pre>
<?= htmlentities($user1account) ?> / <?= htmlentities($user1pw) ?>  
</pre>
<?php
$url = getUrl($sample);
if ( $url === false ) return;

webauto_check_test();
$passed = 0;

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);
$client->getClient()->setSslVerification(false);

// Start the actual test
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'Login', 'Login Locally');
webauto_change_form($form, 'username', $user1account);
webauto_change_form($form, 'password', $user1pw);

// $crawler = $client->submit($form);
$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);

if ( webauto_dont_want($html, "Your username and password didn't match. Please try again.") ) return;

webauto_dont_want($html, "Your result is");

$form = webauto_get_form_with_button($crawler,'Submit', 'Submit Query');
webauto_change_form($form, 'field1', $field1);
webauto_change_form($form, 'field2', $field2);

// $crawler = $client->submit($form);
$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);

webauto_search_for($html, "Your result is");
webauto_search_for($html, $code, false);
webauto_search_for($html, $result, false);


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 7;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

