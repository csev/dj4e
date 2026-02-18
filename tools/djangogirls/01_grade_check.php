<?php
/**
 * Django Girls Milestone 01: Grade Check
 * Verifies that grade_check view returns the student's check string.
 * Corresponds to: Django views, Django URLs chapters.
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls Milestone 01: Grade Check");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_views/" target="_blank" class="btn btn-info">Django views</a>
<a href="../../assn/django-girls/django_urls/" target="_blank" class="btn btn-info">Django URLs</a>
</p>
<p>
Enter the URL of your Django Girls blog deployed on PythonAnywhere (e.g. <code>https://YOURUSERNAME.pythonanywhere.com</code>).
</p>
<?php
nameNote();

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

// Hit grade_check endpoint
$grade_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
$check = webauto_get_check();

$grade_check_ok = false;
if ( $check && stripos($html, $check) !== false ) {
    success_out("Found your check string ($check) in grade_check response");
    $passed++;
    $grade_check_ok = true;
} else {
    error_out("grade_check must return your check string. Expected to find '$check' in the response.");
    error_out("Update views.py: def grade_check(request): return HttpResponse(\"$check\")");
    error_out("Tests continue below, but no grade will be sent until grade_check passes.");
}

// Also verify main page loads (optional soft check)
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    line_out("Main page loads successfully");
}

line_out(' ');
$perfect = 1;
$score = webauto_compute_effective_score($perfect, $passed, $penalty);

if ( webauto_testrun($url) ) {
    error_out("Not graded - sample solution");
    return;
}
if ( !$grade_check_ok ) {
    error_out("Score above is for feedback only. No grade sent – fix grade_check first.");
    return;
}
if ( $score > 0.0 ) webauto_test_passed($score, $url);
