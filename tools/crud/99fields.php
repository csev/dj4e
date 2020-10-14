<?php
$CRUD_FIELDS_LIST = <<< EOF
{
    "autos" : {
        "key": "autos",
        "key_singular": "auto",
        "article": "an",
        "lookup_plural": "makes",
        "lookup_singular": "make",
        "lookup_article": "a",
        "fields" : [
            { "name" : "mileage", "type" : "i" },
            { "name" : "comments", "type" : "s" }
        ]
    },
    "cats" : {
        "key": "cats",
        "key_singular": "cat",
        "article": "a",
        "lookup_plural": "breeds",
        "lookup_singular": "breed",
        "lookup_article": "a",
        "examples": "(i.e. Tabby, Persian, Maine Coon, Siamese, Manx, etc.)",
        "fields" : [
            { "name" : "weight", "type" : "i" },
            { "name" : "foods", "type" : "s" }
        ]
    },
    "stars" : {
        "key": "stars",
        "key_singular": "star",
        "article": "a",
        "lookup_plural": "types",
        "lookup_singular": "type",
        "lookup_article": "a",
        "fields" : [
            { "name" : "mass", "type" : "i" },
            { "name" : "distance", "type" : "i" }
        ]
    },
    "wizards" : {
        "key": "wizards",
        "key_singular": "wizard",
        "article": "a",
        "lookup_plural": "houses",
        "lookup_singular": "house",
        "lookup_article": "a",
        "fields" : [
            { "name" : "power", "type" : "i" },
            { "name" : "spell", "type" : "s" }
        ]
    },
    "boats" : {
        "key": "boats",
        "key_singular": "boat",
        "article": "a",
        "lookup_plural": "types",
        "lookup_singular": "type",
        "lookup_article": "a",
        "fields" : [
            { "name" : "length", "type" : "i" },
            { "name" : "knots", "type" : "i" }
        ]
    },
    "horses" : {
        "key": "horses",
        "key_singular": "horse",
        "article": "a",
        "lookup_plural": "breeds",
        "lookup_singular": "breed",
        "lookup_article": "a",
        "fields" : [
            { "name" : "height", "type" : "i" },
            { "name" : "weight", "type" : "i" }
        ]
    },
    "shows" : {
        "key": "shows",
        "key_singular": "show",
        "article": "a",
        "lookup_plural": "genres",
        "lookup_singular": "genre",
        "lookup_article": "a",
        "fields" : [
            { "name" : "minutes", "type" : "i" },
            { "name" : "summary", "type" : "s" }
        ]
    },
    "gadgets" : {
        "key": "gadgets",
        "key_singular": "gadget",
        "article": "a",
        "lookup_plural": "brands",
        "lookup_singular": "brand",
        "lookup_article": "a",
        "fields" : [
            { "name" : "price", "type" : "i" },
            { "name" : "year", "type" : "i" },
            { "name" : "notes", "type" : "s" }
        ]
    },
    "cities" : {
        "key": "cities",
        "key_singular": "city",
        "article": "a",
        "lookup_plural": "states",
        "lookup_singular": "state",
        "lookup_article": "a",
        "fields" : [
            { "name" : "population", "type" : "i" },
            { "name" : "slogan", "type" : "s" }
        ]
    }
}
EOF
;

$CRUD_FIELDS = json_decode($CRUD_FIELDS_LIST);

function patchSpec($SPEC) {
    $SPEC->assignment_type = ucwords($SPEC->assignment_type_lower);
    $SPEC->assignment_examples = $SPEC->examples;
    $SPEC->assignment_url_text = $SPEC->assignment_type . " Specification";
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
