<?php
/**
 * These are some configuration variables that are not secure / sensitive
 *
 * This file is included at the end of tsugi/config.php
 */

// This is how the system will refer to itself.
$CFG->servicename = 'DJ4E';
$CFG->servicedesc = false;

// Theme like Django
$CFG->theme = array(
    "primary" => "#0a4b33", //default color for nav background, splash background, buttons, text of tool menu
    "secondary" => "#EEEEEE", // Nav text and nav item border color, background of tool menu
    "text" => "#111111", // Standard copy color
    "text-light" => "#5E5E5E", // A lighter version of the standard text color for elements like "small"
    "font-url" => "https://fonts.googleapis.com/css?family=Roboto:400", // Optional custom font url for using Google fonts
    "font-family" => "'Roboto', Corbel, Avenir, 'Lucida Grande', 'Lucida Sans', sans-serif", // Font family
    "font-size" => "14px", // This is the base font size used for body copy. Headers,etc. are scaled off this value
);

$CFG->context_title = "Django for Everybody";

$CFG->giftquizzes = $CFG->dirroot.'/../dj4e-private/quiz';

$CFG->youtube_url = $CFG->apphome . '/mod/youtube/';

$CFG->tdiscus = $CFG->apphome . '/mod/tdiscus/';

$CFG->launcherror = $CFG->apphome . "/launcherror";

$CFG->lessons = $CFG->dirroot.'/../lessons.json';
$CFG->youtube_playlist = 'PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z';

$CFG->setExtension('lessons2_enable', true);
$CFG->setExtension('lessons_debug_conversion', false);
$CFG->lessons = $CFG->dirroot.'/../lessons-items.json';
// $CFG->lessons = $CFG->dirroot.'/../lessons-mini.json';

$CFG->google_login_redirect = $CFG->apphome . "/login";

$CFG->service_worker = true;

$CFG->setExtension('canvas_assignment_extension', true);

$CFG->setExtension('django_version', '5.2');
$CFG->setExtension('django_version_short', '52');

$buildmenu = $CFG->dirroot."/../buildmenu.php";
if ( file_exists($buildmenu) ) {
    require_once $buildmenu;
    $CFG->defaultmenu = buildMenu();
}


