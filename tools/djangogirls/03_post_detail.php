<?php
/**
 * Django Girls Milestone 03: Post Detail
 * Verifies that individual post pages work (post/<pk>/).
 * Corresponds to: Add a Detail Page chapter.
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls Milestone 03: Post Detail");
?>
<p>
Assignment:
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

// Verify grade_check
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

// Get main page and look for link to post detail
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);

// Look for link to /post/1/ or similar in post list
$has_post_link = stripos($html, '/post/') !== false;
if ( $has_post_link ) {
    success_out("Found link to post detail");
    $passed++;
}

// Try to fetch /post/1/ - post detail page
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
$perfect = 4;
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
