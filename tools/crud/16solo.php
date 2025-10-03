<?php

require_once "../crud/webauto.php";
require_once "../crud/names.php";
require_once "../crud/quotes.php";

$code = $USER->id+$CONTEXT->id;
$timecode = $code;
$timecode = $timecode + intval((time() / (60*60)));
$quotepos = intval($timecode % count($quotes)-1);

$quote = $quotes[$quotepos][1];


$sample = "http://localhost:8000/solo1";
$sample = "https://drchuck.pythonanywhere.com/solo1";

?>
<p>
You are to create a new application in your <b>mysite</b> called <b>solo1</b>.  Do this in the bash shell and
do this only once:
<pre>
cd ~/django_projects/mysite
python manage.py startapp solo1
</pre>
You need to add a <b>mysite/solo1/urls.py</b>, update your <b>mysite/solo1/views.py</b>,
<b>mysite/mysite/settings.py</b> and <b>mysite/mysite/urls.py</b> files.  You can look at your own code.
Make sure to edit the <b>mysite/mysite</b> files inserting new lines - do not break these files by replacing
their entire content.  You will need to keep all your applications (i.e. like polls) working without breaking them.
</p>
<p>
In your application you need to create one view that responds to the empty ("") path with the following output:
<pre>
<b><?= $quote ?></b>
</pre>
<p>
This string will be different for each student and change roughly every 60 minutes.
<p>
<?php
$url = getUrl($sample);
if ( $url === false ) return;
warn_about_ngrok($url);

$passed = 0;

webauto_setup();

// Check for polls
$pollsurl = str_replace("/solo1", "/polls", $url);

// Start the actual test
$crawler = webauto_get_url($client, $pollsurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, "Answer");

// Start the actual test
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

webauto_search_for($html, $quote);

// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 1;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

