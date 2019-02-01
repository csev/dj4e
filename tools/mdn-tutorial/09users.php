<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

// TODO: Make this work on 06 07
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
$meta = '<meta name="wa4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
$userpw = "Meow_" . substr(getMD5(),1,6). '_42';
$useraccount = 'dj4e_user';
line_out("Exploring DJango Users (MDN)");
?>
<a href="../../assn/paw_users.md" target="_blank">
https://www.dj4e.com/assn/paw_users.md</a>
</a>
<p>
In addition to the steps in the tutorial, make a user (not an admin account) and add it to
the "Library Staff" account to allow this autograder to log in and check your work
with the following information:
<pre>
Account: <?= htmlentities($useraccount) ?> 
Password: <?= htmlentities($userpw) ?>
</pre>
You can use any email address you like.
</p>
<!--
<p>
You should still have the identifiying <b>meta</b> tag in your <b>&lt;head&gt;</b> area and an author and book from the previous tutorial autograder.
</p>
-->
<p>
You need to add the following line to your <b>base_generic.html</b> file within the
<b>&lt;head&gt;</b> area:
<pre>
<?= htmlentities($meta) ?>
</pre>
Make sure to put this all on one line and with no extra spaces within the tag.
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

// Start the actual test
$crawler = webauto_get_url($client, $catalog_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    error_out('Assignment will not be scored.');
    $passed = -1000;
}
$login_url = webauto_get_url_from_href($crawler,'Login');


$crawler = webauto_get_url($client, $login_url, "Logging in as $useraccount");
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'login');
webauto_change_form($form, 'username', $useraccount);
webauto_change_form($form, 'password', $userpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, 'User: '.$useraccount);
$logout_url = webauto_get_url_from_href($crawler,'Logout');
$borrowed_url = webauto_get_url_from_href($crawler,'My Borrowed');

$crawler = webauto_get_url($client, $borrowed_url, 'Retrieving the "My Borrowed" page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $book_title);

$crawler = webauto_get_url($client, $logout_url, 'Logging out');
$html = webauto_get_html($crawler);

$home_url = webauto_get_url_from_href($crawler,'Home');
$books_url = webauto_get_url_from_href($crawler,'All books');
$authors_url = webauto_get_url_from_href($crawler,'All authors');
$login_url = webauto_get_url_from_href($crawler,'Login');

$crawler = webauto_get_url($client, $books_url, 'Retrieving book list page...');
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);
$book_detail_url = webauto_get_url_from_href($crawler,$book_title);

$crawler = webauto_get_url($client, $book_detail_url, 'Retrieving book detail page...');
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);

$crawler = webauto_get_url($client, $authors_url, 'Retrieving author list page...');
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $last_first);
$author_detail_url = webauto_get_url_from_href($crawler,$last_first);

$crawler = webauto_get_url($client, $author_detail_url, 'Retrieving author detail page...');
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $last_first);
$back_to_book_url = webauto_get_url_from_href($crawler,$book_title);

$crawler = webauto_get_url($client, $back_to_book_url, 'Retrieving book detail page from author page...');
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $book_title);
$retval = webauto_search_for($html, $last_first);


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 22;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

