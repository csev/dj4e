<?php

require_once "webauto.php";

use Goutte\Client;

$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 04</h1>
<p>
For this assignment work through Part 4 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/3.0/intro/tutorial04/" target="_blank">
https://docs.djangoproject.com/en/3.0/intro/tutorial04/</a>.
</a>
</p>
<?php
nameNote();
$message = $check;
if ( $USER->displayname ) {
    $message = $USER->displayname . " / ". $check;
}
?>
Even though this excersise refactors three of your views as generic views, you
can keep the "owner" view as an old-style view in your <b>views.py</b>.
<pre>
    def owner(request):
        return HttpResponse("Hello, world. <?= $message ?> is the polls owner.")

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
$html = webauto_get_html($crawler);

$link = webauto_get_href($crawler, $qtext);
$passed += 1;
$url = $link->getURI();

$crawler = webauto_get_url($client, $url);
$html = webauto_get_html($crawler);

line_out("Looking for '$qtext' in the detail response");
webauto_search_for($html, $qtext);

line_out("Looking for HTML form with 'Vote' button");
$form = webauto_get_form_with_button($crawler,'Vote');

$value = webauto_get_radio_button_choice($form,'choice','42');
if ( is_string($value) ) {
    markTestPassed("Found choice radio button with a label of '42'");
} else {
    error_out("Could not find choice radio button with label of '42'");
    return;
}

line_out(" ");
webauto_change_form($form, 'choice', $value);
line_out("Submitting voting form");
$crawler = $client->submit($form);

$html = webauto_get_html($crawler);
webauto_search_for($html, 'Vote again?');

// -------------------- Send the grade ---------------
line_out(' ');
$perfect = 8;

if ( ! $check ) {
    error_out("No score sent, missing owner name");
    return;
}

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

