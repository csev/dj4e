<?php
/**
 * Django Girls Milestone 04: Blog Complete
 * Verifies the full blog with styling, base template, post list, post detail.
 * Corresponds to: CSS, Template extending, Add a Detail Page.
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls Milestone 04: Blog Complete");
?>
<p>
Assignment: Complete the Django Girls tutorial including
<a href="../../assn/django-girls/css/" target="_blank" class="btn btn-info">CSS</a>
<a href="../../assn/django-girls/template_extending/" target="_blank" class="btn btn-info">Template extending</a>
<a href="../../assn/django-girls/extend_your_application/" target="_blank" class="btn btn-info">Add a Detail Page</a>
</p>
<p>
Your blog should have styling, a base template, post list with links, and post detail pages.
</p>
<?php
nameNote();

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

// 1. Grade check
$grade_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
$check = webauto_get_check();
$grade_check_ok = false;
if ( $check && stripos($html, $check) !== false ) {
    $passed++;
    $grade_check_ok = true;
} else {
    error_out("grade_check must return your check string ('$check').");
    error_out("Tests continue below, but no grade will be sent until grade_check passes.");
}

// 2. Main page
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);

// Has CSS (stylesheet link)
if ( stripos($html, 'stylesheet') !== false || stripos($html, '.css') !== false ) {
    success_out("Stylesheet linked");
    $passed++;
}

// Has typical blog structure
if ( stripos($html, 'article') !== false || stripos($html, 'post') !== false ) {
    success_out("Blog structure on main page");
    $passed++;
}

// 3. Post detail
$detail_url = $url . '/post/1/';
$crawler = webauto_retrieve_url($client, $detail_url);
if ( $crawler !== false ) {
    $detail_html = webauto_get_html($crawler);
    if ( stripos($detail_html, '404') === false && stripos($detail_html, 'Page not found') === false ) {
        success_out("Post detail page works");
        $passed++;
        if ( stripos($detail_html, 'article') !== false || stripos($detail_html, 'post') !== false ) {
            $passed++;
        }
    }
}

// 4. Links from list to detail
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    if ( stripos($html, '/post/') !== false ) {
        success_out("Post list links to detail pages");
        $passed++;
    }
}

line_out(' ');
$perfect = 6;
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
