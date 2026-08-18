<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('css_hidden');
browser_handle_secret_post(
    $secret,
    'Correct! You found the CSS-hidden secret.',
    'That is not the hidden secret. Use Inspect / DevTools and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

?>
<style>
.dj4e-invisible-clue {
    display: none;
}
</style>
<p>
<b>Finding CSS-Hidden Text</b>
</p>
<p>
Not everything on a page is visible. Sometimes text is still in the document
but hidden with CSS. Use Developer Tools to find the secret on this page,
then submit it below.
</p>
<p>Nothing to see here… or is there?</p>
<p class="dj4e-invisible-clue" id="css-clue"><?= htmlentities($secret) ?></p>
<?php browser_submit_form($oldguess); ?>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. Right-click the page and choose Inspect (or open Elements).
2. Search the DOM for the element with class "dj4e-invisible-clue"
   (Ctrl/Cmd+F in the Elements panel), or look for display: none rules.
3. The secret is the text content of that hidden paragraph.
4. Paste it into the text field and Submit.

You can also temporarily un-check display:none in the Styles panel
to make the text appear on the page.
-->
