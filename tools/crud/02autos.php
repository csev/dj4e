<?php

global $assignment_type, $title_singular, $lookup_lower, $lookup_article;
global $lookup_lower_plural, $main_lower, $main_article, $main_lower_plural, $fields;
global $assignment_type_lower, $reference_implementation, $assignment_url;

$assignment_type = 'Assignment';
// $assignment_type = 'Exam';
$assignment_type = "Sample Exam";

$main_lower = 'auto';
$main_article = 'an';
$lookup_lower = 'make';
$lookup_article = 'a';

$title_singular = ucfirst($main_lower);
$title_plural = $title_singular . 's';

$assignment_url = "../../assn/dj4e_autos.md";
$assignment_url_text = "Assignment Specification";
$reference_implementation = "https://crud.dj4e.com/autos";
$assignment_examples = "(Ford, Hundai, Toyota, Tata, Audi, etc.)";

// The logical key for lookup is always 'name'
// The logical key for main is always 'nickname'
$fields = array(
    array('name' => 'mileage', 'type' => 'i'),
    array('name' => 'comments', 'type' => 's'),
);

$assignment_type_lower = strtolower($assignment_type);
$lookup_lower_plural = $lookup_lower . 's';
$lookup_title = ucfirst($lookup_lower);
$lookup_title_plural = ucfirst($lookup_lower_plural);
$main_lower_plural = $main_lower . 's';
$main_title = ucfirst($main_lower);
$main_title_plural = ucfirst($main_lower_plural);

if ( !isset($SPEC_ONLY) ) require_once("02crud.php");
