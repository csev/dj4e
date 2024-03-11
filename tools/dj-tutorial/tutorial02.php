<?php

require_once "../crud/webauto.php";

$adminpw = substr(getMD5(),4,9);
if ( is_numeric($adminpw) ) $adminpw = $adminpw.'a';
$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 02</h1>
<p>
For this assignment work through Part 2 of the Django tutorial at
<a href="https://www.dj4e.com/assn/dj4e_tut02.md" target="_blank">
https://www.dj4e.com/assn/dj4e_tut02.md</a>.
</a>
</p>
<p>
Once you have completed tutorial, make a second admin user with the following information:
<pre>
Account: dj4e
Password: <?= htmlentities($adminpw) ?>
</pre>
You can use any email address you like.
</p>
<p>
Using the Django shell or the Django administration user interface, insert a
<a href="https://en.wikipedia.org/wiki/Phrases_from_The_Hitchhiker%27s_Guide_to_the_Galaxy" target="_blank">question</a> with the exact text:
<pre>
<?= $qtext ?>
</pre>
Insert at least three choices and associate them with your question.  One of the choices
should be "42".  
(<a href="tutorial02/choice_detail.png" target="_blank">Example</a>)
When you have stored this data, submit your Django admin url to the autograder.
</p>
<?php

$url = getUrl('https://djtutorial.dj4e.com/admin');
if ( $url === false ) return;
$passed = 0;

$admin = $url;
error_log("Tutorial02 ".$url);

webauto_setup();

$crawler = webauto_retrieve_url($client, $admin);
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

$url = webauto_extract_url($crawler,'Questions');
if ( $url == false ) return;

$crawler = webauto_retrieve_url($client, $url);
$html = webauto_get_html($crawler);

markTestPassed('Questions page retrieved');

line_out("Looking for '$qtext'");
if ( strpos($html,$qtext) < 1 ) {
    if ( stripos($html,$qtext) > 0 ) {
        error_out('Your question text case does not match');
    } else {
        error_out('It looks like you have not created a question with text');
    }
    error_out($qtext);
    error_out('The test cannot be continued');
    return;
}

line_out("Found '$qtext'");
$passed++;

// -------
line_out(' ');
$perfect = 3;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

