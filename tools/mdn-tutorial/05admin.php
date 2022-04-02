<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

// Use link-independent code
$code = $USER->id+$CONTEXT->id;

$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ', ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";

$adminuser = 'dj4e';
$adminpw = substr(getMD5(),4,9);

line_out("Exploring Django Admin (MDN)");
?>
<a href="../../assn/mdn/paw_admin.md" target="_blank">
https://www.dj4e.com/assn/mdn/paw_admin.md</a>
</a>
</p>
<p>
In addition to the steps in the tutorial, make a second admin user to allow
this autograder to log in and check your work with the following information:
<pre>
Account: <?= htmlentities($adminuser) ?> 
Password: <?= htmlentities($adminpw) ?>
</pre>
You can use any email address you like.
</p>
<p>
Make an Author with the following name (first, last):<br/>
<pre>
Author: <?= htmlentities($full_name) ?> 
Book: <?= htmlentities($book_title) ?> 
</pre>
You can use any values you like for the Summary, ISBN, and Genre fields.
</p>
<?php


$url = getUrl('http://mdntutorial.pythonanywhere.com/admin');
if ( $url === false ) return;
$passed = 0;

webauto_check_test();

$admin = $url;
error_log("Tutorial02 ".$url);

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$catalog_url = str_replace('admin', 'catalog', $admin);
line_out('Checking to make tutorials are being done in the correct order.');
line_out('Checking to make sure the /catalog url still returns a 404 error page');
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, 'Using the URLconf defined in <code>locallibrary.urls</code>');
if ( $retval === true ) {
    $passed += 9;
} else {
    error_out('It looks like you have submitted a website with a later tutorial already completed.');
}


$crawler = webauto_get_url($client, $admin);
$html = webauto_get_html($crawler);

// line_out('Looking for the form with a value="Log In" submit button');
$form = webauto_get_form_with_button($crawler,'Log in');
webauto_change_form($form, 'username', $adminuser);
webauto_change_form($form, 'password', $adminpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( strpos($html,'Log in') > 0 ) {
    error_out("It looks like you have not yet set up the admin account with $adminuser / $adminpw");
    error_out('The test cannot be continued');
    return;
} else {
    line_out("Login successful...");
}

// Grab the urls to the various links
$catalog_url = webauto_get_url_from_href($crawler,'Catalog');
$authors_url = webauto_get_url_from_href($crawler,'Authors');
$books_url = webauto_get_url_from_href($crawler,'Books');
$instance_url = webauto_get_url_from_href($crawler,'Book instances');

// Load the catalog page
$crawler = webauto_get_url($client, $catalog_url);
$html = webauto_get_html($crawler);

markTestPassed('Catalog admin page retrieved');

// Load the authors page
$crawler = webauto_get_url($client, $authors_url);
$html = webauto_get_html($crawler);

markTestPassed('Authors admin page retrieved');

line_out("Looking for '$last_name and $first_name'");
if ( strpos($html,$last_name) < 1 && strpos($html,$first_name) < 1 ) {
    error_out('It looks like you have not created an author named');
    error_out($full_name);
} else {
    line_out("Found '$last_first'");
    $passed++;
}

line_out('Checking to see if the Authors detail page was altered');
line_out("  list_display = ('last_name', ...");
webauto_search_for($html, 'Date of birth');
webauto_search_for($html, 'Last name');

// Load the books page
$crawler = webauto_get_url($client, $books_url);
$html = webauto_get_html($crawler);

markTestPassed('Books admin page retrieved');

line_out("Looking for '$book_title'");
if ( strpos($html,$book_title) < 1 ) {
    error_out('It looks like you have not created an book named');
    error_out($book_title);
} else {
    line_out("Found '$book_title'");
    $passed++;
}

line_out('Checking to see if the Books list page was altered');
line_out("  list_display = ('title', 'author',...");
webauto_search_for($html, 'Title');
webauto_search_for($html, 'Author');

line_out('Checking to see if models.py was modified to add Genre to list display');
line_out("  def display_genre(self): ....");
webauto_search_for($html, 'Genre');

// Load the bookinstancess page
$crawler = webauto_get_url($client, $instance_url);
$html = webauto_get_html($crawler);

markTestPassed('Bookinstances admin page retrieved');

line_out('Checking to see if list_filter was added to Bookinstances');
webauto_search_for($html, 'Filter');
webauto_search_for($html, 'By due back');
webauto_search_for($html, 'This year');

// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
$perfect = 29;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

