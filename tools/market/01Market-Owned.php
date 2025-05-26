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

$user1account = 'dj4e_user1';
$user1pw = "Meow_" . substr(getMD5(),1,6). '_41';
$user2account = 'dj4e_user2';
$user2pw = "Meow_42_" . substr(getMD5(),1,6);
$ad_title = $ad_titles[($code+1) % count($ad_titles)];

// HACK $user1pw = "Meow_679091_41";
// HACK $user2pw = "Meow_42_679091";

$now = date('H:i:s');

line_out("Building MarketPlace with Owned Rows");

?>
Specification:
<a href="../../assn/dj4e_ads1.md" class="btn btn-info" target="_blank">
https://www.dj4e.com/assn/dj4e_ads1.md</a>
</a>
<p>
Create two non-super users, by logging into the <b>/admin</b> URL of your application
using a superuser account:
<?php
print_user_and_password($user1account, $user1pw, $user2account, $user2pw);
?>
You should have this <b>meta</b> tag in the <b>&lt;head&gt;</b> of each page:
<pre>
<?= htmlentities($meta) ?>
</pre>
</p>
<p>
Before you run this autograder, you should log into your application using your <b>administrator</b> account and manually add
a classified ad with this as its title (case matters):
<pre>
<?php
echo($ad_title);
?>
</pre>
The autograder will not run unless it sees an ad with the above title in
the initial list of ads after it logs in.
Don't use either of the above accounts to add the ad or it will be deleted at the beginning of each run.
</p>
<?php
$url = getUrl('https://market.dj4e.com/m1');
if ( $url === false ) return;
warn_about_ngrok($url);

webauto_check_test();
$testrun = webauto_testrun($url);
if ( str_starts_with($testrun, "http://localhost:8000") ) $testrun = false;

$passed = 0;
$failed = 0;

webauto_setup();

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

webauto_search_for_menu($html);
$login_url = webauto_get_url_from_href($crawler,'Login');

$crawler = webauto_get_url($client, $login_url, "Logging in as $user1account");
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'Login', 'Login Locally');
webauto_change_form($form, 'username', $user1account);
webauto_change_form($form, 'password', $user1pw);

$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);
webauto_search_for_menu($html);

if ( webauto_dont_want($html, "Your username and password didn't match. Please try again.") ) return;

// First, check if there is a manually entered title in the ad list
// unless of course this is a test run...
if ( ! webauto_testrun($url) ) {
    line_out('Looking for your manually created entry: '.$ad_title);

    if ( ! webauto_search_for($html, $ad_title) ) {
        error_out('Could not find an ad with a title of: '.$ad_title);
        error_out('Auto grading will not continue until you manually add a title as described above.');
        error_out('');
        return;
    }

    // Check the detail page
    $detail_url = webauto_get_url_from_href($crawler,$ad_title, "(Could not link to the detail page on the list view)");
    $crawler_detail = webauto_get_url($client, $detail_url, "Loading detail page");
    $html_detail = webauto_get_html($crawler_detail);
    if ( ! webauto_search_for($html_detail, $ad_title) ) {
        error_out("Did not find '$ad_title' on detail page");
        return;
    }
    if ( ! webauto_search_for($html_detail, 'Price', true) ) {
        error_out("Did not find price on detail page");
        return;
    }
    if ( ! webauto_search_for_not($html_detail, "owner") ) {
        error_out('The owner field is not supposed to appear in the detail form.');
        return;
    }

    line_out("Congratulations your manually created entry looks good!");

    $crawler = webauto_get_url($client, $url, "Going from the detail page to the ad list view to start the autograder");
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    webauto_search_for_menu($html);

} /* End if ( webauto_testrun() ) */


if ( ! market_delete_old($client, $url, $check, $testrun) ) return;

$crawler = webauto_get_url($client, $url, "Retrieving the main list url");
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

$create_ad_url = webauto_get_url_from_href($crawler,"Create Ad");
$crawler = webauto_get_url($client, $create_ad_url, "Retrieving create ad page...");
$html = webauto_get_html($crawler);
webauto_search_for_menu($html);

// Use the create ad form
$title = 'HHGTTG_42 '.$now;
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'title', $title);
webauto_change_form($form, 'price', '0.42');
webauto_change_form($form, 'text', 'Towels - guaranteed to impress Vogons.');

$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);
webauto_search_for_menu($html);

// Look for the edit entry
line_out("Looking through the main view to update the ad that User 2 just created");
// preg_match_all("'/ad/[0-9]+/update'",$html,$matches);
preg_match_all("'\"([a-z0-9/]*/[0-9]+/update[^\"]*)\"'",$html,$matches);
// echo("\n<pre>\n");var_dump($matches);echo("\n</pre>\n");
if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
    if ( count($matches[1]) != 1 ) {
        error_out("Expecting User 2 to have an update link for item that was just created with a url like /ad/nnn/update - found ".count($matches[1]));
        return;
    }
    $match = $matches[1][0];
    $crawler = webauto_get_url($client, $match, "Loading edit page for old record");
    $html = webauto_get_html($crawler);
    $form = webauto_get_form_with_button($crawler,'Submit');
    webauto_change_form($form, 'title', $title."_updated");
    $crawler = webauto_submit_form($client, $form);
    $html = webauto_get_html($crawler);
    webauto_search_for($html,$title."_updated");
} else {
    error_out("Could not find an update link for the item that User 2 just created with a url like /ad/nnn/update - found ".count($matches[1]));
    return;
}

$logout_form = webauto_get_form_with_button($crawler,'Logout', 'Logout Locally');
$crawler = webauto_submit_form($client, $logout_form);
$html = webauto_get_html($crawler);
webauto_search_for_menu($html);


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = $passed + $failed;
if ( $passed < 0 ) $passed = 0;

line_out(' ');
line_out("Raw score: passed=$passed failed=$failed");

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( is_string($nograde) ) {
    error_out("Not graded - ".$nograde);
    return;
}
if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

