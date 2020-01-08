<?php

$meta_good = true;
line_out("Checking meta tag...");
$dj4e_meta = webauto_get_meta($crawler, "dj4e");
if ( strlen($dj4e_meta) < 1 || $dj4e_meta != $check ) {
    error_out('You seem to be missing the required meta name="dj4e" tag. See above.');
    $meta_good = false;
}

// More meta tags
check_code_and_version($crawler);

// Validate the $crawler code
$dj4e_code = webauto_get_meta($crawler, 'dj4e-code');
if ( strpos($dj4e_code, "4215") !== 0 ) {
    error_out('You seem to be missing the required meta name="dj4e-code" tag.  Check the assignment document.');
    // TODO: Uncomment this after October 2019
    // $meta_good = false;
}

