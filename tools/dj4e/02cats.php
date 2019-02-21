<?php

global $assignment_type, $title_singular, $assignment_title, $lookup_lower, $lookup_article;
global $lookup_lower_plural, $main_lower, $main_article, $main_lower_plural, $fields;
global $assignment_type_lower, $reference_implementation;

$title_singular = "Cat";
$title_plural = "Cats";
$assignment_title = "Cats CRUD";

$assignment_url = "02spec.php?assn=02cats.php";
$assignment_url_text = "Specification";
$reference_implementation = "https://projects.dj4e.com/cats";

$assignment_type = 'Assignment';
$assignment_type_lower = 'assignment';
// $assignment_type == 'Exam';
// $assignment_type == "Sample Exam";

$lookup_lower = 'breed';
$lookup_article = 'a';
$main_lower = 'cat';
$main_article = 'an';

// The logical key for lookup is always 'name'
// The logical key for main is always 'nickname'
$fields = array(
    array('name' => 'weight', 'type' => 'i'),
    array('name' => 'foods', 'type' => 's'),
);

$lookup_lower_plural = $lookup_lower . 's';
$lookup_title = ucfirst($lookup_lower);
$lookup_title_plural = ucfirst($lookup_lower_plural);
$main_lower_plural = $main_lower . 's';
$main_title = ucfirst($main_lower);
$main_title_plural = ucfirst($main_lower_plural);

if ( !isset($SPEC_ONLY) ) require_once("02crud.php");
