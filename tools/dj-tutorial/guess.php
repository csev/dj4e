<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";

$code = $USER->id+$CONTEXT->id;
$secret = ($code % 90) + 3;

$sample = "http://localhost:8000/guess/";
$sample = "https://drchuck.pythonanywhere.com/guess/";

?>
<form method="post" action="<?= $sample ?>" target="_blank">
<input type="hidden" name="secret" value="<?= $secret ?>">
Your Assignment: <input type="submit" value="Replicate this application">
</form>
<p>
You have to figure out the secret number to guess by using the above application.
</p>
<p>
There are two paths in the <b>urls.py</b> for the <b>guess</b> application in the Django project.
<pre>
    path('', views.index, name='index'),
    path('&lt;int:guessvalue&gt;', views.guess, name='index'),
</pre>
</p>
<?php
$url = getUrl($sample);
if ( $url === false ) return;
if ( ! str_ends_with($url, '/') ) $url = $url . '/';

$testrun = webauto_testrun($url);
if ( $testrun ) {
    error_out("Sample implementation used - no score will be sent");
    $secret = 42;
}
$passed = 0;

webauto_setup();

// Start the actual test
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "guess the secret");


for ( $i = 5; $i < 100; $i = $i + 10 ) {
    $checkurl = $url . $i;
    $crawler = webauto_get_url($client, $checkurl);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);
    if ( $i < $secret ) {
        webauto_search_for($html, "Too low");
        webauto_search_for_not($html, "Too high");
        webauto_search_for_not($html, "Just right");
    } else if ( $i > $secret ) {
        webauto_search_for_not($html, "Too low");
        webauto_search_for($html, "Too high");
        webauto_search_for_not($html, "Too low");
    } else {
        webauto_search_for($html, "Just right");
        webauto_search_for_not($html, "Too high");
        webauto_search_for_not($html, "Too low");
    }
}


// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 31;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $testrun ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

