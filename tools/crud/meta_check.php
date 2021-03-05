<?php

$meta_good = true;
line_out("Checking meta tag...");
$dj4e_meta = webauto_get_meta($crawler, "dj4e");
if ( strlen($dj4e_meta) < 1 ) {
    error_out('You seem to be missing the required meta name="dj4e" tag. See above.');
    $meta_good = false;
} else if ( $dj4e_meta != $check ) {
    error_out('Your meta name="dj4e" tag of "'.$dj4e_meta.'" is not correct. See above.');
    $meta_good = false;
}

// More meta tags
check_code_and_version($crawler);

// Validate the $crawler code
// "42"+((Math.floor(d.getTime()/1234567)*123456)+42)
$dj4e_code = webauto_get_meta($crawler, 'dj4e-code');

if ( ! $dj4e_code || strlen($dj4e_code) < 1 ) {
    error_out('You seem to be missing the required meta name="dj4e-code" tag.  Check the assignment document.');
    $meta_good = false;
}  else if ( strpos($dj4e_code, "42-42") === 0 || strlen($dj4e_code) < 14 ) {
    error_out('Your meta name="dj4e-code" tag is not formatted correctly.  Make sure you removed the 42-42 meta tag.');
    $meta_good = false;
} else {
	$then = substr($dj4e_code,2);
	$now = time();
    if ( ($then + 180*24*60*60) < $now ) {
		error_log('Expired dj4e-code '.$dj4e_code.' user:'.$USER->id.' '.$USER->email." ".$USER->displayname);
    	error_out('Your meta name="dj4e-code" tag is out of date.  Check the assignment document.');
    	$meta_good = false;
	}
}

// TODO: Check veriosn too :)
$dj4e_version = webauto_get_meta($crawler, 'dj4e-version');
error_log('dj4e_version '.$dj4e_version);
