<?php
/**
 * Django Girls 01 – startproject
 * Verifies that the Django project is created and deployed.
 * Corresponds to: Starting a New Django Project!
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls (1/6)");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_start_project/" target="_blank" class="btn btn-info">Starting a New Django Project!</a>
</p>
<p class="text-warning"><b>Work only on the above tutorial(s) until you pass this autograder.</b> If you work on later tutorials, your site will not pass this autograder.</p>
<p>
Enter the URL of your Django project on PythonAnywhere.
</p>
<?php
$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
$pythonanywhere_ok = require_pythonanywhere($url);
$url = trimSlash($url);

webauto_setup();

// Speed-of-light: 01 must not have grade_check (from step 05)
// If grade_check returns a valid page (no error), they have implemented it too early
speed_of_light_check();
$grade_check_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_check_url);
if ( $crawler !== false ) {
    global $webauto_http_status;
    $html = $crawler->html();
    $status = isset($webauto_http_status) ? $webauto_http_status : 0;
    $has_django_error = stripos($html, 'Traceback') !== false || stripos($html, 'Exception Value') !== false;
    $is_http_error = $status >= 400;
    if ( !$has_django_error && !$is_http_error ) {
        speed_of_light_exceeded();
        return;
    }
}
success_out("Speed of light not exceeded (that is a good thing).");

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
    error_out("Default Django welcome page not found at the root URL.");
    line_out("This usualy happens because you have already added code fomr a later tutorial. The root URL should show \"The install worked successfully! Congratulations!\"");
}

line_out(' ');
$perfect = 1;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
if ( $score > 0.0 ) {
    if ( $pythonanywhere_ok ) {
        webauto_test_passed($score, $url);
    } else {
        error_out("No grade sent – this assignment must be run on PythonAnywhere to receive a grade.");
    }
}
