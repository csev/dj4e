<?php
/**
 * Django Girls 10 – detail
 * Verifies base template, post detail page, and links.
 * Combines Template extending + Add a Detail Page.
 * Corresponds to: Template extending, Add a Detail Page
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 10 – detail");
?>
<p>
Assignment:
<a href="../../assn/django-girls/template_extending/" target="_blank" class="btn btn-info">Template extending</a>
<a href="../../assn/django-girls/extend_your_application/" target="_blank" class="btn btn-info">Add a Detail Page</a>
</p>
<p>
Enter the URL of your Django Girls blog. Ensure you have at least one post in the admin.
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

// Base template (CSS + content) and post list links
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    $has_css = stripos($html, 'stylesheet') !== false || stripos($html, '.css') !== false;
    $has_content = strlen($html) > 500 && stripos($html, 'Exception Value') === false;
    if ( $has_css && $has_content ) {
        success_out("Base template structure (CSS + content)");
        $passed++;
    }
    if ( stripos($html, '/post/') !== false ) {
        success_out("Post list links to detail pages");
        $passed++;
    }
}

// Post detail page loads
$detail_url = $url . '/post/1/';
$crawler = webauto_retrieve_url($client, $detail_url);
if ( $crawler !== false ) {
    $detail_html = webauto_get_html($crawler);
    $is_404 = stripos($detail_html, 'Page not found') !== false || stripos($detail_html, '404') !== false;
    if ( !$is_404 ) {
        success_out("Post detail page /post/1/ loads");
        $passed++;
        if ( stripos($detail_html, 'article') !== false || stripos($detail_html, 'class="post"') !== false ) {
            $passed++;
        }
    }
}

line_out(' ');
$perfect = 5;
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
