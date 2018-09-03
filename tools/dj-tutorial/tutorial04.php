<?php

require_once "webauto.php";

use Goutte\Client;

$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 04</h1>
<p>
For this assignment work through Part 4 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/2.0/intro/tutorial04/" target="_blank">
https://docs.djangoproject.com/en/2.0/intro/tutorial04/</a>.
</a>
</p>
<?php
nameNote();
?>
Even though this excersise refactors three of your views as generic views, you
can keep the "owner" view as an old-style view in your <b>views.py</b>.
<pre>
    def owner(request):
        return HttpResponse("Hello, world. Jane Instructor / 1ff1de77 is the polls owner.")
</pre>
You can mix old ans new styles in your <b>urls.py</b> file as shown below:
<pre>
urlpatterns = [
    path('', views.IndexView.as_view(), name='index'),
    path('<int:pk>/', views.DetailView.as_view(), name='detail'),
    path('<int:pk>/results/', views.ResultsView.as_view(), name='results'),
    path('owner', views.owner, name='owner'),
    path('<int:question_id>/vote/', views.vote, name='vote'),
]
</pre>
<p>
You should already have a question with this text with one answer that is '42'
from the previous assignment:
<pre>
<?= $qtext ?>
</pre>
Then submit your Django polls url to the autograder. 
</p>
<?php

$url = getUrl('http://dj4e.pythonanywhere.com/polls4');
if ( $url === false ) return;
$passed = 0;

$owner = $url . '/owner';
error_log("Tutorial04 ".$owner);
line_out("Retrieving ".htmlent_utf8($owner)." ...");
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

line_out("Retrieving ".htmlent_utf8($url)." ...");
flush();

$crawler = $client->request('GET', $url);
$html = $crawler->html();
showHTML("Show retrieved page",$html);

$link = webauto_get_href($crawler, $qtext);
$passed += 1;
$url = $link->getURI();
line_out("Retrieving ".htmlent_utf8($url)." ...");
$crawler = $client->request('GET', $url);
$html = $crawler->html();
showHTML("Show retrieved page",$html);

line_out("Looking for '$qtext' in the detail response");
if ( strpos($html, $qtext) !== false ) {
    success_out("Found ($qtext) in your detail response");
    $passed += 1;
} else {
    success_out("Did not find ($qtext) in your detail response");
}

line_out("Looking for HTML form with 'Vote' button");
$form = webauto_get_form_with_button($crawler,'Vote');

line_out("Looking for choice radio button");
if ($form->has("choice") ) {
    $choice = $form->get("choice");
    $type = $choice->getType();
    if ( $type == "radio" ) {
        $passed += 1;
        success_out("Found 'choice' radio buttons");
    } else {
        error_out("Could not find radio buttons for form input 'choice'");
        return;
    }
}

line_out("Looking for choice with '42' as the label");
$matches = Array();
preg_match('/<input type="radio" name="choice" id="choice." value="(.)"><label for="choice.">42<.label>/',$html,$matches);
if ( is_array($matches) && count($matches) == 2 && is_numeric($matches[1]) ) {
    success_out("Found choice with '42'");
    $passed += 1;
    $choiceval = $matches[1];
} else {
    error_out("Could not find radio button for '42'");
    return;
}

success_out($choiceval);

// var_dump($choice->availableOptionValues());

// New for Polls 4

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

