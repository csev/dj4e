<?php

require_once "../crud/webauto.php";

$qtext = 'Answer to the Ultimate Question';
?>
<h1>Django Tutorial 03</h1>
<p>
For this assignment work through Part 3 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/4.2/intro/tutorial03/" class="btn btn-info" target="_blank">
https://docs.djangoproject.com/en/4.2/intro/tutorial03/</a>.
</a>
</p>
<?php
nameNote();
$check = webauto_get_check();
?>
Add the following to your <b>mysite/polls/views.py</b> with the required information above.
<pre>
    from django.http import HttpRequest
    def owner(request: HttpRequest) -&gt; HttpResponse:
        response = HttpResponse()
        response.write("Hello, world. <?= $check ?> is the polls index.")
        return response
</pre>
Make sure to check the file <b>mysite/polls/urls.py</b> to insure that the 
the path to the <b>owner</b> view is properly routed:
<pre>
urlpatterns = [
    path('', views.index, name='index'),
    path('owner', views.owner, name='owner'),
    path('&lt;int:question_id&gt;/', views.detail, name='detail'),
    path('&lt;int:question_id&gt;/results/', views.results, name='results'),
    path('&lt;int:question_id&gt;/vote/', views.vote, name='vote'),
]
</pre>
<p>
You should already 
have created a question with this text from the previous assignment:
<pre>
<?= $qtext ?>
</pre>
and submit your Django polls url to the autograder. (Your url should be /polls - not /polls3).
</p>
<?php

$url = getUrl('http://djtutorial.dj4e.com/polls3');
if ( $url === false ) return;
$passed = 0;

$owner = $url . '/owner';
error_log("Tutorial03 ".$owner);

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

$crawler = webauto_retrieve_url($client, $url, "Retrieving the list page");
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);


$url = webauto_extract_url($crawler, $qtext);
if ( $url == false && stripos($html, $qtext) > 0 ) {
    error_out("I found the question text on your page, but I am looking for the exact question text in the anchor tag");
    return;
}

if ( $url == false ) return;
$passed += 1;

$crawler = webauto_retrieve_url($client, $url, "Retrieving detail page");
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, $qtext);

// -------------------- Send the grade ---------------
line_out(' ');
$perfect = 4;
if ( $passed > $perfect ) $passed = $perfect;

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( ! $hascheck ) {
    error_out("No score sent, missing owner value");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

