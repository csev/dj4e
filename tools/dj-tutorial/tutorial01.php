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
?>
Here is a sample of what you might put into your <b>views.py</b>.
<pre>
    return HttpResponse("Hello, world. Jane Instructor / 1ff1de77 is the polls index.")
</pre>

<?php

$url = getUrl('http://dj4e.pythonanywhere.com/polls1');
if ( $url === false ) return;
$grade = 0;

error_log("Tutorial01 ".$url);
line_out("Retrieving ".htmlent_utf8($url)."...");
flush();

// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = $client->request('GET', $url);
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
    error_out("No score sent");
    return;
}

$perfect = 2;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

