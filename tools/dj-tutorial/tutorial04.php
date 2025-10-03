<?php

require_once "../crud/webauto.php";

$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 04</h1>
<p>
For this assignment work through Part 4 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/5.2/intro/tutorial04/" class="btn btn-info" target="_blank">
https://docs.djangoproject.com/en/5.2/intro/tutorial04/</a>.
</a>
</p>
<?php
nameNote();
$check = webauto_get_check();

?>
Even though this exercise refactors three of your views as generic views, you
can keep the "owner" view as an old-style view in your <b>mysite/polls/views.py</b>.
<pre>
    from django.http import HttpRequest, HttpResponse
    def owner(request: HttpRequest) -&gt; HttpResponse:
        response = HttpResponse()
        response.write("Hello, world. <?= $check ?> is the polls index.")
        return response
</pre>
You can mix function and class views in your <b>mysite/polls/urls.py</b> file as shown below:
<pre>
urlpatterns = [
    path('', views.IndexView.as_view(), name='index'),
    path('owner', views.owner, name='owner'),
    path('&lt;int:pk&gt;/', views.DetailView.as_view(), name='detail'),
    path('&lt;int:pk&gt;/results/', views.ResultsView.as_view(), name='results'),
    path('&lt;int:question_id&gt;/vote/', views.vote, name='vote'),
]
</pre>
Note that this file somewhat different between tutorials three and four.  In this tutorial
it directs you change 'question_id' to 'pk' in two of paths.
</p>
<p>
You should already have a question with this text with one answer that is '42'
from the previous assignment:
<pre>
<?= $qtext ?>
</pre>
Then submit your Django polls url to the autograder. 
Your url should be "/polls" not "/polls4".
</p>
<?php

$url = getUrl('http://djtutorial.dj4e.com/polls4');
if ( $url === false ) return;
$passed = 0;

$owner = $url . '/owner';
error_log("Tutorial04 ".$owner);

webauto_setup();

$crawler = webauto_retrieve_url($client, $owner);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

$hascheck = false;
if ( $check && stripos($html,$check) !== false ) {
    markTestPassed("Found ($check) in your html");
    $hascheck = true;
} else {
    error_out("Did not find $check in your html");
    error_out("No score will be sent, but the test will continue");
}

$crawler = webauto_retrieve_url($client, $url);
$html = webauto_get_html($crawler);

$url = webauto_extract_url($crawler, $qtext);
if ( $url == false ) return;
$passed += 1;

$crawler = webauto_retrieve_url($client, $url);
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

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( ! $hascheck ) {
    error_out("No score sent, missing owner value");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

