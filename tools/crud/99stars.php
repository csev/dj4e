<?php

require_once "99fields.php";

$SPEC = $CRUD_FIELDS->stars;
$SPEC->assignment_type_lower = 'sample exam';
$SPEC->online = true;
patchSpec($SPEC);

if ( !isset($SPEC_ONLY) ) require_once("99crud.php");
