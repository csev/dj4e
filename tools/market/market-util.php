<?php


function market_check_basics($client, $base_url, $check, $testrun) {
    global $passed, $failed, $fatal;

    $newurl = webauto_append_suffix($base_url, "/home");
    $crawler = webauto_get_url($client, $newurl);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    // webauto_dump_html($html);

    webauto_search_for($html, "Welcome");

    if ( !$testrun && webauto_dont_want($html, "Chuck's Marketplace") ) {
       error_out("Make sure to change APP_NAME in settings.py to your own application name");
    }

    if ( !$testrun && webauto_dont_want($html, "Chucks Marketplace") ) {
       error_out("Make sure to change APP_NAME in settings.py to your own application name");
    }

    if ( !$testrun && webauto_dont_want($html, "reference implementation") ) {
       error_out("You should folow the assignment instructions versus reverse engineering the assignment from the sample solution output");
    }

    if ( ! webauto_search_for($html, "django.db.backends.mysql") ) {
       error_out("Make sure to switch from SQLite to MySQL in your settings.py");
    }

    if ( !$testrun && ! webauto_search_for($html, $check) ) {
       error_out("Add the DJ4E_CODE value to settings.py as descried above");
    }

    // Check /admin
    $newurl = webauto_append_suffix($base_url, "/admin");

    $crawler = webauto_get_url($client, $newurl);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    webauto_search_for($html, "Django administration");
    webauto_search_for($html, "Username");

    // Check login
    $newurl = webauto_append_suffix($base_url, "/accounts/login");

    $crawler = webauto_get_url($client, $newurl);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    webauto_search_for($html, "Username");
    webauto_search_for($html, "csrfmiddlewaretoken");
    webauto_search_for($html, "bootstrap");

    // check 404

    line_out(" ");
    line_out("Accesing an incorrect url to generate a 404...");

    $newurl = webauto_append_suffix($base_url, "/missing");

    $crawler = webauto_get_url($client, $newurl);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);
    $response = $client->getResponse();
    $status = $response->getStatusCode();

    if ( $status == 404 ) {
        success_out("For this page, getting 'Page may have errors, HTTP status=404' is expected behavior. Nice job!");
        $passed++;
    } else {
        error_out("Accessing the '/missing' url did *not* generate a 404 error");
        error_out("Make sure DEBUG = True in your 'config/settings.py'");
        $failed++;
    }

    webauto_search_for($html, "Page not found");
    webauto_search_for($html, "home/");
    webauto_search_for($html, "admin/");
    webauto_search_for($html, "accounts/");
    webauto_search_for($html, "oauth/");
    webauto_search_for($html, "site");
    webauto_search_for($html, "favicon.ico");
    webauto_search_for($html, "config.urls");

}

// Must be called from a menu page
function market_do_login($client, $crawler, $user, $password) {

    $login_url = webauto_get_url_from_href($crawler,'Login');

    $crawler = webauto_get_url($client, $login_url, "Logging in as $user");
    $html = webauto_get_html($crawler);

    // Use the log_in form
    $form = webauto_get_form_with_button($crawler,'Login', 'Login Locally');
    webauto_change_form($form, 'username', $user);
    webauto_change_form($form, 'password', $password);

    $crawler = webauto_submit_form($client, $form);
    $html = webauto_get_html($crawler); 
    webauto_search_for_menu($html);

    if ( webauto_dont_want($html, "Your username and password didn't match. Please try again.") ) return false;

    return $crawler;
} 

function market_do_logout($client, $crawler) {
    $logout_form = webauto_get_form_with_button($crawler,'Logout', 'Logout Locally');
    $crawler = webauto_submit_form($client, $logout_form);
    $html = webauto_get_html($crawler);
    webauto_search_for_menu($html);
    return $crawler;
}

// Clean up old ads on either account - log out before calling
// and it will log out after deleting the ads
function market_delete_old($client, $url, $check, $testrun) {
    global $passed, $failed, $nograde;
    global $user1account, $user1pw, $user2account, $user2pw, $check;

    $crawler = webauto_get_url($client, $url, "At this point, ads created from prior runs wll be deleted");
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    webauto_search_for_menu($html);


    $crawler = market_do_login($client, $crawler, $user1account, $user1pw);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    // Cleanup old ads
    $save_passed = $passed;
    $save_failed = $failed;
    // preg_match_all("'/ad/[0-9]+/delete'",$html,$matches);
    preg_match_all("'\"([a-z0-9/]*/[0-9]+/delete[^\"]*)\"'",$html,$matches);
    // echo("\n<pre>\n");var_dump($matches);echo("\n</pre>\n");
    
    $deletecount = 0;
    if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
        foreach($matches[1] as $match ) {
            line_out("Loading delete page for old record");
            $crawler = webauto_get_url($client, $match);
            $html = webauto_get_html($crawler);
            $form = webauto_get_form_with_button($crawler,'Yes, delete.');
            $crawler = webauto_submit_form($client, $form);
            $html = webauto_get_html($crawler);
	    $deletecount++;
        } 
    }

    line_bold("$user1account deleted $deletecount ads from previous autograder runs");
    echo("<hr/>");

    $crawler = market_do_logout($client, $crawler);

    header_out("Deleting any old ads belonging to User 2");
    
    $crawler = market_do_login($client, $crawler, $user2account, $user2pw);
    if ( $crawler === false ) return;
    $html = webauto_get_html($crawler);

    $deletecount = 0;
    // preg_match_all("'/ad/[0-9]+/delete'",$html,$matches);
    preg_match_all("'\"([a-z0-9/]*/[0-9]+/delete[^\"]*)\"'",$html,$matches);
    if ( is_array($matches) && isset($matches[1]) && is_array($matches[1]) ) {
        foreach($matches[1] as $match ) {
            line_out("Loading delete page for old record");
            $crawler = webauto_get_url($client, $match);
            $html = webauto_get_html($crawler);
            $form = webauto_get_form_with_button($crawler,'Yes, delete.');
            $crawler = webauto_submit_form($client, $form);
            $html = webauto_get_html($crawler);
            webauto_search_for_menu($html);
	    $deletecount++;
        }
    }

    line_bold("$user2account deleted $deletecount ads from previous autograder runs");
    echo("<hr/>");

    market_do_logout($client, $crawler);

    $passed = $save_passed;
    $failed = $save_failed;

    return true;
}

function market_check_prereq($prereq, $prereq_title, $prereq_grade ) {

    if ( ! is_string($prereq_title) || strlen($prereq_title) < 1 ) return;
    if ( is_numeric($prereq_grade) && $prereq_grade > 0.0 ) return;
?>
<p style="color:red;">
Note that you have not yet finished an earlier assignment:
<?= $prereq_title ?>
</p>
<p style="color:red;">
You need to complete the assignments and earn a grade in assignment order because the autograder (a) expects all the features
of the prior assignment to continue working in later assignments and (b) earlier assignments cannot be autograded if
features from later assignments are detected in your solution.
</p>
<?php 

}

