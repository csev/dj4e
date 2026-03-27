<?php

require_once "../crud/webauto.php";
require_once "../market/market-util.php";

line_out("Autograder Django Tutorial 01 (localhost.run)");

?>
<div class="alert alert-info">
<p><strong>This autograder only accepts public URLs from <a href="https://localhost.run" target="_blank" rel="noopener noreferrer">localhost.run</a> tunnels</strong>
(for example a hostname ending in <code>.lhr.lt</code>, as printed when you run
<code>ssh -R 80:localhost:8000 localhost.run</code>). Other hosts are not accepted here.</p>
</div>
<p>
Assignment instructions:
<a href="../../assn/dj4e_install52.md" class="btn btn-info" target="_blank" rel="noopener noreferrer" aria-label="Assignment instructions (opens in new tab)">
https://www.dj4e.com/assn/dj4e_install52.md
</a>.
</p>
<?php
nameNote();
$message = $check;
?>
Here is a sample of what you might put into your <b>views.py</b>.
<pre>
    return HttpResponse("Hello, world. <?= $message ?> You're at the polls index.")
</pre>
Also you will need to edit the file <b>mysite/mysite/settings.py</b> and
edit the <b>ALLOWED_HOSTS</b> to look as follows:
<pre>
ALLOWED_HOSTS = ['*']
</pre>

<?php

$url = getUrl('http://djtutorial.dj4e.com/polls');
if ( $url === false ) return;
if ( ! market_url_is_localhost_run_tunnel($url) ) {
    error_out('This assignment only accepts URLs whose hostname is provided by localhost.run (e.g. ending in <strong>.lhr.lt</strong>).');
    error_out('Use the SSH tunnel from the local Django install guide; do not submit other tunnel hosts to this version of the autograder.');
    return;
}
$passed = 0;

error_log("Tutorial01-LocalhostRun ".$url);

webauto_setup();

$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

$check = webauto_get_check();

if ( $check && stripos($html,$check) !== false ) {
    success_out("Found ($check) in your html");
    $passed += 1;
} else {
    error_out("Did not find $check in your html");
    error_out("No score sent");
    return;
}

// -------
line_out(' ');

$perfect = 2;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

