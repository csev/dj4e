<?php

require_once "99fields.php";

$SPEC = $CRUD_FIELDS->cats;
$SPEC->assignment_type_lower = 'assignment';
patchSpec($SPEC);

if ( !isset($SPEC_ONLY) ) require_once("99crud.php");
