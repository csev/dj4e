<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";

use Goutte\Client;

$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();

$meta = '<meta name="wa4e" content="'.$check.'">';

$user1account = 'dj4e_user1';
$user1pw = "Meow_" . substr(getMD5(),1,6). '_41';
$user2account = 'dj4e_user2';
$user2pw = "Meow_42_" . substr(getMD5(),1,6);

$now = date('H:i:s');

line_out("Building Classified Ad Site #1");

?>
<a href="../../assn/dj4e_ads1.md" target="_blank">
https://www.dj4e.com/assn/dj4e_ads1.md</a>
</a>
<p>
Create two users (they do not have to be superusers):
<pre>
<?= htmlentities($user1account) ?> / <?= htmlentities($user1pw) ?>  
<?= htmlentities($user2account) ?> / <?= htmlentities($user2pw) ?>
</pre>
You should have this <b>meta</b> tag in then <b>&lt;head&gt;</b> of each page:
<pre>
<?= htmlentities($meta) ?>
</pre>
</p>
<?php
$url = getUrl('https://chucklist.dj4e.com/');
if ( $url === false ) return;

webauto_check_test();
$passed = 0;

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

// Start the actual test
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
$meta_good = true;
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    error_out('Assignment will not be scored.');
    $meta_good = false;
}
$login_url = webauto_get_url_from_href($crawler,'Login');

$crawler = webauto_get_url($client, $login_url, "Logging in as $user1account");
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'Login Locally');
webauto_change_form($form, 'username', $user1account);
webauto_change_form($form, 'password', $user1pw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( webauto_dont_want($html, "Your username and password didn't match. Please try again.") ) return;

// Cleanup old ads
$saved = $passed;
// preg_match_all("'/ad/[0-9]+/delete'",$html,$matches);
preg_match_all("'\"([a-z0-9/]*/[0-9]+/delete)\"'",$html,$matches);
// echo("\n<pre>\n");var_dump($matches);echo("\n</pre>\n");

if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
    foreach($matches[1] as $match ) {
        $crawler = webauto_get_url($client, $match, "Loading delete page for old record");
        $html = webauto_get_html($crawler);
        $form = webauto_get_form_with_button($crawler,'Yes, delete.');
        $crawler = $client->submit($form);
        $html = webauto_get_html($crawler);
    } 
}
$passed = $saved;

$create_ad_url = webauto_get_url_from_href($crawler,"Create Ad");
$crawler = webauto_get_url($client, $create_ad_url, "Retrieving create ad page...");
$html = webauto_get_html($crawler);

if ( ! webauto_search_for_not($html, "owner") ) {
    error_out('The owner field is not supposed to appear in the create form.');
    return;
}

// Add a record
$title = 'HHGTTG_41 '.$now;
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'title', $title);
webauto_change_form($form, 'price', '0.41');
webauto_change_form($form, 'text', 'Low cost Vogon poetry.');

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( ! webauto_search_for($html, $title) ) {
    error_out('Tried to create a record and cannot find the record in the list view');
    return;
}

// Look for the edit entry
// preg_match_all("'/ad/[0-9]+/update'",$html,$matches);
preg_match_all("'\"([a-z0-9/]*/[0-9]+/update)\"'",$html,$matches);
if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
    if ( count($matches[1]) != 1 ) {
        error_out("Expecting exactly one update url like /ad/nnn/update");
        return;
    }
    $match = $matches[1][0];
    $crawler = webauto_get_url($client, $match, "Loading edit page for old record");
    $html = webauto_get_html($crawler);
    $form = webauto_get_form_with_button($crawler,'Submit');
    webauto_change_form($form, 'title', $title."_updated");
    $crawler = $client->submit($form);
    $html = webauto_get_html($crawler);
    webauto_search_for($html,$title."_updated");
} else {
    error_out("Could not find update url of the form /ad/nnn/update");
    return;
}

$logout_url = webauto_get_url_from_href($crawler,'Logout');
$crawler = webauto_get_url($client, $logout_url, "Logging out...");
$html = webauto_get_html($crawler);

success_out("Completed first user, moving to second user...");

// Do it again with the second user

$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

require("meta_check.php");

$login_url = webauto_get_url_from_href($crawler,'Login');


$crawler = webauto_get_url($client, $login_url, "Logging in as $user2account");
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'Login Locally');
webauto_change_form($form, 'username', $user2account);
webauto_change_form($form, 'password', $user2pw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( webauto_dont_want($html, "Your username and password didn't match. Please try again.") ) return;

// Cleanup old ads
$saved = $passed;
// preg_match_all("'/ad/[0-9]+/delete'",$html,$matches);
preg_match_all("'\"([a-z0-9/]*/[0-9]+/delete)\"'",$html,$matches);
if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
    foreach($matches[1] as $match ) {
        $crawler = webauto_get_url($client, $match, "Loading delete page for old record");
        $html = webauto_get_html($crawler);
        $form = webauto_get_form_with_button($crawler,'Yes, delete.');
        $crawler = $client->submit($form);
        $html = webauto_get_html($crawler);
    } 
}
$passed = $saved;

$create_ad_url = webauto_get_url_from_href($crawler,"Create Ad");
$crawler = webauto_get_url($client, $create_ad_url, "Retrieving create ad page...");
$html = webauto_get_html($crawler);

// Use the create ad form
$title = 'HHGTTG_42 '.$now;
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'title', $title);
webauto_change_form($form, 'price', '0.42');
webauto_change_form($form, 'text', 'Towels - guaranteed to impress Vogons.');

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

// Look for the edit entry
// preg_match_all("'/ad/[0-9]+/update'",$html,$matches);
preg_match_all("'\"([a-z0-9/]*/[0-9]+/update)\"'",$html,$matches);
// echo("\n<pre>\n");var_dump($matches);echo("\n</pre>\n");
if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
    if ( count($matches[1]) != 1 ) {
        error_out("Expecting exactly one update url like /ad/nnn/update");
        return;
    }
    $match = $matches[1][0];
    $crawler = webauto_get_url($client, $match, "Loading edit page for old record");
    $html = webauto_get_html($crawler);
    $form = webauto_get_form_with_button($crawler,'Submit');
    webauto_change_form($form, 'title', $title."_updated");
    $crawler = $client->submit($form);
    $html = webauto_get_html($crawler);
    webauto_search_for($html,$title."_updated");
} else {
    error_out("Could not find update url of the form /ad/nnn/update");
    return;
}

$logout_url = webauto_get_url_from_href($crawler,'Logout');
$crawler = webauto_get_url($client, $logout_url, "Logging out...");
$html = webauto_get_html($crawler);

// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 19;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( ! $meta_good ) {
    error_out("Not graded - missing meta tag");
    return;
}
if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

