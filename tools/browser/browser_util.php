<?php

use \Tsugi\Core\LTIX;

/**
 * Build a stable, readable secret for a student launch.
 *
 * Pattern: adjective + Noun + two digits, e.g. calmMaple42
 */
function browser_readable_secret($seed) {
    $adjectives = array(
        'amber', 'brave', 'calm', 'crisp', 'eager', 'fancy', 'gentle',
        'happy', 'jolly', 'kind', 'lively', 'merry', 'noble', 'proud',
        'quick', 'quiet', 'sunny', 'swift', 'vivid', 'zesty'
    );
    $nouns = array(
        'Apple', 'Birch', 'Cedar', 'Daisy', 'Eagle', 'Falcon', 'Ginger',
        'Hazel', 'Ivory', 'Jade', 'Kite', 'Lemon', 'Maple', 'Nova',
        'Olive', 'Pearl', 'Quill', 'River', 'Stone', 'Tulip'
    );

    $h = hexdec(substr(md5((string)$seed), 0, 8));
    $adj = $adjectives[$h % count($adjectives)];
    $noun = $nouns[($h >> 8) % count($nouns)];
    $num = 10 + (($h >> 16) % 90);
    return $adj.$noun.$num;
}

/** Per-student, per-exercise secret (different puzzle => different answer). */
function browser_exercise_secret($exercise) {
    global $USER, $LINK, $CONTEXT;
    return browser_readable_secret($USER->id+$LINK->id+$CONTEXT->id.$exercise);
}

/**
 * If this is a POST, grade the secret field and redirect.
 * Call at the top of each exercise before any output.
 */
function browser_handle_secret_post($secret, $success_msg, $error_msg) {
    global $RESULT, $dueDate;

    if ( count($_POST) < 1 ) {
        return;
    }

    $_SESSION['postdata'] = $_POST;
    $guess = isset($_POST['secret']) ? trim($_POST['secret']) : '';
    $gradetosend = ($guess === $secret) ? 1.0 : 0.0;
    LTIX::gradeSendDueDate($gradetosend, $RESULT->grade, $dueDate);

    if ( $gradetosend >= 1.0 ) {
        $_SESSION['success'] = $success_msg;
    } else {
        $_SESSION['error'] = $error_msg;
    }

    header('Location: '.addSession('index.php'));
    exit();
}

function browser_show_grade_and_due() {
    global $RESULT, $dueDate;

    if ( $RESULT->grade > 0 ) {
        echo('<p class="alert alert-info">Your current grade on this assignment is: '.($RESULT->grade*100.0).'%</p>'."\n");
    }
    if ( $dueDate->message ) {
        echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
    }
}

function browser_old_guess() {
    $postdata = isset($_SESSION['postdata']) ? $_SESSION['postdata'] : array();
    unset($_SESSION['postdata']);
    return isset($postdata['secret']) ? $postdata['secret'] : '';
}

function browser_submit_form($oldguess) {
?>
<form method="post" autocomplete="off">
<p>
<label for="secret">Enter the secret you found:</label><br/>
<input type="text" id="secret" name="secret" size="40"
 value="<?= htmlentities($oldguess) ?>" autocomplete="off" />
</p>
<input type="submit" value="Submit" />
</form>
<?php
}

function browser_instructor_note($secret) {
    global $USER;
    if ( ! $USER->instructor ) {
        return;
    }
    echo("\n<hr/>\n");
    echo("<p><b>Instructor note:</b> student secret is <code>".htmlentities($secret)."</code></p>\n");
}
