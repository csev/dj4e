<?php

require_once "../crud/webauto.php";
require_once "names.php";

$code = $USER->id+$CONTEXT->id;
$check = webauto_get_check_full();


$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ' ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";
$meta = '<meta name="dj4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
line_out("Exploring Django Views (MDN)");
?>
<a href="../../assn/mdn/paw_details.md" target="_blank">
https://www.dj4e.com/assn/mdn/paw_details.md</a>
</a>
</p>
<?php
?>
</p>
As part of this assignment, you must not have the `dj4e` superuser or change its password.
This assignment will mark points off if it <i>can</i> log in to superuser account.
<pre>
Account: dj4e
</pre>
</p>
<p>
You should still have the meta tag, an author and book from the previous tutorial:
</p>
<pre>
<?= htmlentities($meta) ?> 
</pre>
<pre>
Author: <?= htmlentities($full_name) ?> 
Book: <?= htmlentities($book_title) ?>
</pre>
</p>

<?php

$url = getUrl('https://mdntutorial.pythonanywhere.com/');
if ( $url === false ) return;
$passed = 0;

webauto_check_test();

$admin = $url . 'admin';
$catalog_url = $url . 'catalog';
$css_url = $url . 'static/css/styles.css';

webauto_setup();

line_out('Checking to make sure we cannot log into the /admin url');
$crawler = webauto_get_url($client, $admin);
$html = webauto_get_html($crawler);

// line_out('Looking for the form with a value="Log In" submit button');
$form = webauto_get_form_with_button($crawler,'Log in');
webauto_change_form($form, 'username', 'dj4e');
webauto_change_form($form, 'password', $adminpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( ! webauto_testrun($url) ) {
if ( strpos($html,'Log in') > 0 ) {
    line_out('Congratulations, it looks like you have deleted the superuser account with dj4e / '.$adminpw);
    $passed += 10;
} else {
    error_out('Oops! It looks like you forgot to delete or change the password on the superuser account with dj4e / '.$adminpw);
    error_out('Ten point score deduction');
}
}

// Start the actual test
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);

$home_url = webauto_get_url_from_href($crawler,'Home');
$books_url = webauto_get_url_from_href($crawler,'All books');
$authors_url = webauto_get_url_from_href($crawler,'All authors');

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
}

if ( ! webauto_testrun($url) && strpos($html, 'You have visited this page') > 0 ) {
    error_out('It looks like you already have some code running from a future version of this application.');
    error_out('Ten point score deduction for going more than 88 miles per hour');
    $passed = $passed -10;
}

// Make sure static is set up properly
line_out("Checking to see if you are serving your CSS files properly...");
$crawler = webauto_get_url($client, $css_url);
$response = $client->getResponse();
$status = $response->getStatusCode();
if ( $status != 200 ) {
    error_out("Could not load $css_url, make sure you are serving your static files status=$status");
    return;
} else {
    success_out("Loaded $css_url");
    $passed += 1;
}

$must_do_challenge = true;

line_out('Retrieving book list page...');
$crawler = webauto_get_url($client, $books_url);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);
$book_detail_url = webauto_get_url_from_href($crawler,$book_title);

line_out('Retrieving book detail page...');
$crawler = webauto_get_url($client, $book_detail_url);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);

if ( $must_do_challenge ) {
line_out('Retrieving author list page...');
$crawler = webauto_get_url($client, $authors_url);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $last_first);
$author_detail_url = webauto_get_url_from_href($crawler,$last_first);

line_out('Retrieving author detail page...');
$crawler = webauto_get_url($client, $author_detail_url);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $last_first);
$back_to_book_url = webauto_get_url_from_href($crawler,$book_title);

line_out('Retrieving book detail page from author page...');
$crawler = webauto_get_url($client, $back_to_book_url);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);
} // End must_do_challenge


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$passed += 1;
if ( $must_do_challenge ) {
	$perfect = 30;
} else {
	$perfect = 24;
}

if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

