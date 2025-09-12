<?php // do some grading
$possgrade = 0;
$grade = 0;
$titlematch = false;

$dom = new DOMDocument;
@$dom->loadHTML($data);

print("Checking title tag for correct string...\n");
$titlematch = titleCheck($dom);
if ( $titlematch ) {
    echo("Found correct text in title tag\n");
} else {
    echo("<span class=\"incorrect\">Did not find ... ".htmlentities(getTitleString())." ... in the title tag\n");
    echo("Document will be checked, but the grade will not be recorded...</span>\n");
}

print("Checking Document Structure.\n");
$possgrade++;
if ( tagExists($dom, 'html') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'head') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'body') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'h1') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'strong') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
$count = getTagCount($dom, 'b');
if ( $count >= 1 ) {
    badmessage('The b tag is not recommended for bold text');
} else {
    $grade++;
    goodmessage('Did not find any b (bold) tags');
}
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'em') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
$count = getTagCount($dom, 'i');
if ( $count >= 1 ) {
    badmessage('The i tag is not recommended for italics text');
} else {
    $grade++;
    goodmessage('Did not find any i (italics) tags');
}
progressMessage($grade,$possgrade);

$possgrade++;
if ( tagExists($dom, 'ul') ) $grade++;
progressMessage($grade,$possgrade);

$possgrade++;
$count = getTagCount($dom, 'li');
if ( $count >= 3 ) {
    $grade++;
    goodmessage('Found at least three li tags');
} else {
    badmessage('Wanted at least three li tags, found '.$count."\n");
}
progressMessage($grade,$possgrade);

$possgrade++;
$count = getTagCount($dom, 'a');
if ( $count >= 3 && $count <= 10 ) {
    $grade++;
    goodmessage('Found three a (anchor) tags');
} else {
    badmessage('Wanted three a (anchor) tags, found '.$count."\n");
}
progressMessage($grade,$possgrade);

