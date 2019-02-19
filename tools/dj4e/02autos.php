<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

// TODO: Make this work on 06 07
$code = $USER->id+$CONTEXT->id;

$lookup_lower = 'make';
$lookup_article = 'a';
$lookup_lower_plural = 'makes';
$main_lower = 'auto';
$main_article = 'an';
$main_lower_plural = 'autos';

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
line_out("Exploring Django Users (MDN)");
?>
<a href="../../assn/dj4e_autos.md" target="_blank">
https://www.dj4e.com/assn/dj4e_autos.md</a>
</a>
<p>
In addition to the steps in the tutorial, make a user (not an admin account).  
Don't give it staff or super user permissions.
<pre>
Account: <?= htmlentities($useraccount) ?> 
Password: <?= htmlentities($userpw) ?>
</pre>
You can use any email address you like.
</p>
<p>
You should add identifiying <b>meta</b> tag in your <b>&lt;head&gt;</b> area of each page you generate.
<pre>
<?= htmlentities($meta) ?> 
</pre>
</p>

<?php

// $url = getUrl('http://projects.dj4e.com/');
$url = getUrl('http://localhost:8000/');
if ( $url === false ) return;
$passed = 0;

webauto_check_test();

$admin = $url . 'admin';
$main_url = $url . $main_lower_plural;

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

// Start the actual test
$crawler = webauto_get_url($client, $main_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

// Use the log_in form
$form = webauto_get_form_with_button($crawler,'login');
webauto_change_form($form, 'username', $useraccount);
webauto_change_form($form, 'password', $userpw);

$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

if ( stripos($html,"Your username and password didn't match. Please try again.") ) {
    error_out("Could not log in to your account...");
    return;
}

$add_lookup_url = webauto_get_url_from_href($crawler,"Add $lookup_article $lookup_lower");
$view_lookup_url = webauto_get_url_from_href($crawler,"View $lookup_lower_plural");

line_out("Checking for old $lookup_lower_plural from previous autograder runs...");
$savepassed = $passed;

$count = 0;
for($i=0; $i<10; $i++) {
    $crawler = webauto_get_url($client, $view_lookup_url, "Retrieving the 'View $lookup_lower_plural' page");
    $html = webauto_get_html($crawler);
    $pos = strpos($html, 'LU_');
    if ( $pos < 1 ) break;
    $pos2 = strpos($html, 'Delete', $pos);
    if ( $pos2 < 1 ) break;

    $link = quoteBack($html, $pos2);
    $delete_url = trimSlash($url) . $link;
    $crawler = webauto_get_url($client, $delete_url, "Retrieving Delete URL");
    $html = webauto_get_html($crawler);

    $form = webauto_get_form_with_button($crawler,'Yes, delete.');
    $crawler = $client->submit($form);
    $html = webauto_get_html($crawler);
    $count++;
}

line_out("Deleted $count old $lookup_lower_plural");
$passed = $savepassed;

$crawler = webauto_get_url($client, $add_lookup_url, "Retrieving the 'Add $lookup_article $lookup_lower' page");
$html = webauto_get_html($crawler);

$meta_good = false;
line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    $meta_good = false;
}

// Add an item the the lookup table

$lookup_new = "LU_42_" . rand(0,100);
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'name', $lookup_new);
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);
line_out("It looks like we created $lookup_article $lookup_lower named $lookup_new :)");

// Update our item in the lookup table

$view_lookup_url = webauto_get_url_from_href($crawler,"View $lookup_lower_plural");
$crawler = webauto_get_url($client, $view_lookup_url, "Retrieving view page...");
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $lookup_new);
$pos = strpos($html, $lookup_new);
if ( $pos < 1 ) {
    error_out("Could not find $lookup_new in $lookup_lower_plural");
    return;
}
$pos2 = strpos($html, "Update", $pos);
if ( $pos2 < 1 ) {
    error_out("Could not find Update link for $lookup_new in $lookup_lower_plural");
    return;
}

$update_link = quoteBack($html, $pos2);
$update_url = trimSlash($url) . $update_link;
$crawler = webauto_get_url($client, $update_url, "Retrieving the Update page");
$html = webauto_get_html($crawler);

$lookup_new = $lookup_new . "_updated";
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'name', $lookup_new);
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

$crawler = webauto_get_url($client, $view_lookup_url, "Retrieving lookup view page...");
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $lookup_new);


// Add an item to the main table
$crawler = webauto_get_url($client, $main_url, "Retrieving main view page...");
$html = webauto_get_html($crawler);
$add_main_url = webauto_get_url_from_href($crawler,"Add $main_article $main_lower");
$crawler = webauto_get_url($client, $add_main_url, "Retrieving 'Add $main_article $main_lower' page...");
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $lookup_new);

// Find the value= for the right dropdown
$pos = strpos($html, $lookup_new);
if ( $pos < 1 ) {
    error_out("Could not find $lookup_new in $lookup_lower_plural drop-down");
    return;
}

$lookup_select = quoteBack($html, $pos);
line_out("Selecting $lookup_new key=$lookup_select");

$new_nickname =  "Main_entry_42_" . rand(0,100);

$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'nickname', $new_nickname);
webauto_change_form($form, 'mileage', "42");
webauto_change_form($form, 'comments', "Hello world");
webauto_change_form($form, $lookup_lower, $lookup_select);
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);
$retval = webauto_search_for($html, $new_nickname);

$pos = strpos($html, $new_nickname);
if ( $pos < 1 ) {
    error_out("Could not find $new_nickname in $main_lower_plural");
    return;
}
$pos2 = strpos($html, "Update", $pos);
if ( $pos2 < 1 ) {
    error_out("Could not find Update link for $new_nickname in $main_lower_plural");
    return;
}

$update_link = quoteBack($html, $pos2);
$update_url = trimSlash($url) . $update_link;
$crawler = webauto_get_url($client, $update_url, "Retrieving the Update page");
$html = webauto_get_html($crawler);

$new_nickname = $new_nickname . "_updated";
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'nickname', $new_nickname);
$crawler = $client->submit($form);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $new_nickname);


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 14;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// if ( $score < 1.0 ) autoToggle();

if ( ! $meta_good ) {
    error_out("Not graded - missing meta tag");
    return;
}
if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

