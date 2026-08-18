<?php

require_once "browser_util.php";

$secret = browser_exercise_secret('network_json');
browser_handle_secret_post(
    $secret,
    'Correct! You found the secret in the Network response.',
    'That is not the network secret. Check the Network panel and try again.'
);
browser_show_grade_and_due();
$oldguess = browser_old_guess();

$api = addSession('network_api.php');

?>
<p>
<b>Network: Reading a JSON Response</b>
</p>
<p>
Modern pages load data with <code>fetch</code> / XHR. The response may never appear
as visible text on the page. Open the Network panel, click the button, and
find the secret in the JSON response.
</p>
<p>
<button type="button" id="load-clue" class="btn btn-primary">Load clue from server</button>
<span id="load-status" style="margin-left: 0.5em; color: #666;"></span>
</p>
<?php browser_submit_form($oldguess); ?>
<script>
document.getElementById('load-clue').addEventListener('click', function () {
    var status = document.getElementById('load-status');
    status.textContent = 'Loading…';
    fetch(<?= json_encode($api) ?>, { credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            // Intentionally do not show data.clue on the page.
            status.textContent = data.ok ? 'Loaded. Check the Network panel.' : 'Unexpected response.';
        })
        .catch(function () {
            status.textContent = 'Request failed.';
        });
});
</script>
<?php browser_instructor_note($secret); ?>
<!--
How to solve this puzzle:
1. Open DevTools → Network.
2. Click "Load clue from server".
3. Click the network_api.php (or similar) request in the list.
4. Open the Response (or Preview) tab and find the JSON "clue" field.
5. Copy that value into the text field and Submit.
-->
