<?php
/**
 * Django Girls 03 – admin
 * Verifies Django admin is configured and can log in.
 * Corresponds to: Django admin (includes models)
 */

require_once __DIR__ . "/../crud/webauto.php";

// Generate account and password for this user (deterministic per user+context)
$hash = webauto_get_check_full();
$admin_user = 'dj4e_ola';
$admin_pass = substr($hash, 4, 10);

line_out("Django Girls (2/6)");
?>
<p>
Assignment:
<a href="../../assn/django-girls/django_models/" target="_blank" class="btn btn-info">Django models</a>
<a href="../../assn/django-girls/django_admin/" target="_blank" class="btn btn-info">Django admin</a>
</p>
<p class="text-warning"><b>Work only on the above tutorial(s) until you pass this autograder.</b> If you work on later tutorials, your site will not pass this autograder.</p>
<p>
Create an admin user with <b>this exact account and password</b> so the autograder can log in and explore admin:
</p>
<pre>
Username: <?= htmlentities($admin_user) ?>

Password: <?= htmlentities($admin_pass) ?>
</pre>
<p>
Run <code>python manage.py createsuperuser</code> in the <code>~/djangogirls</code> and create the above account.
The autograder will use these credentials to log in and verify your admin setup.
</p>
<p>
Enter the URL of your Django Girls blog.
</p>
<?php

$url = getUrl('https://YOURUSERNAME.pythonanywhere.com');
if ( $url === false ) return;

$passed = 0;
$pythonanywhere_ok = require_pythonanywhere($url);
$url = trimSlash($url);

webauto_setup();

// Speed-of-light: 03 must not have grade_check (from step 05)
// If grade_check returns a valid page (no error), they have implemented it too early
speed_of_light_check();
$grade_check_url = $url . '/grade_check';
$crawler = webauto_retrieve_url($client, $grade_check_url);
if ( $crawler !== false ) {
    global $webauto_http_status;
    $gc_html = $crawler->html();
    $status = isset($webauto_http_status) ? $webauto_http_status : 0;
    $has_django_error = stripos($gc_html, 'Traceback') !== false || stripos($gc_html, 'Exception Value') !== false;
    $is_http_error = $status >= 400;
    if ( !$has_django_error && !$is_http_error ) {
        speed_of_light_exceeded();
        return;
    }
}
success_out("Speed of light not exceeded (that is a good thing).");

// Speed-of-light: root URL must still show default "Welcome to Django" page, not the blog
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler === false ) return;
$html = webauto_get_html($crawler);
$has_default = stripos($html, 'The install worked successfully') !== false || stripos($html, 'It worked') !== false;
$has_blog = stripos($html, 'Django Girls Blog') !== false;
if ( $has_blog || !$has_default ) {
    error_out("Root URL (/) must still show the default Django welcome page, not the blog. Complete steps in order.");
    line_out(' ');
    return;
}
success_out("Root URL still shows default Django page");
$passed++;

// Admin login page
$admin_url = $url . '/admin/';
$crawler = webauto_retrieve_url($client, $admin_url);
if ( $crawler === false ) return;

$html = webauto_get_html($crawler);
if ( stripos($html, 'Django administration') === false && stripos($html, 'Log in') === false ) {
    error_out("Admin login page not found at /admin/");
} else {
    success_out("Admin login page loads");
    $passed++;
}

// Log in and explore
try {
    $form = webauto_get_form_with_button($crawler, 'Log in');
    webauto_change_form($form, 'username', $admin_user);
    webauto_change_form($form, 'password', $admin_pass);

    $crawler = $client->submit($form);
    $html = webauto_get_html($crawler);

    if ( stripos($html, 'Please correct the error') !== false || ( stripos($html, 'Log in') !== false && stripos($html, 'name="username"') !== false ) ) {
        error_out("Login failed. Create the admin user with: $admin_user / $admin_pass");
    } else {
        success_out("Logged in to admin");
        $passed++;

        // Look for BLOG or Posts (Django Girls blog app)
        if ( stripos($html, 'Posts') !== false || stripos($html, 'BLOG') !== false || stripos($html, 'blog') !== false ) {
            success_out("Posts section visible in admin");
            $passed++;
        }
    }
} catch (Exception $e) {
    error_out("Could not complete admin login check: " . $e->getMessage());
}

line_out(' ');
$perfect = 4;
if ( $passed < 0 ) $passed = 0;
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
