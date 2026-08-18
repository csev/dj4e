<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('console_dataset');
browser_handle_secret_post(
    $secret,
    'Correct! You read the secret from the Console.',
    'That is not the console secret. Use Elements + Console and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

?>
<p>
<b>Console: $0 and dataset</b>
</p>
<p>
The Elements panel and the Console work together. Select a node in Elements,
then ask the Console about it. The box below holds a secret — but not as
visible text.
</p>
<div id="console-target"
     data-clue="<?= htmlentities($secret) ?>"
     style="border: 2px dashed #888; padding: 1.5em; max-width: 28em; text-align: center;">
    Select me in Elements, then use the Console.
</div>
<?php browser_submit_form($oldguess); ?>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. Right-click the dashed box and choose Inspect (it becomes $0 in the Console).
2. Open the Console panel.
3. Type: $0.dataset.clue
   and press Enter. (data-clue becomes dataset.clue)
4. Copy the returned string into the text field and Submit.

You can also expand the element's attributes in Elements and read data-clue=
directly — but the point of this puzzle is the Console + $0 workflow.
-->
