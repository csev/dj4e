<?php

global $assignment_type, $title_singular, $lookup_lower, $lookup_article;
global $lookup_lower_plural, $main_lower, $main_article, $main_lower_plural, $fields;
global $assignment_type_lower, $reference_implementation;

$assignment_url = "02spec.php?assn=199horses.php";
$assignment_url_text = "Specification";
$reference_implementation = "https://crud.dj4e.com/horses";

$assignment_type = 'Assignment';
$assignment_type = 'Exam';
// $assignment_type == "Sample Exam";
$assignment_type_lower = strtolower($assignment_type);

$main_lower = 'horse';
$main_article = 'a';
$lookup_lower = 'breed';
$lookup_article = 'a';
$assignment_examples = "(i.e. Black Beauty, Seabiscuit, Secretariat, Trigger, Mr. Ed, etc.)";

$title_singular = ucfirst($main_lower);
$title_plural = $title_singular . 's';

// The logical key for lookup is always 'name'
// The logical key for main is always 'nickname'
$fields = array(
    array('name' => 'height', 'type' => 'i'),
    array('name' => 'weight', 'type' => 'i'),
);

$lookup_lower_plural = $lookup_lower . 's';
$lookup_title = ucfirst($lookup_lower);
$lookup_title_plural = ucfirst($lookup_lower_plural);
$main_lower_plural = $main_lower . 's';
$main_title = ucfirst($main_lower);
$main_title_plural = ucfirst($main_lower_plural);

if ( !isset($SPEC_ONLY) ) require_once("02crud.php");
