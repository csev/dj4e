<?php
/**
 * Django Girls 07 – templates
 * Verifies Django templates with dynamic data (post_list).
 * Combines dynamic data in templates + Django templates.
 * Corresponds to: Django ORM, Dynamic data in templates, Django templates
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 07 – templates");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_orm/" target="_blank" class="btn btn-info">Django ORM</a>
<a href="../../assn/django-girls/dynamic_data_in_templates/" target="_blank" class="btn btn-info">Dynamic data in templates</a>
<a href="../../assn/django-girls/django_templates/" target="_blank" class="btn btn-info">Django templates</a>
</p>
<p class="text-warning"><b>Work only on the above tutorials until you pass this autograder.</b> If you work on later tutorials, your site will not pass this autograder.</p>
<p>
Enter the URL of your Django Girls blog. Add posts via admin (see ORM chapter).
</p>
<?php
nameNote(false, true);

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

// Post list template
$future_detected = false;
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    // Speed-of-light: 07 must not show content from step 08 (CSS) or step 10 (detail links)
    $has_css_from_08 = stripos($html, 'stylesheet') !== false || stripos($html, 'blog.css') !== false ||
        preg_match('#<link[^>]+\.css#i', $html);
    $has_detail_links = preg_match('#href=["\'][^"\']*\/post\/\d#', $html) || stripos($html, '"/post/') !== false || stripos($html, "'/post/") !== false;
    if ( $has_css_from_08 || $has_detail_links ) {
        $parts = [];
        if ( $has_css_from_08 ) $parts[] = 'CSS/styling (step 08)';
        if ( $has_detail_links ) $parts[] = 'post detail links (step 10)';
        error_out("Code from a later step detected (" . implode(', ', $parts) . "). Complete steps in order.");
        $future_detected = true;
    }
    $has_structure = !$future_detected && ( stripos($html, 'article') !== false || stripos($html, 'class="post"') !== false ||
        stripos($html, "class='post'") !== false || stripos($html, 'post_list') !== false );
    $has_content = !$future_detected && strlen($html) > 500 && stripos($html, 'Exception Value') === false;
    if ( $has_structure ) {
        success_out("Post list template with dynamic data renders");
        $passed++;
    } elseif ( $has_content ) {
        success_out("Main page loads (add posts via admin to see dynamic data)");
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
if ( $future_detected ) {
    error_out("No grade sent – complete steps in order.");
    return;
}
if ( !$grade_check_ok ) {
    error_out("Score above is for feedback only. No grade sent – fix grade_check first.");
    return;
}
if ( $score > 0.0 ) webauto_test_passed($score, $url);
