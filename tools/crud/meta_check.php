<?php

$meta_good = true;
line_out("Checking meta tag...");
$wa4e_meta = webauto_get_meta($crawler, "wa4e");
if ( strlen($wa4e_meta) < 1 || $wa4e_meta != $check ) {
    error_out('You seem to be missing the required meta name="wa4e" tag. See above.');
    $meta_good = false;
}

// More meta tags
check_code_and_version($crawler);

// Validate the $crawler code
$wa4e_code = webauto_get_meta($crawler, 'wa4e-code');
if ( strpos($wa4e_code, "4215") !== 0 ) {
    error_out('You seem to be missing the required meta name="wa4e-code" tag.  Check the assignment document.');
    // TODO: Uncomment this after October 2019
    // $meta_good = false;
}

