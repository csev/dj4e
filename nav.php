<?php

require_once('buildmenu.php');

$set = buildMenu();

$OUTPUT->bodyStart();
$OUTPUT->topNavSession($set);

$OUTPUT->topNav();
$OUTPUT->flashMessages();
