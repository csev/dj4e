<?php
if ( ! isset($OUTPUT) || ! is_object($OUTPUT) ) {
    http_response_code(404);
    exit;
}
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();
