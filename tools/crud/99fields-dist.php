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
        "examples": "Tabby, Persian, Maine Coon, Siamese, Manx",
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
        "examples": "Siruis, Riegel, Arcturus, Vega, Polaris",
        "fields" : [
            { "name" : "mass", "type" : "i" },
            { "name" : "distance", "type" : "i" }
        ]
    }
}
EOF
;

require_once "99utils.php";
