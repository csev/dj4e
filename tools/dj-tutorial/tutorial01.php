<?php

require_once "../crud/webauto.php";

line_out("Autograder Django Tutorial 01");

?>
<p>
Assignment instructions:
<a href="../../assn/dj4e_install52.md" class="btn btn-info" target="_blank">
https://www.dj4e.com/assn/dj4e_install52.md
</a>.
</p>
<?php
nameNote();
$message = $check;
?>
Here is a sample of what you might put into your <b>views.py</b>.
<pre>
    return HttpResponse("Hello, world. <?= $message ?> is the polls index.")
</pre>
Also you will need to edit the file <b>mysite/mysite/settings.py</b> and
edit the <b>ALLOWED_HOSTS</b> to look as follows:
<pre>
ALLOWED_HOSTS = ['*']
</pre>

<?php

$url = getUrl('http://djtutorial.dj4e.com/polls');
if ( $url === false ) return;
$passed = 0;

error_log("Tutorial01 ".$url);

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

