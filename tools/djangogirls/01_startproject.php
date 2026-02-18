<?php
/**
 * Django Girls 01 – startproject
 * Verifies that the Django project is created and deployed.
 * Corresponds to: Starting a New Django Project!
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 01 – startproject");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_start_project/" target="_blank" class="btn btn-info">Starting a New Django Project!</a>
</p>
<p>
Enter the URL of your Django project on PythonAnywhere.
</p>
<?php
nameNote();

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

// Site must load (basic deployment)
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
if ( stripos($html, 'Exception Value') !== false || stripos($html, 'Traceback') !== false ) {
    error_out("Site appears to have a Django error.");
} elseif ( stripos($html, 'The install worked successfully! Congratulations!') !== false ) {
    success_out("Found default Django welcome page");
    $passed++;
} else {
    line_out("Expected to find: The install worked successfully! Congratulations!");
}

line_out(' ');
$perfect = 1;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
if ( $score > 0.0 ) webauto_test_passed($score, $url);
