<?php

require_once "webauto.php";
require_once "names.php";

// TODO: Make this work on 06 07
$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();

$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ' ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";
$meta = '<meta name="dj4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
$userpw = "Meow_" . substr(getMD5(),1,6). '_42';
$useraccount = 'dj4e_user';
line_out("Exploring Django Users (MDN)");
?>
<a href="../../assn/dj4e_hello.md" target="_blank">
https://www.dj4e.com/assn/dj4e_hello.md</a>
</a>
<p>
You should add an identifiying <b>meta</b> tag in your <b>&lt;head&gt;</b> area of each page you generate.
<pre>
<?= htmlentities($meta) ?> 
</pre>
</p>

<?php

$url = getUrl('https://crud.dj4e.com/');
if ( $url === false ) return;
$passed = 0;

$url = trimSlash($url);

webauto_check_test();

webauto_setup();

// Start the actual test
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);

line_out("Checking meta tag...");
$retval = webauto_search_for($html, $meta);
if ( $retval === False ) {
    error_out('You seem to be missing the required meta tag.  Check spacing.');
    error_out('Assignment will not be scored.');
    $passed = -1000;
}

$retval = webauto_search_for($html, 'Hello World');

// -------
line_out(' ');
echo("<!-- Raw score $passed -->\n");
// echo("  -- Raw score $passed \n");
$perfect = 2;
if ( $passed < 0 ) $passed = 0;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

