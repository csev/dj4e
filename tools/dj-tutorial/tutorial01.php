<?php

require_once "webauto.php";

use Goutte\Client;

line_out("Grading Django Tutorial 01");

?>
<p>
For this assignment work through Part 1 of the Django tutorial at
<a href="https://docs.djangoproject.com/en/2.0/intro/tutorial01/" target="_blank">
https://docs.djangoproject.com/en/2.0/intro/tutorial01/</a>.
</a>
</p>
<?php
nameNote();
$message = $check;
if ( $USER->displayname ) {
    $message = $USER->displayname . " / ". $check;
}
?>
Here is a sample of what you might put into your <b>views.py</b>.
<pre>
    return HttpResponse("Hello, world. <?= $message ?> is the polls index.")
</pre>

<?php

$url = getUrl('http://dj4e.pythonanywhere.com/polls1');
if ( $url === false ) return;
$passed = 0;

error_log("Tutorial01 ".$url);
// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

$check = webauto_get_check();

if ( $USER->displayname && stripos($html,$USER->displayname) !== false ) {
    success_out("Found ($USER->displayname) in your html");
    $passed += 1;
} else if ( $check && stripos($html,$check) !== false ) {
    success_out("Found ($check) in your html");
    $passed += 1;
} else if ( $USER->displayname ) {
    error_out("Did not find $USER->displayname or $check in your html");
    error_out("No score sent");
    return;
}

// -------
line_out(' ');

$perfect = 2;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

