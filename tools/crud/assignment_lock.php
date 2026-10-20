<?php

/**
 * Hard lock for an autograder until another resource link in this course
 * is complete at 80% or above.
 *
 * Link setting: lock_until = resource_link_id (lti_link.link_key).
 * "0" or empty means the assignment is open.
 * Instructors can still open a locked assignment. Students cannot.
 */

function dj4e_lock_min_grade() {
    return 0.80;
}

function dj4e_lock_h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function dj4e_current_link_key() {
    global $CFG, $PDOX, $LINK;
    static $key = null;
    if ( $key !== null ) return $key;
    $key = '';
    if ( ! isset($LINK) || ! is_object($LINK) || ! isset($LINK->id) ) return $key;
    if ( ! isset($PDOX) || ! isset($CFG) ) return $key;
    $row = $PDOX->rowDie(
        "SELECT link_key FROM {$CFG->dbprefix}lti_link WHERE link_id = :LID",
        array(':LID' => $LINK->id)
    );
    if ( is_array($row) && isset($row['link_key']) ) {
        $key = trim((string) $row['link_key']);
    }
    return $key;
}

/**
 * The settings dropdown stores an all-digit resource link id as the option
 * label. Keep a non-numeric prefix so the saved value stays the id.
 */
function dj4e_lock_setting_value($key) {
    return 'id:' . $key;
}

function dj4e_lock_key_from_setting($value) {
    if ( ! is_string($value) ) return false;
    $value = trim($value);
    if ( $value === '' || $value === '0' ) return false;
    if ( strncmp($value, 'id:', 3) === 0 ) {
        $value = trim(substr($value, 3));
    } else if ( preg_match('/\(([^)]+)\)\s*$/', $value, $matches) ) {
        $value = trim($matches[1]);
    }
    if ( $value === '' || $value === '0' ) return false;
    return $value;
}

function dj4e_lock_setting() {
    global $LINK;
    if ( ! isset($LINK) || ! is_object($LINK) ) return false;
    $all = $LINK->settingsGetAll();
    $stored = (is_array($all) && array_key_exists('lock_until', $all)) ? $all['lock_until'] : false;
    if ( ! is_string($stored) ) {
        $stored = $LINK->settingsGet('lock_until', false);
    }
    $key = dj4e_lock_key_from_setting(is_string($stored) ? $stored : '');
    if ( $key === false ) return false;
    if ( $key === dj4e_current_link_key() ) return false;
    $canonical = dj4e_lock_setting_value($key);
    if ( is_string($stored) && $stored !== $canonical ) {
        $LINK->settingsSet('lock_until', $canonical);
    }
    return $key;
}

function dj4e_lock_db_title($key) {
    global $CFG, $PDOX, $CONTEXT;
    if ( ! isset($PDOX) || ! isset($CFG) || ! isset($CONTEXT) || ! is_object($CONTEXT) ) {
        return $key;
    }
    $row = $PDOX->rowDie(
        "SELECT title FROM {$CFG->dbprefix}lti_link
         WHERE context_id = :CID AND link_key = :KEY
           AND (deleted IS NULL OR deleted = 0)
         LIMIT 1",
        array(':CID' => $CONTEXT->id, ':KEY' => $key)
    );
    if ( is_array($row) && isset($row['title']) && trim((string) $row['title']) !== '' ) {
        return trim((string) $row['title']);
    }
    return $key;
}

/**
 * Resource links that exist in this course. A launch creates the link,
 * so the published flag is ignored.
 * The lesson outline is only used to label a link that is already here.
 * The current link is omitted. A saved lock target that no longer exists
 * stays in the list so saving settings does not silently clear it.
 *
 * @return array{choices:array<string,string>,titles:array<string,string>}
 */
function dj4e_lock_catalog() {
    global $CFG, $PDOX, $CONTEXT;
    static $catalog = null;
    if ( $catalog !== null ) return $catalog;

    $choices = array();
    $titles = array();
    $current = dj4e_current_link_key();
    $db = array();

    if ( isset($PDOX) && isset($CFG) && isset($CONTEXT) && is_object($CONTEXT) ) {
        $rows = $PDOX->allRowsDie(
            "SELECT link_key, title FROM {$CFG->dbprefix}lti_link
             WHERE context_id = :CID
               AND (deleted IS NULL OR deleted = 0)
             ORDER BY title",
            array(':CID' => $CONTEXT->id)
        );
        if ( is_array($rows) ) {
            foreach ( $rows as $row ) {
                $key = isset($row['link_key']) ? trim((string) $row['link_key']) : '';
                if ( $key === '' || $key === $current || isset($db[$key]) ) continue;
                $title = isset($row['title']) && trim((string) $row['title']) !== ''
                    ? trim((string) $row['title']) : $key;
                $db[$key] = $title;
            }
        }
    }

    if ( isset($CFG->lessons) && $CFG->lessons && count($db) > 0 ) {
        $lessons = new \Tsugi\UI\Lessons($CFG->lessons);
        foreach ( $lessons->enumerateLtiAssignmentItems(true) as $item ) {
            $key = isset($item['resource_link_id']) ? trim((string) $item['resource_link_id']) : '';
            if ( $key === '' || ! isset($db[$key]) || isset($choices[dj4e_lock_setting_value($key)]) ) continue;
            $title = isset($item['item_title']) && trim((string) $item['item_title']) !== ''
                ? trim((string) $item['item_title']) : $db[$key];
            $title = dj4e_lock_visible_title($title, $key);
            $choices[dj4e_lock_setting_value($key)] = $title;
            $titles[$key] = $title;
        }
    }

    foreach ( $db as $key => $title ) {
        $token = dj4e_lock_setting_value($key);
        if ( isset($choices[$token]) ) continue;
        $title = dj4e_lock_visible_title($title, $key);
        $choices[$token] = $title;
        $titles[$key] = $title;
    }

    $saved = dj4e_lock_setting();
    if ( $saved !== false && ! isset($choices[dj4e_lock_setting_value($saved)]) ) {
        $title = 'Assignment not in this course';
        $choices[dj4e_lock_setting_value($saved)] = $title;
        $titles[$saved] = $title;
    }

    $catalog = array('choices' => $choices, 'titles' => $titles);
    return $catalog;
}

