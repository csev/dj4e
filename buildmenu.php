<?php

use \Tsugi\Util\U;

function buildMenu() {
    global $CFG;
    $R = $CFG->apphome . '/';
    $T = $CFG->wwwroot . '/';
    $L = $CFG->wwwroot . '/lms/';
    $A = $L . 'announce';

    // Generate URLs using rest_path and addSession
    $json_url = U::addSession($A . '/json.php');
    $dismiss_url = U::addSession($A . '/dismiss.php');
    $view_url = U::addSession($A . '/index.php');

    $adminmenu = isset($_COOKIE['adminmenu']) && $_COOKIE['adminmenu'] == "true";
    $set = new \Tsugi\UI\MenuSet();
    $set->setHome($CFG->servicename, $CFG->apphome);

    if ( isset($CFG->lessons) ) {
        $set->addLeft('Lessons', $L.'lessons');
    }
    if ( isset($CFG->tdiscus) && $CFG->tdiscus ) $set->addLeft('Discussions', $R.'discussions');
    if ( isset($_SESSION['id']) ) {
        $set->addLeft('My Progress', $R.'assignments');
    } else {
        $set->addLeft('Assignments', $R.'assn');
    }

    if ( isset($_SESSION['id']) ) {
        $submenu = new \Tsugi\UI\Menu();
        $submenu->addLink('Profile', $R.'profile');
        if ( isset($CFG->google_map_api_key) ) {
            $submenu->addLink('Map', $R.'map');
        }
        if ( isset($CFG->badge_path)  ) {
            $submenu->addLink('Badges', $R.'badges');
        }
        if ( file_exists('materials.php') ) {
            $submenu->addLink('Materials', $R.'materials');
        }
        if ( file_exists('privacy.php') ) {
            $submenu->addLink('Privacy', $R.'privacy');
        }
        $submenu->addLink('Announcements', $L.'announce');
        $submenu->addLink('Grades', $L.'grades');
        $submenu->addLink('Pages', $L.'pages');
        $submenu->addLink('LMS Integration', $T . 'settings');
        if ( isset($CFG->google_classroom_secret) ) {
            $submenu->addLink('Google Classroom', $T.'gclass/login');
        }
        if ( isset($_COOKIE['adminmenu']) && $_COOKIE['adminmenu'] == "true" ) {
            $submenu->addLink('Administer', $T . 'admin/');
        }
        $submenu->addLink('Django Versions', $R.'versions');
        $submenu->addLink('Logout', $R.'logout');
        if ( isset($_SESSION['avatar']) ) {
            $set->addRight('<img src="'.$_SESSION['avatar'].'" title="'.htmlentities(__('User Profile Menu - Includes logout')).'" style="height: 2em;"/>', $submenu);
            // htmlentities($_SESSION['displayname']), $submenu);
        } else {
            $set->addRight(htmlentities($_SESSION['displayname']), $submenu);
        }
    } else {
        $set->addRight('Login', $T.'login.php');
    }
    $set->addRight('Instructor', 'https://online.dr-chuck.com', true, array('target' => '_self'));

    if ( isset($_SESSION['id']) ) {
        $set->addRight('<tsugi-announce json-url="'. htmlspecialchars($json_url) . '" dismiss-url="'. htmlspecialchars($dismiss_url) . '" view-url="'. htmlspecialchars($view_url) . '"> </tsugi-announce>', false);
    }

    return $set;
}

