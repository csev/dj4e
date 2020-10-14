<?php

require_once "99fields.php";

$keys = array();
foreach($CRUD_FIELDS as $key => $value ) {
    if ( $key == 'autos' || $key == 'cats' || $key == 'stars' ) continue;
    $keys[] = $key;
}

$pos = ($code % count($keys));

$assn = $keys[$pos];

$SPEC = $CRUD_FIELDS->{$assn};
$SPEC->assignment_type_lower = 'exam';
patchSpec($SPEC);

if ( !isset($SPEC_ONLY) ) require_once("99crud.php");
