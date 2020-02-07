<?php

require_once "webauto.php";

use Goutte\Client;

$qtext = 'Answer to the Ultimate Question';
?>
<h1>DIY Hello World / Sessions</h1>
<p>
The instructions for this assignment are at 
<a href="../../assn/dj4e_hello.md" target="_blank">dj4e_hello.md</a>
</a>.
This assignment extends the previous Django tutorial Part 4 - and you 
need to keep the polls application running as well as the data for that autograder
to get credit for this assignment.
</p>
<?php
nameNote();
$check = webauto_get_check();

?>
You need to keep the <b>/polls/owner</b> view working as well.
You should already have a question with this text with one answer that is '42'
from the previous assignment:
<pre>
<?= $qtext ?>
</pre>
Then submit your Django base site (i.e. with no path) to this autograder.
</p>
<?php

$url = getUrl('http://djtutorial.dj4e.com');
if ( $url === false ) return;
$passed = 0;
error_log("Hello05 ".$url);
//
// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$owner = $url . '/polls/owner';

$crawler = webauto_retrieve_url($client, $owner);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

if ( $check && stripos($html,$check) !== false ) {
    markTestPassed("Found ($check) in your html");
} else {
    error_out("Did not find $check in your html");
    error_out("No score will be sent, but the test will continue");
}


$sessurl = $url . '/hello';

$crawler = webauto_retrieve_url($client, $sessurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'view count=1');

$crawler = webauto_retrieve_url($client, $sessurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'view count=2');


// -------------------- Send the grade ---------------
line_out(' ');
$perfect = 4;

if ( ! $check ) {
    error_out("No score sent, missing owner name");
    return;
}

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

