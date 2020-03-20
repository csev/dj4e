<?php

global $assignment_type, $title_singular, $lookup_lower, $lookup_article;
global $lookup_lower_plural, $main_lower, $main_article, $main_lower_plural, $fields;
global $assignment_type_lower, $reference_implementation;

$assignment_url = "02spec.php?assn=194cities.php";
$assignment_url_text = "Specification";
$reference_implementation = "https://crud.dj4e.com/cities";

$assignment_type = 'Exam';
$assignment_type = 'Assignment';
// $assignment_type == "Sample Exam";
$assignment_type_lower = strtolower($assignment_type);

$main_lower = 'city';
$main_article = 'a';
$lookup_lower = 'state';
$lookup_article = 'a';
$assignment_examples = "(i.e. Ann Arbor, Atlanta, Chicago, Dallas, New York, Phoenix, etc.)";

$title_singular = ucfirst($main_lower);
$title_plural = 'cities';

// The logical key for lookup is always 'name'
// The logical key for main is always 'nickname'
$fields = array(
    array('name' => 'population', 'type' => 'i'),
    array('name' => 'slogan', 'type' => 's'),
);

$lookup_lower_plural = $lookup_lower . 's';
$lookup_title = ucfirst($lookup_lower);
$lookup_title_plural = ucfirst($lookup_lower_plural);
$main_lower_plural = 'cities';
$main_title = ucfirst($main_lower);
$main_title_plural = ucfirst($main_lower_plural);

if ( !isset($SPEC_ONLY) ) require_once("02crud.php");
