<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('password_field');
browser_handle_secret_post(
    $secret,
    'Correct! You found the password field value.',
    'That is not the value in the password field. Use Inspect / DevTools and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

?>
<p>
<b>Inspecting a Password Field</b>
</p>
<p>
Browsers hide the characters in a <code>password</code> input so people nearby cannot
read them. Your job is to use browser Developer Tools to discover the value of the
password field below, type it into the text field, and submit.
</p>
<p>
<label for="hidden_secret">Secret (password field):</label><br/>
<input type="password" id="hidden_secret" size="40" readonly
 autocomplete="off" aria-label="Hidden secret password field" />
</p>
<?php browser_submit_form($oldguess); ?>
<script>
document.getElementById('hidden_secret').value = <?= json_encode($secret) ?>;
</script>
<?php browser_instructor_note($secret); ?>
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
