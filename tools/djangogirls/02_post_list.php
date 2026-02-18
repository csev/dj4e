<?php
/**
 * Django Girls Milestone 02: Post List
 * Verifies that the home page shows the post list template.
 * Corresponds to: Dynamic data in templates, Django templates chapters.
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls Milestone 02: Post List");
?>
<p>
Assignment:
<a href="../../assn/django-girls/dynamic_data_in_templates/" target="_blank" class="btn btn-info">Dynamic data in templates</a>
<a href="../../assn/django-girls/django_templates/" target="_blank" class="btn btn-info">Django templates</a>
</p>
<p>
Enter the URL of your Django Girls blog (e.g. <code>https://YOURUSERNAME.pythonanywhere.com</code>).
</p>
<?php
nameNote();

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
warn_about_ngrok($url);
$url = trimSlash($url);

webauto_setup();

// Verify grade_check still works
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

// Check main page loads with post list template (may be empty if no posts yet)
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);

// Post list template yields article, .post, or body content when template extends base
$has_structure = stripos($html, 'article') !== false || stripos($html, 'class="post"') !== false ||
     stripos($html, "class='post'") !== false || stripos($html, 'post_list') !== false;
// With 0 posts, for-loop renders nothing but base template; accept reasonable body length
$has_content = strlen($html) > 500 && stripos($html, 'Exception Value') === false && stripos($html, 'Traceback') === false;
if ( $has_structure ) {
    success_out("Post list structure found");
    $passed++;
} elseif ( $has_content ) {
    success_out("Main page loads (post_list view; add posts via admin to see them)");
    $passed++;
} else {
    line_out("Ensure post_list view and template are set up.");
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
