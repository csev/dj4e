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
