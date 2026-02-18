<?php
/**
 * Django Girls 05 – html
 * Verifies grade_check, URL routing, and HTML structure.
 * Combines urls + html: at the end of Django URLs/views the code is broken
 * (TemplateDoesNotExist) until you add the post_list template.
 * Corresponds to: Django URLs, Django views, Introduction to HTML
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 05 – html");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_urls/" target="_blank" class="btn btn-info">Django URLs</a>
<a href="../../assn/django-girls/django_views/" target="_blank" class="btn btn-info">Django views</a>
<a href="../../assn/django-girls/html/" target="_blank" class="btn btn-info">Introduction to HTML</a>
</p>
<p class="text-warning"><b>Work only on the above tutorials until you pass this autograder.</b> If you work on later tutorials, your site will not pass this autograder.</p>
<p>
Enter the URL of your Django Girls blog.
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

// Main page: Django Girls Blog, My first post, My second post
$future_detected = false;
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    // Speed-of-light: 05 must not show structure from 07+ or CSS from 08
    $has_future_structure = stripos($html, 'class="post"') !== false || stripos($html, "class='post'") !== false ||
        stripos($html, 'page-header') !== false;
    $has_css_links = stripos($html, 'stylesheet') !== false || stripos($html, 'blog.css') !== false ||
        preg_match('#<link[^>]+\.css#i', $html);
    if ( $has_future_structure || $has_css_links ) {
        error_out("Code from a later step detected (class=\"post\", page-header, or CSS links). Complete steps in order.");
        $future_detected = true;
    }
    if ( !$future_detected && stripos($html, 'Django Girls Blog') !== false ) {
        success_out("Found 'Django Girls Blog'");
        $passed++;
    } elseif ( !$future_detected ) {
        line_out("Expected 'Django Girls Blog' on main page");
    }
    if ( !$future_detected && stripos($html, 'My first post') !== false ) {
        success_out("Found 'My first post'");
        $passed++;
    } else {
        if ( !$future_detected ) line_out("Expected 'My first post' (add posts via admin, ORM chapter)");
    }
    if ( !$future_detected && stripos($html, 'My second post') !== false ) {
        success_out("Found 'My second post'");
        $passed++;
    } else {
        if ( !$future_detected ) line_out("Expected 'My second post' (add posts via admin, ORM chapter)");
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
if ( $future_detected ) {
    error_out("No grade sent – complete steps in order.");
    return;
}
if ( !$grade_check_ok ) {
    error_out("Score above is for feedback only. No grade sent – fix grade_check first.");
    return;
}
if ( $score > 0.0 ) webauto_test_passed($score, $url);
