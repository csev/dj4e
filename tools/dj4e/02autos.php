<?php

$lookup_lower = 'make';
$lookup_article = 'a';
$lookup_lower_plural = $lookup_lower . 's';
$main_lower = 'auto';
$main_article = 'an';
$main_lower_plural = $main_lower . 's';

// The logical key for lookup is always 'name'
// The logical key for main is always 'nickname'
$fields = array(
    array('name' => 'mileage', 'type' => 'i'),
    array('name' => 'comments', 'type' => 's'),
);

require_once("02crud.php");
