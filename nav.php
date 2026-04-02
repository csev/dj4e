<?php

require_once('buildmenu.php');

$set = buildMenu();
$CFG->defaultmenu = $set;

$OUTPUT->bodyStart();
$OUTPUT->topNavSession($set);

$OUTPUT->topNav();
$OUTPUT->flashMessages();
