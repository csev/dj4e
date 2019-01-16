<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

$check = webauto_get_check_full();

$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ', ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";
$meta = '<meta name="wa4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
line_out("Exploring DJango Views (MDN)");
?>
<a href="../../assn/paw_home.md" target="_blank">
https://www.dj4e.com/assn/paw_home.md</a>
</a>
</p>
<?php
?>
</p>
As part of this assignment, you must <b>remove</b> the `dj4e` superuser or change its password.
This assignment will mark points off if it <i>can</i> log in to superuser account.
<pre>
Account: dj4e
</pre>
</p>
<p>
You need to add the following line to your <b>base_generic.html</b> file within the 
<b>&lt;head&gt;</b> area:
<pre>
<?= htmlentities($meta) ?>
</pre>
Make sure to put this all on one line and with no extra spaces within the tag.
</p>
<?php

$url = getUrl('http://mdntutorial.pythonanywhere.com/');
if ( $url === false ) return;
$passed = 0;

$admin = $url . 'admin';
$catalog_url = $url . 'catalog';
$css_url = $url . 'catalog/static/css/styles.css';

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

line_out('Checking to make sure we cannot log into the /admin url');
$crawler = webauto_get_url($client, $admin);
$html = webauto_get_html($crawler);

// line_out('Looking for the form with a value="Log In" submit button');
$form = webauto_get_form_with_button($crawler,'Log in');
webauto_change_form($form, 'username', 'dj4e');
webauto_change_form($form, 'password', $adminpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( strpos($html,'Log in') > 0 ) {
    line_out('Congratulations, it looks like you have deleted the superuser account with dj4e / '.$adminpw);
    $passed += 10;
} else {
    error_out('Oops! It looks like you forgot to delete or change the password on the superuser account with dj4e / '.$adminpw);
    error_out('Ten point score deduction');
}

// Start the actual test
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);

webauto_search_for($html, 'Dynamic content');
webauto_search_for($html, 'All books');
webauto_search_for($html, 'All authors');

if ( strpos($html, 'Mozilla Developer Network') > 0 ) {
    error_out('It looks like you left in the default name for the developer of the application.');
    error_out('Two point score deduction');
} else {
    success_out('It looks like you fixed the default name for the developer of the application.');
    $passed += 2;
}

line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    error_out('Assignment will not be scored.');
    $passed = -1000;
} else {
    success_out('Found the appropriate <meta> tag');
    $passed += 2;
}

// Checking if a later tutorial is already working
$books_url = webauto_get_href_url($crawler,'All books');

if ( strpos($books_url, 'catalog/books') > 0 ) {
    error_out('It looks like your "All books" link from a future graded exercise is already working..');
    error_out('10 point deduction...');
    $passed -= 10;
}

// Make sure static is set up properly
line_out("Checking to see if you are serving your CSS files properly...");
$crawler = webauto_get_url($client, $css_url);
$response = $client->getResponse();
$status = $response->getStatus();
if ( $status != 200 ) {
    error_out("Could not load $css_url, make sure you are serving your static files status=$status");
    return;
} else {
    success_out("Loaded $css_url");
    $passed += 1;
}


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
$perfect = 21;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