function dj4e_lock_grade($key) {
    global $CFG, $PDOX, $CONTEXT, $USER;
    if ( ! isset($PDOX) || ! isset($CFG) || ! isset($CONTEXT) || ! is_object($CONTEXT) ) return null;
    if ( ! isset($USER) || ! is_object($USER) || ! isset($USER->id) ) return null;
    $row = $PDOX->rowDie(
        "SELECT MAX(R.grade) AS grade
         FROM {$CFG->dbprefix}lti_link AS L
         JOIN {$CFG->dbprefix}lti_result AS R ON R.link_id = L.link_id
         WHERE L.context_id = :CID AND L.link_key = :KEY AND R.user_id = :UID
           AND (L.deleted IS NULL OR L.deleted = 0)",
        array(
            ':CID' => $CONTEXT->id,
            ':KEY' => $key,
            ':UID' => $USER->id,
        )
    );
    if ( ! is_array($row) || ! array_key_exists('grade', $row) || $row['grade'] === null || $row['grade'] === '' ) {
        return null;
    }
    return (float) $row['grade'];
}

/**
 * @return array{configured:bool,met:bool,open:bool,title:string,key:string,grade:?float}
 */
function dj4e_assignment_lock() {
    global $USER;
    static $state = null;
    if ( $state !== null ) return $state;

    $open = array(
        'configured' => false,
        'met' => true,
        'open' => true,
        'title' => '',
        'key' => '',
        'grade' => null,
    );
    $key = dj4e_lock_setting();
    if ( $key === false ) {
        $state = $open;
        return $state;
    }

    $catalog = dj4e_lock_catalog();
    $title = isset($catalog['titles'][$key]) ? $catalog['titles'][$key] : dj4e_lock_db_title($key);
    $grade = dj4e_lock_grade($key);
    $met = $grade !== null && ($grade + 0.0000001) >= dj4e_lock_min_grade();
    $instructor = isset($USER) && is_object($USER) && ! empty($USER->instructor);
    $state = array(
        'configured' => true,
        'met' => $met,
        'open' => $met || $instructor,
        'title' => $title,
        'key' => $key,
        'grade' => $grade,
    );
    return $state;
}

function dj4e_lock_visible_title($title, $key) {
    $title = trim((string) $title);
    $key = trim((string) $key);
    if ( $title === '' || $title === $key ) return 'Untitled assignment';
    return $title;
}

function dj4e_lock_rlid_comment($key) {
    $key = str_replace('--', '-', trim((string) $key));
    return '<!-- ' . $key . ' -->';
}

function dj4e_lock_title_html($title, $key) {
    return '<strong>' . dj4e_lock_h($title) . '</strong>' . dj4e_lock_rlid_comment($key);
}

function dj4e_assignment_lock_score_text($lock) {
    if ( $lock['grade'] === null ) {
        return 'You do not have a score on that assignment yet.';
    }
    $percent = number_format(((float) $lock['grade']) * 100.0, 1);
    return 'Your score on that assignment is ' . $percent . '%.';
}

function dj4e_assignment_lock_html($lock) {
    $score = dj4e_lock_h(dj4e_assignment_lock_score_text($lock));
    return '<div class="alert alert-danger" role="alert">'
        . '<p><strong>This assignment is locked and cannot be opened until the following assignment is complete.</strong></p>'
        . '<p>' . dj4e_lock_title_html($lock['title'], $lock['key']) . '</p>'
        . '<p>That assignment must be complete with a score of at least 80%. ' . $score . '</p>'
        . '</div>';
}

function dj4e_assignment_lock_instructor_html($lock) {
    return '<div class="alert alert-warning" role="alert">'
        . '<p><strong>Students are locked out of this assignment</strong> until '
        . dj4e_lock_title_html($lock['title'], $lock['key'])
        . ' is complete with a score of at least 80%.</p>'
        . '</div>';
}

/**
 * Stop a direct request for an assignment script. The tool index renders
 * the same message inside the page and does not include the script.
 */
function dj4e_assignment_lock_stop() {
    $lock = dj4e_assignment_lock();
    if ( $lock['open'] ) return;
    $html = dj4e_assignment_lock_html($lock);
    if ( ! headers_sent() ) {
        echo "<!DOCTYPE html>\n<html><head><meta charset=\"utf-8\"><title>Assignment locked</title></head><body>\n";
        echo $html;
        echo "\n</body></html>\n";
    } else {
        echo $html;
    }
    exit;
}
