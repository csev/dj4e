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
$userpw = "Meow_" . substr(getMD5(),1,6). '_42';
$useraccount = 'dj4e_user';
line_out("Exploring DJango Forms (MDN)");
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
You can use any email address you like.  This account should have permission to do
book renewals and access the CRUD forms for authors.
</p>
<p>
You should still have the identifiying <b>meta</b> tag in your <b>&lt;head&gt;</b> area and an author and book from the previous tutorial autograder.
</p>

<?php

$url = getUrl('http://mdntutorial.pythonanywhere.com/');
if ( $url === false ) return;
$passed = 0;

$admin = $url . 'admin';
$catalog_url = $url . 'catalog';

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
$all_url = webauto_get_url_from_href($crawler,'All Borrowed');
$authors_url = webauto_get_url_from_href($crawler,'All authors');

$crawler = webauto_get_url($client, $all_url, 'Retrieving the "All Borrowed" page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $book_title);

$renew_url = webauto_get_url_from_href($crawler,'Renew');
$crawler = webauto_get_url($client, $renew_url, 'Retrieving the renew page');
$html = webauto_get_html($crawler);

$form = webauto_get_form_with_button($crawler,'Submit');
$datetime = new DateTime('tomorrow');
$when = $datetime->format('Y-m-d');
webauto_change_form($form, 'renewal_date', $when);
line_out("Submitting the form...");
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);
$detail_url = webauto_get_url_from_href($crawler, $book_title);

$crawler = webauto_get_url($client, $detail_url, 'Retrieving the detail page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $book_title);

$crawler = webauto_get_url($client, $all_url, 'Retrieving the "All Borrowed" page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $book_title);

$when_long = $datetime->format('M. d, Y');
$retval = webauto_search_for($html, $when_long);

// Clean up a previously created authors if they are there

$new_first = 'Django';
$new_last = 'Girls';
$new_full = $new_last . ', ' . $new_first;

$save = $passed;
line_out("Checking for extra authors from a previous CRUD run...");
for($i=0;$i<4;$i++) {
    $crawler = webauto_get_url($client, $authors_url, 'Retrieving the create author page');
    $html = webauto_get_html($crawler);
    if ( strpos($html, $new_full) > 0 ) {
        $author_url = webauto_get_url_from_href($crawler, $new_full);
        $delete_url = $author_url . '/delete';
        $crawler = webauto_get_url($client, $delete_url, 'Retrieving the delete author page');
        $html = webauto_get_html($crawler);
        $form = webauto_get_form_with_button($crawler,'Yes, delete.');
        $crawler = $client->submit($form);
    } else {
        break;
    }
}
$passed = $save;

$create_url = $catalog_url . '/author/create';
$crawler = webauto_get_url($client, $create_url, 'Retrieving the create author page');
$html = webauto_get_html($crawler);
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'first_name', $new_first);
webauto_change_form($form, 'last_name', $new_last);
webauto_change_form($form, 'date_of_birth', $when);
webauto_change_form($form, 'date_of_death', '');
line_out("Submitting the form...");
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

// The redirected author detail page
$retval = webauto_search_for($html, $new_full);
$retval = webauto_search_for($html, $when_long);

$save = $passed;
line_out("Removing added author(s)...");
for($i=0;$i<4;$i++) {
    $crawler = webauto_get_url($client, $authors_url, 'Retrieving the authors page');
    $html = webauto_get_html($crawler);
    if ( strpos($html, $new_full) > 0 ) {
        $author_url = webauto_get_url_from_href($crawler, $new_full);
        $delete_url = $author_url . '/delete';
        $crawler = webauto_get_url($client, $delete_url, 'Retrieving the delete author page');
        $html = webauto_get_html($crawler);
        $form = webauto_get_form_with_button($crawler,'Yes, delete.');
        $crawler = $client->submit($form);
    } else {
        break;
    }
}
$passed = $save;

$crawler = webauto_get_url($client, $logout_url, 'Logging out');
$html = webauto_get_html($crawler);

line_out('');
line_out('Making sure the CRUD forms fail when the user has logged out');

$crawler = webauto_get_url($client, $create_url, 'Retrieving the create author page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, 'Please login to see this page.');

$crawler = webauto_get_url($client, $authors_url, 'Retrieving author list page...');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $last_first);
$author_detail_url = webauto_get_url_from_href($crawler,$last_first);

$crawler = webauto_get_url($client, $author_detail_url, 'Retrieving author detail page...');
$html = webauto_get_html($crawler);

$update_url = $author_detail_url . '/update';
$crawler = webauto_get_url($client, $update_url, 'Retrieving the update author page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, 'Please login to see this page.');

$delete_url = $author_detail_url . '/delete';
$crawler = webauto_get_url($client, $delete_url, 'Retrieving the delete author page');
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, 'Please login to see this page.');



// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 22;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

