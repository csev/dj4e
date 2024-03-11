<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\Util\Mersenne_Twister;

require_once "names.php";

// Compute the stuff for the output
$code = $USER->id+$LINK->id+$CONTEXT->id;
$MT = new Mersenne_Twister($code);
$my_names = array();
$my_age = array();
$howmany = $MT->getNext(4,6);
for($i=0; $i < $howmany; $i ++ ) {
    $name = $names[$MT->getNext(0,count($names)-1)];
    $age = $MT->getNext(13,40);
    $sha = strtoupper(bin2hex($name.$age));
    // https://stackoverflow.com/questions/4100488/a-numeric-string-as-array-key-in-php
    $database[$sha.'!'] = array($sha,$name,$age);
}
$sorted = $database;
ksort($sorted);
reset($sorted);
$row = reset($sorted);
$goodsha = $row[0];
$oldgrade = $RESULT->grade;
// die($goodsha);

if ( isset($_POST['sha1']) ) {
    if ( $_POST['sha1'] == '42' ) {
        $_SESSION['debug'] = '42';
    }
    if ( $_POST['sha1'] != $goodsha ) {
        $_SESSION['error'] = "Your code did not match";
        header('Location: '.addSession('index.php'));
        return;
    }

    $gradetosend = 1.0;
    $scorestr = "Your answer is correct, score saved.";
    if ( $dueDate->penalty > 0 ) {
        $gradetosend = $gradetosend * (1.0 - $dueDate->penalty);
        $scorestr = "Effective Score = $gradetosend after ".$dueDate->penalty*100.0." percent late penalty";
    }
    if ( $oldgrade > $gradetosend ) {
        $scorestr = "New score of $gradetosend is < than previous grade of $oldgrade, previous grade kept";
        $gradetosend = $oldgrade;
    }

    // Use LTIX to send the grade back to the LMS.
    $debug_log = array();
    $retval = LTIX::gradeSend($gradetosend, false, $debug_log);
    $_SESSION['debug_log'] = $debug_log;

    if ( $retval === true ) {
        $_SESSION['success'] = $scorestr;
    } else if ( is_string($retval) ) {
        $_SESSION['error'] = "Grade not sent: ".$retval;
    } else {
        echo("<pre>\n");
        var_dump($retval);
        echo("</pre>\n");
        die();
    }

    // Redirect to ourself
    header('Location: '.addSession('index.php'));
    return;
}

// echo($goodsha);
if ( $RESULT->grade > 0 ) {
    echo('<p class="alert alert-info">Your current grade on this assignment is: '.($RESULT->grade*100.0).'%</p>'."\n");
}

if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}
if ( isset($_SESSION['debug']) ) {
    echo("<pre>\n");
    echo("Code=$code\n");
    echo("Howmany=$howmany\n");
    var_dump($sorted);
    echo("</pre>\n");
    unset($_SESSION['debug']);
}
?>
<p>
<form method="post">
To get credit for this assignment, perform the instructions below and 
enter the code you get here: <br/>
<input type="text" size="80" name="sha1">
<input type="submit">
</form>
(Hint: starts with <?= substr($goodsha,0,3) ?>)<br/>
</p>
<h1>Instructions</h1>
<p>
You need to run a sequence of SQL commands to an SQL interpreter.
</p>
<p>
One way to start an interpreter is to run SQLite command line
tool on a Linux system like the bash shell on
<a href="https://www.pythonanywhere.com/" target="_blank">
https://www.pythonanywhere.com/</a>.
<pre>
$ cd ~
$ sqlite3 pitch.sqlite3
SQLite version 3.24.0 2018-06-04 14:10:15
Enter ".help" for usage hints.
sqlite&gt;
</pre>
<p>
If you can't run SQLit on your own computer, you can
use an in-browser SQLite instance at
<a href="https://sqlite.org/fiddle/" target="_blank">
https://sqlite.org/fiddle/</a>.
<p>
Once you have the interpreter available, use an SQL statement
to create a table in the database called "Ages":
<pre>
CREATE TABLE Ages ( 
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
  name VARCHAR(128), 
  age INTEGER
);
</pre>
<p>
Then make sure the table is empty by deleting any rows that 
you previously inserted, and insert these rows and only these rows 
with the following commands:
<pre>
<?php
echo("DELETE FROM Ages;\n");
foreach($database as $row) {
   echo("INSERT INTO Ages (name, age) VALUES ('".$row[1]."', ".$row[2].");\n");
}
?>
</pre>
Once the inserts are done, run the following SQL command:
<pre>
SELECT hex(name || age) AS X FROM Ages ORDER BY X;
</pre>
Find the <b>first</b> row in the resulting record set and enter the long string that looks like 
<b>53656C696E613333</b>.
</p>
<p>
If you are using the SQLite command line interface, you can use the ".quit"
command to exit the program.
</p>
<p>
<b>Note:</b> This assignment must be done using SQLite - in particular, the 
<code>SELECT</code> query above will not work in any other database.  So 
you cannot use MySQL or Oracle for this assignment.
</p>
