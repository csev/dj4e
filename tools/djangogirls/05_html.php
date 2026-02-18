<?php
/**
 * Django Girls 05 – html
 * Verifies basic HTML structure.
 * Corresponds to: Introduction to HTML
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 05 – html");
?>
<p>
Assignment:
<a href="../../assn/django-girls/html/" target="_blank" class="btn btn-info">Introduction to HTML</a>
</p>
<p>
Enter the URL of your Django Girls blog.
</p>
<?php
nameNote();

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

$grade_check_ok = false;
$grade_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    $check = webauto_get_check();
    if ( $check && stripos($html, $check) !== false ) {
        $passed++;
        $grade_check_ok = true;
    } else {
        error_out("grade_check must return your check string ('$check').");
        error_out("Tests continue below, but no grade will be sent until grade_check passes.");
    }
}

// Main page has HTML structure
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    if ( stripos($html, '<html') !== false || stripos($html, '<body') !== false ) {
        success_out("Page has HTML structure");
        $passed++;
    }
}

line_out(' ');
$perfect = 2;
if ( $passed < 0 ) $passed = 0;
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
