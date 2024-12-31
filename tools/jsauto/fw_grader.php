<?php
$steps = json_decode(<<<EOF
[
    {"step": "P01", "command": "ping", "text": "42"},
    {"step": "P02", "command": "ping", "text": "43"}
]
EOF
, true );

header("Content-type:application/json");


function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}


$json_params = file_get_contents("php://input");
if (strlen($json_params) > 0 && isValidJSON($json_params)) {
  $decoded_input = json_decode($json_params);
} else {
    $step = $steps[0];
    echo(json_encode($step));
    return;
}

$step = $decoded_input->step->step;
$response = $decoded_input->response;

error_log($step);
switch($step) {

case "P01":
    $text = $response->text;
    if ( $text == "42" ) error_log("Groovy!");
    error_log($text);
    echo(json_encode($steps[1]));
    break;

case "P02":
    $text = $response->text;
    error_log($text);
    echo(json_encode($steps[0]));
    break;

default:

    echo("Yada!");
}
