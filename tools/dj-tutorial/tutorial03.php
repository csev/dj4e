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

$url = getUrl('http://dj4e.pythonanywhere.com/polls3');
if ( $url === false ) return;
$passed = 0;

$owner = $url . '/owner';
error_log("Tutorial03 ".$owner);

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = webauto_get_url($client, $owner);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

$check = webauto_get_check();

if ( $USER->displayname && stripos($html,$USER->displayname) !== false ) {
    markTestPassed("Found ($USER->displayname) in your html");
} else if ( $check && stripos($html,$check) !== false ) {
    markTestPassed("Found ($check) in your html");
} else if ( $USER->displayname ) {
    error_out("Did not find $USER->displayname or $check in your html");
    error_out("No score will be sent, but the test will continue");
}

$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

$link = webauto_get_href($crawler, $qtext);
$passed += 1;
$url = $link->getURI();

$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, $qtext);

// -------------------- Send the grade ---------------
line_out(' ');
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

