<?php

require_once "webauto.php";

use Goutte\Client;

$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 03</h1>
<p>
For this assignment work through Part 3 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/2.0/intro/tutorial03/" target="_blank">
https://docs.djangoproject.com/en/2.0/intro/tutorial03/</a>.
</a>
</p>
<?php
nameNote();
?>
Add the following to your <b>views.py</b> with the required information above.
<pre>
    def owner(request):
        return HttpResponse("Hello, world. Jane Instructor / 1ff1de77 is the polls owner.")
</pre>
Add the following to your <b>urls.py</b> to add the route the to the <b>/owner</b> path.
<pre>
urlpatterns = [
    # ex: /polls/
    path('', views.index, name='index'),
    # ex: /polls/owner
    path('owner', views.owner, name='owner'),
    # ex: /polls/5/
    ...
</pre>
<p>
You should already have a question with this text from the previous assignment:
<pre>
<?= $qtext ?>
</pre>
and submit your Django polls url to the autograder. 
</p>
<?php

$url = getUrl('http://drchuck.pythonanywhere.com/polls3');
if ( $url === false ) return;
$passed = 0;

$owner = $url . '/owner';
error_log("Tutorial03 ".$owner);
line_out("Retrieving ".htmlent_utf8($owner)."...");
flush();

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = $client->request('GET', $owner);
$html = $crawler->html();
showHTML("Show retrieved page",$html);
$passed = 0;

if ( stripos($html, 'Hello') !== false ) {
    success_out("Found 'Hello' in your HTML");
    $passed += 1;
} else {
    error_out("Did not find 'Hello' in your HTML");
}

$check = webauto_get_check();

if ( $USER->displayname && stripos($html,$USER->displayname) !== false ) {
    success_out("Found ($USER->displayname) in your html");
    $passed += 1;
} else if ( $check && stripos($html,$check) !== false ) {
    success_out("Found ($check) in your html");
    $passed += 1;
} else if ( $USER->displayname ) {
    error_out("Did not find $USER->displayname or $check in your html");
    error_out("No score will be sent, but the test will continue");
}

line_out("Retrieving ".htmlent_utf8($url)."...");
flush();

$crawler = $client->request('GET', $url);
$html = $crawler->html();
showHTML("Show retrieved page",$html);

$link = webauto_get_href($crawler, $qtext);
$passed += 1;
$url = $link->getURI();
line_out("Retrieving ".htmlent_utf8($url)."...");
$html = $crawler->html();
showHTML("Show retrieved page",$html);

line_out("Looking for '$qtext' in the detail response");
if ( strpos($html, $qtext) !== false ) {
    success_out("Found ($qtext) in your detail response");
    $passed += 1;
} else {
    success_out("Did not find ($qtext) in your detail response");
}

// -------------------- Send the grade ---------------
$perfect = 3;
if ( $passed > $perfect ) $passed = $perfect;

if ( ! $check ) {
    error_out("No score sent, missing owner name");
    return;
}

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

