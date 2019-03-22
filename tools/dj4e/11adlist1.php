<?php

require_once "webauto.php";
require_once "names.php";

use Goutte\Client;

$code = $USER->id+$CONTEXT->id;

$check = webauto_get_check_full();

$MT = new \Tsugi\Util\Mersenne_Twister($code);
$shuffled = $MT->shuffle($names);
$first_name = $shuffled[0];
$last_name = $shuffled[1];
$title_name = $shuffled[3];
$full_name = $first_name . ' ' . $last_name;
$last_first = $last_name . ', ' . $first_name;
$book_title = "How the Number 42 and $title_name are Connected";
$meta = '<meta name="wa4e" content="'.$check.'">';

$adminpw = substr(getMD5(),4,9);
$userpw = "Meow_" . substr(getMD5(),1,6). '_42';
$useraccount = 'dj4e_user';
line_out("Building Classified Ad Site #1");
?>
<a href="../../assn/dj4e_ads1.md" target="_blank">
https://www.dj4e.com/assn/dj4e_ads1.md</a>
</a>
<p>
Under construction.
</p>
