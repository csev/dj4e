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
$full_name = $first_name . ' ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";
$meta = '<meta name="wa4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
line_out("Exploring DJango Views (MDN)");
?>
<a href="../../assn/paw_sessions.md" target="_blank">
https://www.dj4e.com/assn/paw_sessions.md</a>
</a>
</p>
<?php
?>
</p>
<p>
You need to add the following line to your <b>base_generic.html</b> file within the
<b>&lt;head&gt;</b> area:
<pre>
<?= htmlentities($meta) ?>
</pre>
Make sure to put this all on one line and with no extra spaces within the tag.  This should stop
changing from now on. (sorry).
</p>
<!--
<p>
You should still have the identifiying <b>meta</b> tag in your <b>&lt;head&gt;</b> area and an author and book from the previous tutorial autograder.
</p>
-->

<?php

$url = getUrl('http://mdntutorial.pythonanywhere.com/');
if ( $url === false ) return;
$passed = 0;

webauto_check_test();

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

if ( ! webauto_testrun($url) ) {
if ( strpos($html,'Log in') > 0 ) {
    line_out('Congratulations, it looks like you have deleted the superuser account with dj4e / '.$adminpw);
    $passed += 8;
} else {
    error_out('Oops! It looks like you forgot to delete or change the password on the superuser account with dj4e / '.$adminpw);
    error_out('Eight point score deduction');
}
}

// Start the actual test
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

$home_url = webauto_get_url_from_href($crawler,'Home');
$books_url = webauto_get_url_from_href($crawler,'All books');
$authors_url = webauto_get_url_from_href($crawler,'All authors');

$retval = webauto_search_for_not($html, 'Mozilla Developer Network');

line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    error_out('Assignment will not be scored.');
    $passed = -1000;
} else {
    success_out('Found the appropriate <meta> tag');
}

line_out('Checking for session support');
$retval = webauto_search_for($html, 'You have visited this page 0 times.');
$retval = webauto_search_for_not($html, 'You have visited this page 1 time.');

line_out('Re-retrieve the catalog page..');
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
line_out('Checking for session support');
$retval = webauto_search_for_not($html, 'You have visited this page 0 times.');
$retval = webauto_search_for($html, 'You have visited this page 1 time.');

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


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 30;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

