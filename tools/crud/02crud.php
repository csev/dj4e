<?php

use \Tsugi\Util\U;

require_once "webauto.php";
require_once "names.php";

// TODO: Make this work on 06 07
$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();

if ( $LAUNCH->user && $LAUNCH->user->instructor ) {
    if ( U::isNotEmpty(U::get($_REQUEST, 'reset')) ) {
        unset($_SESSION['userpw']);
        unset($_SESSION['useraccount']);
    } else if ( U::isNotEmpty(U::get($_REQUEST, 'userpw')) && U::isNotEmpty(U::get($_REQUEST, 'useraccount')) ) {
        $userpw =  U::get($_REQUEST, 'userpw');
        $_SESSION['userpw'] =  $userpw;
        $useraccount =  U::get($_REQUEST, 'useraccount');
        $_SESSION['useraccount'] =  $useraccount;
    }
}

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
$userpw = "Meow_" . substr(getMD5(),1,6). '_42';
$userpw =  U::get($_SESSION, 'userpw', $userpw);
$useraccount = 'dj4e_user';
$useraccount =  U::get($_SESSION, 'useraccount', $useraccount);

line_out("Create, Read, Update, and Delete (CRUD)")
?>
<a href="<?= $assignment_url ?>" class="btn btn-info" target="_blank"><?= $assignment_url_text ?></a>
</a>
<p>
In order for the autograder to exercise your assignment, you must
create a user account with the credentials below.  It is best if this is not
a "superuser" - instead
navigating to the <b>/admin</b> path in your application, logging in
with your superuser account and creating the new account in the admin UI.  By
default the new user will not have staff or superuser permissions.
<pre>
Account: <?= htmlentities($useraccount) ?> 
Password: <?= htmlentities($userpw) ?>
</pre>
You can use any email address you like.
</p>
<?php if ( $LAUNCH->user && $LAUNCH->user->instructor ) { ?>
<p>
As an instructor, you can change the user account and password in order to test a student site with their account/password.
<form method="get">
Account: <input type="text" name="useraccount"> <br/>
Password: <input type="text" name="userpw"> <br/>
<input type="submit" value="Submit">
<input type="submit" value="Reset" name="reset">
</form>
</p>
<?php } ?>
<p>
You should edit or add a <b>meta</b> tag in your <b>&lt;head&gt;</b> area of each page you generate
as shown below:
<pre>
<?= htmlentities($meta) ?> 
</pre>
If there is already a meta tag with the value "42-42" <b>make sure</b>
to <em>remove it or replace</em> it with the above tag.
</p>

<?php
$url = getUrl('http://crud.dj4e.com/');
if ( $url === false ) return;
$passed = 0;
warn_about_ngrok($url);

webauto_check_test();

$suffix = '/'  . $main_lower_plural;
if ( strpos($url, $suffix) > 0 ) {
  $url = substr($url, 0, strpos($url, $suffix));
}

$admin = $url . 'admin';
$main_url = trimSlash($url) . $suffix;

webauto_setup();

// Start the actual test
$crawler = webauto_get_url($client, $main_url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

// TODO: Bring this back after October 2019
// require("meta_check.php");

// Use the log_in form
$form = webauto_get_form_with_button($crawler, 'login', 'Login');
webauto_change_form($form, 'username', $useraccount);
webauto_change_form($form, 'password', $userpw);

line_out("Submitting form...");
$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);

if ( stripos($html,"Your username and password didn't match. Please try again.") ) {
    error_out("Could not log in to your account...");
    return;
}

// TODO: Remove this after October 2019
require("meta_check.php");

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
    line_out("Submitting form...");
	$crawler = webauto_submit_form($client, $form);
    $html = webauto_get_html($crawler);
    $count++;
}

line_out("Deleted $count old $lookup_lower_plural");
$passed = $savepassed;

$crawler = webauto_get_url($client, $add_lookup_url, "Retrieving the 'Add $lookup_article $lookup_lower' page");
$html = webauto_get_html($crawler);


// Add an item the the lookup table
$lookup_new = "LU_42_" . rand(0,100);
$form = webauto_get_form_with_button($crawler,'Submit');
webauto_change_form($form, 'name', $lookup_new);
line_out("Submitting form...");
$crawler = webauto_submit_form($client, $form);
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
line_out("Submitting form...");
$crawler = webauto_submit_form($client, $form);
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
foreach($fields as $field) {
    if ( $field['type'] == 'i' ) {
        $value = ( 4200 + rand(1,100) ) . "";
    } else {
        $value = "Hello world";
    }
    webauto_change_form($form, $field['name'], $value);
}
webauto_change_form($form, $lookup_lower, $lookup_select);
line_out("Submitting form...");
$crawler = webauto_submit_form($client, $form);
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
line_out("Submitting form...");
$crawler = webauto_submit_form($client, $form);
$html = webauto_get_html($crawler);

$retval = webauto_search_for($html, $new_nickname);


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 14;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( ! $meta_good ) {
    error_out("Not graded - missing meta tag(s)");
    return;
}
if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

