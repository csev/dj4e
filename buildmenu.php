<?php

use \Tsugi\Util\U;

function buildMenu() {
    global $CFG, $USER;
    $home = rtrim($CFG->apphome, '/');
    $flag = $CFG->getExtension('courses_in_urls', false);
    $cid = (int) U::get($_SESSION, 'context_id', 0);
    $prefix = '';
    if ( ! empty($flag) && $cid > 0 ) {
        $allowed = true;
        if ( is_array($flag) ) {
            $email = isset($_SESSION['email']) ? (string) $_SESSION['email'] : '';
            $allowed = false;
            foreach ($flag as $candidate) {
                if ( strcasecmp(trim((string) $candidate), trim($email)) === 0 ) {
                    $allowed = true;
                    break;
                }
            }
        }
        if ( $allowed ) {
            $prefix = '/courses/'.$cid;
        }
    }
    $R = $home . $prefix . '/';
    $T = $CFG->wwwroot . '/';

    $adminmenu = isset($_COOKIE['adminmenu']) && $_COOKIE['adminmenu'] == "true";
    $isInstructor = (isset($USER) && $USER && isset($USER->instructor) && $USER->instructor)
        || (isset($_SESSION['instructor']) && $_SESSION['instructor']);
    $showCalendarDueUi = isset($_SESSION['id'])
        && U::isNotEmpty($CFG->lessons)
        && \Tsugi\Grades\GradeUtil::showDueDates(U::get($_SESSION, 'context_id', 0));
    $set = new \Tsugi\UI\MenuSet();
    $set->setHome($CFG->servicename, $CFG->apphome);

    if ( isset($CFG->lessons) ) {
        $set->addLeft('Lessons', $R.'lessons');
    }
    if ( isset($_SESSION['id']) ) {
        $set->addLeft('Assignments', $R.'assignments');
    }

    if ( isset($_SESSION['id']) ) {
        $submenu = new \Tsugi\UI\Menu();
        $submenu->addLink('Announcements', $R.'announcements');
        $submenu->addLink('Grades', $R.'grades');
        $submenu->addLink('Pages', $R.'pages');
        $submenu->addLink('Files', $R.'files');
        $submenu->addLink('Discussions', $R.'discussions');
        if ( $isInstructor ) {
            $submenu->addLink('Notifications', $R.'notifications');
        }
        $submenu->addLink('Courses', $home.'/coursesredirect.php');
        if ( isset($CFG->google_map_api_key) ) {
            $submenu->addLink('Map', $R.'map');
        }
        $submenu->addLink('Profile', $home.'/profile');
        if ( $showCalendarDueUi ) {
            $submenu->addLink('Calendar', $R.'calendar');
        }
        if ( isset($CFG->badge_path)  ) {
            $submenu->addLink('Badges', $R.'badges');
        }
        if ( file_exists('materials.php') ) {
            $submenu->addLink('Materials', $home.'/materials');
        }
        if ( file_exists('privacy.php') ) {
            $submenu->addLink('Privacy', $home.'/privacy');
        }
        $submenu->addLink('LMS Integration', $T . 'settings');
        if ( isset($CFG->google_classroom_secret) ) {
            $submenu->addLink('Google Classroom', $T.'gclass/login');
        }
        $submenu->addLink('Django Versions', $home.'/versions');
        if ( isset($_COOKIE['adminmenu']) && $_COOKIE['adminmenu'] == "true" ) {
            $submenu->addLink('Administer', $T . 'admin/');
        }
        $submenu->addLink('Logout', $home.'/logout');
        if ( isset($_SESSION['avatar']) ) {
            $set->addRight('<img src="'.$_SESSION['avatar'].'" alt="'.htmlentities(__('User Profile Menu - Includes logout')).'" style="height: 2em;"/>', $submenu);
            // htmlentities($_SESSION['displayname']), $submenu);
        } else {
            $set->addRight(htmlentities($_SESSION['displayname']), $submenu);
        }
    } else {
        $set->addRight('Login', $home.'/login');
        $set->addRight('Courses', $home.'/coursesredirect.php');
    }
    if ( isset($_SESSION['id']) ) {
        $set->addRight(
            '<tsugi-notifications api-url="'. htmlspecialchars($T . 'api/notifications.php') . '" notifications-view-url="'. htmlspecialchars($R . 'notifications') . '" announcements-view-url="'. htmlspecialchars($R . 'announcements') . '"></tsugi-notifications>',
            false,
            true,
            'hidden-xs tsugi-wc-nav-item'
        );
        if ( $showCalendarDueUi ) {
            $set->addRight(
                '<tsugi-calendar-due api-url="'. htmlspecialchars($R . 'calendar/json') . '" lessons-url="'. htmlspecialchars($R . 'calendar') . '" calendar-url="'. htmlspecialchars($R . 'calendar') . '"></tsugi-calendar-due>',
                false,
                true,
                'hidden-xs tsugi-wc-nav-item'
            );
        }
        if ( isset($CFG->tdiscus) && $CFG->tdiscus ) {
            $set->addRight(
                '<tsugi-discussions api-url="'. htmlspecialchars($R . 'discussions/json') . '" discussions-url="'. htmlspecialchars($R . 'discussions') . '"></tsugi-discussions>',
                false,
                true,
                'hidden-xs tsugi-wc-nav-item'
            );
        }
    }   

    return $set;
}

