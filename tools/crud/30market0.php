<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";
require_once "../crud/ad_titles.php";

use \Tsugi\Util\U;

$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();
// HACK $check = "1679091c5a880faf6fb5e6087eb1b2dc";

$meta = '<meta name="dj4e" content="'.$check.'">';

$user1account = 'dj4e_user1';
$user1pw = "Meow_" . substr(getMD5(),1,6). '_41';
$user2account = 'dj4e_user2';
$user2pw = "Meow_42_" . substr(getMD5(),1,6);
$ad_title = $ad_titles[($code+1) % count($ad_titles)];

// HACK $user1pw = "Meow_679091_41";
// HACK $user2pw = "Meow_42_679091";

$now = date('H:i:s');

line_out("Building Marketplace - Initial Setup");

?>
Specification:
<a href="../../assn/dj4e_mkt0.md" class="btn btn-info" target="_blank">
https://www.dj4e.com/assn/dj4e_mkt0.md</a>
</a>
<p>
You need to edit your <b>settings.py</b> file and add the following line:
<pre>
DJ4E_CODE = '<?= $check ?>'
</pre>
</p>
<?php
$url = getUrl('https://market.dj4e.com/');
if ( $url === false ) return;
warn_about_ngrok($url);

webauto_check_test();
$testrun = webauto_testrun($url);
warn_about_testrun($url);

$base_url = U::get_base_url($url);

$passed = 0;

webauto_setup();

// --
$newurl = webauto_append_suffix($base_url, "/home");
$crawler = webauto_get_url($client, $newurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

// webauto_dump_html($html);

webauto_search_for($html, "Welcome");

// Debug
// $testrun = false;

if ( !$testrun && webauto_dont_want($html, "Chuck's Marketplace") ) {
   error_out("Make sure to change APP_NAME in settings.py to your own application name");
}

if ( !$testrun && webauto_dont_want($html, "reference implementation") ) {
   error_out("You should folow the assignment instructions versus reverse engineering the assignment from the sample solution output");
}

if ( ! webauto_search_for($html, "django.db.backends.mysql") ) {
   error_out("Make sure to switch from SQLite to MySQL in your settings.py");
}

if ( !$testrun && ! webauto_search_for($html, $check) ) {
   error_out("Add the DJ4E_CODE value to settings.py as descried above");
}

// Make sure the page is also available at /
$crawler = webauto_get_url($client, $base_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "Welcome");

// --------
$newurl = webauto_append_suffix($base_url, "/admin");

$crawler = webauto_get_url($client, $newurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "Django administration");
webauto_search_for($html, "Username");


// --------
$newurl = webauto_append_suffix($base_url, "/accounts/login");

$crawler = webauto_get_url($client, $newurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "Username");
webauto_search_for($html, "csrfmiddlewaretoken");
webauto_search_for($html, "bootstrap");

// --------
line_out(" ");
line_out("Accesing an incorrect url to generate a 404...");

$newurl = webauto_append_suffix($base_url, "/missing");

$crawler = webauto_get_url($client, $newurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
$response = $client->getResponse();
$status = $response->getStatusCode();

if ( $status == 404 ) {
    success_out("For this page, getting 'Page may have errors, HTTP status=404' is expected behavior. Nice job!");
    $passed++;
} else {
    error_out("Accessing the '/missing' url did *not* generate a 404 error");
}

webauto_search_for($html, "Page not found");
webauto_search_for($html, "home/");
webauto_search_for($html, "admin/");
webauto_search_for($html, "accounts/");
webauto_search_for($html, "oauth/");
webauto_search_for($html, "site");
webauto_search_for($html, "favicon.ico");
webauto_search_for($html, "config.urls");

// TODO: Check for speed of light

// -------
$perfect = $passed + $failed;
if ( $passed < 0 ) $passed = 0;

line_out(' ');
line_out("Raw score: passed=$passed failed=$failed");

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

