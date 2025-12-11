<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";
require_once "../crud/ad_titles.php";
require_once "market-util.php";

use \Tsugi\Util\U;

$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();
// HACK $check = "1679091c5a880faf6fb5e6087eb1b2dc";

$meta = '<meta name="dj4e" content="'.$check.'">';

$creds = market_get_user_credentials($code);
$user1account = $creds['user1account'];
$user1pw = $creds['user1pw'];
$user2account = $creds['user2account'];
$user2pw = $creds['user2pw'];
$ad_title = market_get_ad_title($code, $ad_titles);

// HACK $user1pw = "Meow_679091_41";
// HACK $user2pw = "Meow_42_679091";

$now = date('H:i:s');

echo("<h2>Building Marketplace - Initial Setup</h2>\n");

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
echo('<div id="disappear-start"></div>'."\n");
warn_about_ngrok($url);

webauto_check_test();
warn_about_testrun($url);

$testrun = webauto_testrun($url);
if ( str_starts_with($testrun, "http://localhost:8000") ) $testrun = false;

$base_url = U::get_base_url($url);

$passed = 0;

webauto_setup();

// Check the /home, /admin, /accounts/login, and 404 pages
market_check_basics($client, $base_url, $check, $testrun);

// Make sure the page is also available at / for this first autograder
$crawler = webauto_get_url($client, $base_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "Welcome");

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

