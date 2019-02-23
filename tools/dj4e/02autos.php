<?php

global $assignment_type, $title_singular, $assignment_title, $lookup_lower, $lookup_article;
global $lookup_lower_plural, $main_lower, $main_article, $main_lower_plural, $fields;
global $assignment_type_lower, $reference_implementation, $assignment_url;

$assignment_type = 'Assignment';
// $assignment_type = 'Exam';
$assignment_type = "Sample Exam";

$assignment_title = "Autos CRUD";
$title_singular = "Auto";
$title_plural = "Autos";
$main_lower = 'auto';
$main_article = 'an';
$lookup_lower = 'make';
$lookup_article = 'a';

$assignment_url = "../../assn/dj4e_autos.md";
$assignment_url_text = "https://www.dj4e.com/assn/dj4e_autos.md";
$reference_implementation = "https://projects.dj4e.com/autos";

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
