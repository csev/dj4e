<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ', ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";

$adminpw = substr(getMD5(),4,9);
line_out("Exploring DJango Admin (MDN)");
?>
<a href="https://www.dj4e.com/assn/paw_admin.md" target="_blank">
https://www.dj4e.com/assn/paw_skeleton.md</a>
</a>
</p>
</p>
In addition to the steps in the tutorial, make a second admin user to allow
this autograder to log in and check your work with the following information:
<pre>
Account: dj4e
Password: <?= htmlentities($adminpw) ?>
</pre>
You can use any email address you like.
</p>
<p>
Make an Author with the following name (first, last):<br/>
<pre>
<?= htmlentities($full_name) ?>
</pre>
</p>
<p>
Also add a "Science Fiction" book by that author with a title of:<br/>
<pre>
<?= htmlentities($book_title) ?>
</pre>
</p>
<?php

$url = getUrl('http://mdntutorial.pythonanywhere.com/admin');
if ( $url === false ) return;
$passed = 0;

$admin = $url;
error_log("Tutorial02 ".$url);

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = webauto_get_url($client, $admin);
$html = webauto_get_html($crawler);

// line_out('Looking for the form with a value="Log In" submit button');
$form = webauto_get_form_with_button($crawler,'Log in');
webauto_change_form($form, 'username', 'dj4e');
webauto_change_form($form, 'password', $adminpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( strpos($html,'Log in') > 0 ) {
    error_out('It looks like you have not yet set up the admin account with dj4e / '.$adminpw);
    error_out('The test cannot be continued');
    return;
} else {
    line_out("Login successful...");
}

// Grab the urls to the various links
$catalog_link = webauto_get_href($crawler,'Catalog');
$catalog_url = $catalog_link->getURI();
$authors_link = webauto_get_href($crawler,'Authors');
$authors_url = $authors_link->getURI();
$books_link = webauto_get_href($crawler,'Books');
$books_url = $books_link->getURI();
$instance_link = webauto_get_href($crawler,'Book instances');
$instance_url = $instance_link->getURI();

// Load the catalog page
$crawler = webauto_get_url($client, $catalog_url);
$html = webauto_get_html($crawler);

markTestPassed('Catalog page retrieved');

// Load the authors page
$crawler = webauto_get_url($client, $authors_url);
$html = webauto_get_html($crawler);

markTestPassed('Authors page retrieved');

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
webauto_search_for($html, 'DATE OF BIRTH');
webauto_search_for($html, 'LAST NAME');

// Load the books page
$crawler = webauto_get_url($client, $books_url);
$html = webauto_get_html($crawler);

markTestPassed('Books page retrieved');

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
webauto_search_for($html, 'TITLE');
webauto_search_for($html, 'AUTHOR');

line_out('Checking to see if models.py was modified to add Genre to list display');
line_out("  def display_genre(self): ....");
webauto_search_for($html, 'GENRE');

// Load the bookinstancess page
$crawler = webauto_get_url($client, $instance_url);
$html = webauto_get_html($crawler);

markTestPassed('Bookinstances page retrieved');

line_out('Checking to see if list_filter was added to Bookinstances');
webauto_search_for($html, 'FILTER');
webauto_search_for($html, 'By due back');
webauto_search_for($html, 'This year');

// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
$perfect = 19;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

