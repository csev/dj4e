<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('local_storage');
browser_handle_secret_post(
    $secret,
    'Correct! You found the localStorage secret.',
    'That is not the localStorage secret. Check Application / Storage and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

$storage_key = 'dj4e_browser_clue';

?>
<p>
<b>Application: localStorage</b>
</p>
<p>
Browsers can store key/value data in <code>localStorage</code>. It is not a cookie and
does not show up in the page text. Find the value stored for this assignment
and submit it below.
</p>
<p>A small script on this page writes one localStorage entry when the page loads.</p>
<?php browser_submit_form($oldguess); ?>
<script>
try {
    localStorage.setItem(<?= json_encode($storage_key) ?>, <?= json_encode($secret) ?>);
} catch (e) {
    console.warn('localStorage unavailable', e);
}
</script>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. Open DevTools → Application (Chrome/Edge) or Storage (Firefox).
2. Under Local Storage, select this site's origin.
3. Find the key dj4e_browser_clue and copy its value.
4. Paste into the text field and Submit.

Alternate: in the Console run
localStorage.getItem('dj4e_browser_clue')
-->
