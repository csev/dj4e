<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('data_attribute');
browser_handle_secret_post(
    $secret,
    'Correct! You found the data attribute secret.',
    'That is not the data attribute secret. Use Inspect / DevTools and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

?>
<p>
<b>Reading a data-* Attribute</b>
</p>
<p>
HTML elements can carry extra information in <code>data-*</code> attributes that
never appear as visible text. Inspect the badge below and find the secret.
</p>
<p>
<span id="clue-badge" class="label label-info" data-dj4e-secret="<?= htmlentities($secret) ?>"
 style="font-size: 1.1em; padding: 0.4em 0.7em;">Official Clue Badge</span>
</p>
<?php browser_submit_form($oldguess); ?>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. Right-click the "Official Clue Badge" and choose Inspect.
2. Look at the <span> attributes for data-dj4e-secret="...".
3. Copy that value into the text field and Submit.

Alternate: select the badge in Elements, then in the Console run
$0.dataset.dj4eSecret (dataset names drop "data-" and camelCase the rest).
-->
