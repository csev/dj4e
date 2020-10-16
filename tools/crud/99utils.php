<?php

$CRUD_FIELDS = json_decode($CRUD_FIELDS_LIST);

function patchSpec($SPEC) {
    $SPEC->assignment_type = ucwords($SPEC->assignment_type_lower);
    $SPEC->assignment_examples = $SPEC->examples;
    $SPEC->assignment_url_text = $SPEC->assignment_type . " Specification";
    $SPEC->assignment_url = "99spec.php?assn=".urlencode(base64_encode($SPEC->key))."&type=".urlencode(base64_encode($SPEC->assignment_type_lower));
}

function prePatchSpec($SPEC) {
    $SPEC->reference_implementation = "https://crud.dj4e.com/".$SPEC->key;
    $SPEC->lookup_lower = $SPEC->lookup_singular;
    $SPEC->main_lower = $SPEC->key_singular;
    $SPEC->main_article = $SPEC->article;
    $SPEC->title_singular = ucfirst($SPEC->key_singular);
    $SPEC->title_plural = $SPEC->title_singular . 's';
    $SPEC->lookup_lower_plural = $SPEC->lookup_plural;
    $SPEC->lookup_title = ucfirst($SPEC->lookup_lower);
    $SPEC->lookup_title_plural = ucfirst($SPEC->lookup_lower_plural);
    $SPEC->main_lower_plural = $SPEC->main_lower . 's';
    $SPEC->main_title = ucfirst($SPEC->main_lower);
    $SPEC->main_title_plural = ucfirst($SPEC->main_lower_plural);
}

foreach($CRUD_FIELDS as $key => $value) {
    prePatchSpec($CRUD_FIELDS->{$key});
}
