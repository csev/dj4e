<?php

header("Content-type:application/json");

function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}

$json_params = file_get_contents("php://input");
if (strlen($json_params) > 0 && isValidJSON($json_params)) {
    $decoded_input = json_decode($json_params);
    error_log("Response: ".json_encode($decoded_input));
    $step = $decoded_input->step->step;
    $response = $decoded_input->response;
} else {
    $step = "P00";
}

switch($step) {

case "P00":
    $retval = '{"step": "P01", "command": "ping", "text": "42"}';
    break;

case "P01":
    $text = $response->text;
    if ( $text == "42" ) error_log("Groovy!");
    $retval = '{"step": "P02", "command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';
    break;

case "P02":
    $text = $response->text;
    $retval = '{"step": "P03", "command": "ping", "text": "42", "message": "Check for correct page load"}';
    break;

case "P03":
    $text = $response->text;
    $retval = '{"step": "P04", "command": "switchurl", "text": "/", "message": "Switch to / url"}';
    break;

case "P04":
    $retval = '{"step": "P05", "command": "ping", "text": "42", "message": "Check for correct page load"}';
    break;

case "P05":
    $text = $response->text;
    $retval = '{"step": "P06", "command": "switchurl", "text": "/broken", "message": "Switch to /broken url"}';
    break;

case "P06":
    $retval = '{"step": "P07", "command": "ping", "text": "42", "message": "Check for correct page load"}';
    break;

case "P07":
    $retval = '{"step": "P02", "command": "switchurl", "text": "/missing", "message": "Switch to /missing url"}';
    break;

default:

    $retval = "Yada!";
    break;
}

error_log("Next step: ".$retval);
echo($retval);
