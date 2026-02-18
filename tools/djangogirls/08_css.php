<?php
/**
 * Django Girls 08 – css
 * Verifies stylesheets are linked.
 * Corresponds to: CSS – make it pretty
 */

require_once __DIR__ . "/../crud/webauto.php";

line_out("Django Girls 08 – css");
?>
<p>
Assignment:
<a href="../../assn/django-girls/css/" target="_blank" class="btn btn-info">CSS – make it pretty</a>
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

// Stylesheet linked and fetch to verify content
$future_detected = false;
$crawler = webauto_retrieve_url($client, $url);
if ( $crawler !== false ) {
    $html = webauto_get_html($crawler);
    // Speed-of-light: 08 must not show post detail links from step 10
    if ( preg_match('#href=["\'][^"\']*\/post\/\d#', $html) || stripos($html, '"/post/') !== false || stripos($html, "'/post/") !== false ) {
        error_out("Code from a later step detected (post detail links /post/1/, etc.). Complete steps in order.");
        $future_detected = true;
    }
    if ( !$future_detected && ( stripos($html, 'stylesheet') !== false || stripos($html, '.css') !== false ) ) {
        success_out("Stylesheet linked");
        $passed++;
    }

    // Find and fetch the custom stylesheet (blog.css) - attributes may be in any order
    $css_url = null;
    if ( preg_match_all('#<link\s[^>]*?href=["\']([^"\']+\.css)["\'][^>]*>#i', $html, $matches) ) {
        foreach ( $matches[1] as $css_href ) {
            if ( stripos($css_href, 'bootstrap') === false && stripos($css_href, 'cdn.') === false ) {
                $css_url = (strpos($css_href, 'http') === 0) ? $css_href : rtrim($url, '/') . (strpos($css_href, '/') === 0 ? $css_href : '/' . $css_href);
                break;
            }
        }
    }

    if ( $css_url ) {
        try {
            $httpClient = \Symfony\Component\HttpClient\HttpClient::create(['verify_peer' => false, 'verify_host' => false]);
            $response = $httpClient->request('GET', $css_url);
            $css_content = $response->getContent();
        } catch ( \Exception $e ) {
            $css_content = '';
        }
        if ( strlen($css_content) > 0 ) {
            togglePre(_m('Retrieved CSS'), "<pre>\n" . htmlspecialchars($css_content) . "\n</pre>");
            $expected = [
                '.page-header' => '.page-header',
                '#C25100' => '#C25100 (Django Girls orange)',
                'Lobster' => "'Lobster', cursive",
                '.post' => '.post',
                '.date' => '.date',
                '.btn-secondary' => '.btn-secondary',
            ];
            foreach ( $expected as $needle => $label ) {
                if ( stripos($css_content, $needle) !== false ) {
                    if ( !$future_detected ) {
                        success_out("CSS: $label");
                        $passed++;
                    }
                } elseif ( !$future_detected ) {
                    line_out("CSS: $label (missing)");
                }
            }
        } else {
            line_out("Could not fetch stylesheet from " . htmlspecialchars($css_url));
        }
    } else {
        line_out("Could not find custom stylesheet URL (link to blog.css)");
    }
}

line_out(' ');
$perfect = 8;
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
