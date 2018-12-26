<?php

require_once "webauto.php";

use Goutte\Client;

line_out("Grading Installing Django");

?>
<p>
For this assignment work through Part 1 of the Django tutorial at
<a href="https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/development_environment" target="_blank">
https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/development_environment</a>.
</a>
</p>
<p>
You will need to edit the file <b>mytestsite/settings.py</b> and change the following line:
<pre>
ALLOWED_HOSTS = ['*']
</pre>

<?php

// $url = getUrl('http://dj4e.pythonanywhere.com/polls1');
$url = getUrl('http://localhost:8888/dj4e/assn/install_pyaw/index.htm');
if ( $url === false ) return;
$passed = 0;

$path = $url;
if ( strpos($url,'index.htm') !== false ) {
    $path = dirname($path);
}

$csspath = $path . '/static/admin/css/fonts.css';
// echo("PATH=$csspath\n");

error_log("MDNInstall".$url);
// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);

$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
webauto_search_for($html, 'The install worked successfully! Congratulations!');

$crawler = webauto_get_url($client, $csspath);
$response = $client->getResponse();
$status = $response->getStatus();
if ( $status != 200 ) {
    error_out("Could not load $csspath, make sure you are serving your static files status=$status");
} else {
    success_out("Loaded $csspath");
    $passed += 1;
}

// -------
line_out(' ');

$perfect = 2;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

