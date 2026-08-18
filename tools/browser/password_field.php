<?php

use \Tsugi\Core\LTIX;

require_once "browser_util.php";

$seed = $USER->id+$LINK->id+$CONTEXT->id;
$secret = browser_readable_secret($seed);

$oldgrade = $RESULT->grade;

if ( count($_POST) > 0 ) {
    $_SESSION['postdata'] = $_POST;

    $guess = isset($_POST['secret']) ? trim($_POST['secret']) : '';
    $gradetosend = ($guess === $secret) ? 1.0 : 0.0;
    LTIX::gradeSendDueDate($gradetosend, $oldgrade, $dueDate);

    if ( $gradetosend >= 1.0 ) {
        $_SESSION['success'] = 'Correct! You found the password field value.';
    } else {
        $_SESSION['error'] = 'That is not the value in the password field. Use Inspect / DevTools and try again.';
    }

    header('Location: '.addSession('index.php'));
    return;
}

if ( $RESULT->grade > 0 ) {
    echo('<p class="alert alert-info">Your current grade on this assignment is: '.($RESULT->grade*100.0).'%</p>'."\n");
}

if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}

$postdata = isset($_SESSION['postdata']) ? $_SESSION['postdata'] : array();
unset($_SESSION['postdata']);
$oldguess = isset($postdata['secret']) ? $postdata['secret'] : '';

?>
<p>
<b>Inspecting a Password Field</b>
</p>
<p>
Browsers hide the characters in a <code>password</code> input so people nearby cannot
read them. Your job is to use browser Developer Tools to discover the value of the
password field below, type it into the text field, and submit.
</p>
<form method="post" autocomplete="off">
<p>
<label for="hidden_secret">Secret (password field):</label><br/>
<input type="password" id="hidden_secret" size="40" readonly
 autocomplete="off" aria-label="Hidden secret password field" />
</p>
<p>
<label for="secret">Enter the secret you found:</label><br/>
<input type="text" id="secret" name="secret" size="40"
 value="<?= htmlentities($oldguess) ?>" autocomplete="off" />
</p>
<input type="submit" value="Submit" />
</form>
<script>
document.getElementById('hidden_secret').value = <?= json_encode($secret) ?>;
</script>
<?php
if ( $USER->instructor ) {
    echo("\n<hr/>\n");
    echo("<p><b>Instructor note:</b> student secret is <code>".htmlentities($secret)."</code></p>\n");
}
?>
<!--
How to solve this puzzle:
1. Right-click the password field and choose Inspect.
2. In Elements / Inspector, find type="password" on that <input>.
3. Edit it to type="text" — the secret appears in the field on the page.
   (You often will not see a useful value="..." attribute; autofill and
   script-filled fields work this way.)
4. Type that exact value into the text field and Submit.

Alternate: select the password input in Elements, then in the Console
run $0.value and press Enter.
-->
