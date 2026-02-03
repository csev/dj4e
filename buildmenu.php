<?php

use \Tsugi\Util\U;

function buildMenu() {
    global $CFG;
    $R = $CFG->apphome . '/';
    $T = $CFG->wwwroot . '/';

    $adminmenu = isset($_COOKIE['adminmenu']) && $_COOKIE['adminmenu'] == "true";
    $set = new \Tsugi\UI\MenuSet();
    $set->setHome($CFG->servicename, $CFG->apphome);

    if ( isset($CFG->lessons) ) {
        $set->addLeft('Lessons', $R.'lessons');
        // $set->addLeft('Lessons', $R.'/tsugi/lms/lessons');
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
        $submenu->addLink('Announcements', $R.'announcements');
        $submenu->addLink('Notifications', $R.'notifications');
        $submenu->addLink('Grades', $R.'grades');
        $submenu->addLink('Pages', $R.'pages');
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
        $set->addRight('Login', $R.'login');
    }
    $set->addRight('Instructor', 'https://online.dr-chuck.com', true, array('target' => '_self'));

    if ( isset($_SESSION['id']) ) {
        $set->addRight('<tsugi-notifications api-url="'. htmlspecialchars($T . 'api/notifications.php') . '" notifications-view-url="'. htmlspecialchars($R . 'notifications') . '" announcements-view-url="'. htmlspecialchars($R . 'announcements') . '"></tsugi-notifications>', false);
    }   

    return $set;
}

