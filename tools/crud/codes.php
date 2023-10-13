<?php
use \Tsugi\Util\U;
use \Tsugi\Core\LTIX;

require_once "../config.php";

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

use \Tsugi\Grades\GradeUtil;
use \Tsugi\Grades\UI;

$menu = new \Tsugi\UI\MenuSet();
$menu->addLeft(__('Back'), 'index.php');

$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav($menu);


$sql = "SELECT R.updated_at, JSON_EXTRACT(R.json, '$.dj4e_codes') AS codes, 
JSON_EXTRACT(R.json, '$.dj4e_versions') AS versions, email, displayname 
FROM lti_result AS R
JOIN lti_user AS U ON R.user_id = U.user_id
WHERE grade = 1 AND link_id = :LNK ORDER BY R.updated_at desc limit 200;";

$sql = "SELECT R.updated_at, JSON_EXTRACT(R.json, '$.dj4e_codes') AS codes, 
JSON_EXTRACT(R.json, '$.dj4e_versions') AS versions, email, displayname 
FROM lti_result AS R
JOIN lti_user AS U ON R.user_id = U.user_id
WHERE grade = 1 AND link_id = :LNK AND 
DATE(R.updated_at) >= CURDATE() - INTERVAL 21 DAY
ORDER BY R.updated_at desc limit 300;";

$rows = $PDOX->allRowsDie($sql,
    array(':LNK' => $LAUNCH->link->id)
);

echo("<p>This is an experiment - under construction - see Chuck for details.</p>\n");

echo("<pre>\n");

echo("Checking versions seen in the past 21 days...\n");

// Lets find the most common version
$versions = array();
foreach ( $rows as $row ) {
    $verstr = U::get($row,'versions','');
    $vers = json_decode($verstr);
    if ( ! is_array($vers) ) continue;
    foreach($vers as $ver) {
        $versions[$ver] = U::get($versions, $ver, 0) + 1;
    }
}

krsort($versions);
$version = False;
$count = 0;
if ( count($versions) > 0 ) {
    foreach ( $versions as $version => $count ) {
        break;
    }
}

echo("Latest version $version ($count)\n");

echo("Checking version anomalies...\n");
$count = 0;
foreach($rows as $row) {
    $verstr = U::get($row,'versions','');
    $vers = json_decode($verstr);
    if ( ! is_array($vers) ) continue;
    if ( $vers == array($version) ) continue;

    $count++;
    echo($row['updated_at'].' ');
    echo($row['codes'].' ');
    echo($row['versions'].' ');
    echo($row['email'].' ');
    echo($row['displayname']);
    echo("\n");
}

if ( $count < 1 ) echo("None found.\n");

echo("Checking code anomalies...\n");
$count = 0;
foreach($rows as $row) {
    $codstr = U::get($row, 'codes', '');
    $codes = json_decode($codstr);
    if ( ! is_array($codes) ) continue;
    if ( count($codes) ) continue;

    $count++;
    echo($row['updated_at'].' ');
    echo($row['codes'].' ');
    echo($row['versions'].' ');
    echo($row['email'].' ');
    echo($row['displayname']);
    echo("\n");
}

if ( $count < 1 ) echo("None found.\n");


$OUTPUT->footerStart();
$OUTPUT->footerEnd();


