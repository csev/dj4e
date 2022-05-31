<?php

require_once "../crud/webauto.php";

use Goutte\Client;

line_out("Installing Django on PythonAnywhere");

?>
<p>
<a href="../../assn/mdn/paw_skeleton.md" target="_blank">
https://www.dj4e.com/assn/mdn/paw_skeleton.md</a>
</a>
</p>
<p>
If you are building your application on PythonAnywhere, your grading
url should look like:
<pre>
http://mdntutorial.pythonanywhere.com/catalog/
</pre>
<?php

$url = getUrl('https://www.dj4e.com/assn/mdn/paw_skeleton/index.htm');
if ( $url === false ) return;
$passed = 0;

$path = $url;
if ( strpos($url,'index.htm') !== false ) {
    $path = dirname($path);
}

error_log("MDNInstall".$url);
// http://symfony.com/doc/current/components/dom_crawler.html
$client = new Client();
$client->setMaxRedirects(5);
$client->getClient()->setSslVerification(false);

line_out('It is OK for this page to get a 404 error');
$crawler = webauto_get_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
if ( strpos($html, 'ALLOWED_HOSTS') !== false ) {
    error_out('It looks like you forgot to edit the ALLOWED_HOSTS setting');
?>
<p>
You will need to edit the file <b>locallibrary/settings.py</b> and change the following line:
<pre>
ALLOWED_HOSTS = ['*']
</pre>
</p>
<?php
    return;
}
if ( strpos($html, 'The requested resource was not found on this server.') !== false ) {
    error_out('It looks like you set DEBUG = False');
?>
<p>
You should turn debugging on so that you get more detail in the browsers when you have errors in 
your application.
</p>
<p>
You will need to edit the file <b>locallibrary/settings.py</b> and change the following line:
<pre>
DEBUG = True
</pre>
</p>
<?php
    return;
}

webauto_search_for($html, 'Using the URLconf defined in <code>locallibrary.urls</code>');

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}

// -------
line_out(' ');

$perfect = 1;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( $score < 1.0 ) autoToggle();

// Send grade
if ( $score > 0.0 ) webauto_test_passed($score, $url);

