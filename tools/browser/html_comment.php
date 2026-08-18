<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('html_comment');
browser_handle_secret_post(
    $secret,
    'Correct! You found the HTML comment secret.',
    'That is not the comment secret. Use View Source / DevTools and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

?>
<p>
<b>Secrets in HTML Comments</b>
</p>
<p>
Page authors sometimes leave notes in HTML comments. They do not show on the
rendered page, but they are part of the markup. Find the secret for this
assignment and submit it below.
</p>
<p>Keep looking… the page looks empty of clues on purpose.</p>
<!-- dj4e-secret: <?= htmlentities($secret) ?> -->
<?php browser_submit_form($oldguess); ?>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. View Page Source (right-click → View Page Source), or use Elements and
   expand the markup looking for <!-- ... --> comments.
2. Find the comment that starts with dj4e-secret:
3. Copy the value after the colon into the text field and Submit.

Note: the longer comment at the bottom of this file is a how-to hint;
the graded secret is in the short dj4e-secret comment above the form.
-->
