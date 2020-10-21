<?php

require_once "../crud/webauto.php";

use Goutte\Client;

$qtext = 'Answer to the Ultimate Question';
$cookie_name = 'dj4e_cookie';
?>
<h1>DIY Hello World / Sessions</h1>
<p>
The instructions for this assignment are at
<a href="../../assn/dj4e_hello.md" target="_blank">dj4e_hello.md</a>
</a>.
This assignment adds two new applications (main and hello) to your project
where you built your tutorial solutions.  In addition to
the requirements of the assignment,
you need to keep the <b>/polls/owner</b> view working as well
to keep the autograder happy.
</p>
<?php
nameNote();
$check = webauto_get_check();
?>
</p>
<p>
In addition to the session feature in the above assignment also set a cookie
in your <b>/hello</b> view:
<pre>
resp.set_cookie('<?= $cookie_name ?>', '<?= $check?>', max_age=1000)
</pre>
Remember that to set a cookie in a Django view, you can't just use
the <b>render()</b> shortcut.  Instead you
need to create the <b>HttpResponse</b> and then add the cookie to the response
before returning it from your view.  Take a look at the
<b>dj4e-sample</b> code to see how this can be done.
</p>
Then submit your Django base site (i.e. with no path) to this autograder.
</p>
<?php

$url = getUrl('http://djtutorial.dj4e.com');
if ( $url === false ) return;
$passed = 0;
$send = true;
error_log("Hello05 ".$url);
//
// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);
$client->getClient()->setSslVerification(false);

// Check that top page
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
if (  stripos($html,'Page not found') !== false ) {
    line_out("Your top page is not correct - No score will be sent, but the test will continue");
    $send = false;
} else {
    $passed++;
}

$owner = $url . '/polls/owner';

$crawler = webauto_retrieve_url($client, $owner);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'Hello');

if ( $check && stripos($html,$check) !== false ) {
    markTestPassed("Found ($check) in your html");
} else {
    error_out("Did not find $check in your html - No score will be sent, but the test will continue");
    $send = false;
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

$crawler = webauto_retrieve_url($client, $sessurl);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
webauto_search_for($html, 'view count=3');

// Go after the cookies
$cookieJar = $client->getCookieJar();

line_out(' ');
echo("<hr/>\n");
line_out("Looking for $cookie_name - expecting $check");
$test = $cookieJar->get($cookie_name);
$value = $test ? $test->getValue() : null;

if ( $value == $check ) {
    success_out("Found $cookie_name=".$check);
    $passed = $passed + 1;
} else if ( strlen($value) > 0 ) {
    error_out("Found $cookie_name with incorrect value: ".$value);
} else {
    error_out("Did not find $cookie_name");
    $cookies = $cookieJar->all();
    $count = 0;
    foreach ($cookies as $cookie) {
        if ( $count == 0 ) {
            line_out("Found these cookies:");
        }
        $name       = $cookie->getName();
        $value      = $cookie->getValue();
        line_out(htmlentities($name).'='.htmlentities($value));
        $count = $count + 1;
    }
    if ( $count < 1 ) {
        line_out("No cookies found :(");
    }
}

// -------------------- Send the grade ---------------
line_out(' ');
$perfect = 7;

if ( ! $send ) {
    error_out("No score sent");
    return;
}

$score = webauto_compute_effective_score($perfect, $passed, $penalty);

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

