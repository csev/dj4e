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
<p class="text-warning"><b>Work only on the above tutorials until you pass this autograder.</b> If you work on later tutorials, your site will not pass this autograder.</p>
<p>
Enter the URL of your Django project on PythonAnywhere.
</p>
<?php
$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

// Speed-of-light: 01 must not have grade_check (from step 05)
$grade_check_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_check_url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    $check = webauto_get_check();
    if ( $check && stripos($html, $check) !== false ) {
        error_out("grade_check detected – that is from a later step. Complete steps in order.");
        line_out(' ');
        return;
    }
}

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
    line_out("Assignments must be completed in order. The root URL should show \"The install worked successfully! Congratulations!\"");
}

line_out(' ');
$perfect = 1;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
if ( $score > 0.0 ) webauto_test_passed($score, $url);
