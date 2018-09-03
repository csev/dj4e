<?php

require_once "webauto.php";

use Goutte\Client;

$adminpw = substr(getMD5(),4,9);
$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 02</h1>
<p>
For this assignment work through Part 2 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/2.0/intro/tutorial02/" target="_blank">
https://docs.djangoproject.com/en/2.0/intro/tutorial02/</a>.
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
Then create a 
<a href="https://en.wikipedia.org/wiki/Phrases_from_The_Hitchhiker%27s_Guide_to_the_Galaxy" target="_blank">question</a> with the exact text:
<pre>
<?= $qtext ?>
</pre>
Have at three answerd and at least one answer be 42 
and submit your Django admin url to the autograder. 
</p>
<?php

$url = getUrl('http://drchuck.pythonanywhere.com/admin');
if ( $url === false ) return;
$passed = 0;

$admin = $url;
error_log("Tutorial02 ".$url);
line_out("Retrieving ".htmlent_utf8($admin)."...");
flush();

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = $client->request('GET', $admin);
$html = $crawler->html();
showHTML("Show retrieved page",$html);

line_out('Looking for the form with a value="Log In" submit button');
$form = webauto_get_form_button($crawler,'Log in');
$form->setValues(array("username" => "dj4e", "password" => $adminpw));
$crawler = $client->submit($form);
$html = $crawler->html();
showHTML("Show retrieved page",$html);

if ( strpos($html,'Log in') > 0 ) {
    error_out('It looks like you have not yet set up the admin account with dj4e / '.$adminpw);
    error_out('The test cannot be continued');
    return;
} else {
    line_out("Login successful...");
}

line_out("Looking for  an anchor tag with text of 'Questions'");
$link = $crawler->selectLink('Questions')->link();
$url = $link->getURI();
line_out("Retrieving ".htmlent_utf8($url)."...");
$crawler = $client->request('GET', $url);
markTestPassed('Questions page retrieved');
$html = $crawler->html();
showHTML("Show retrieved page",$html);

line_out("Looking for '$qtext'");
if ( strpos($html,$qtext) < 1 ) {
    error_out('It looks like you have not created a question with text');
    error_out($qtext);
    error_out('The test cannot be continued');
    return;
}

line_out("Found '$qtext'");
$passed++;

$perfect = 3;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

