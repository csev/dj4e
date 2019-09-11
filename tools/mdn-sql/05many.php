<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\Util\Mersenne_Twister;

$MAX_UPLOAD_FILE_SIZE = 3*1024*1024;

require_once "sql_util.php";

$oldgrade = $RESULT->grade;

if ( isset($_FILES['database']) ) {
    $fdes = $_FILES['database'];

    // Check to see if they left off the file
    if( $fdes['error'] == 4) {
        $_SESSION['error'] = 'Missing file, make sure to select a file before pressing submit';
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( $fdes['size'] > $MAX_UPLOAD_FILE_SIZE ) {
        $_SESSION['error'] = "Uploaded file must be < ".$OUTPUT->displaySize($MAX_UPLOAD_FILE_SIZE);
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( ! endsWith($fdes['name'],'.sqlite3') ) {
        $_SESSION['error'] = "Uploaded file must have .sqlite3 suffix: ".$fdes['name'];
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( ! isset($fdes['tmp_name']) ) {
        $_SESSION['error'] = "Could not find file on server: ".$fdes['name'];
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( strlen($fdes['tmp_name']) < 1 ) {
        $_SESSION['error'] = "Temporary name not found: ".$fdes['name'];
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    $file = $fdes['tmp_name'];

    $fh = fopen($file,'r');
    $prefix = fread($fh, 100);
    fclose($fh);
    if ( ! startsWith($prefix,'SQLite format 3') ) {
        $_SESSION['error'] = "Uploaded file is not SQLite3 format: ".$fdes['name'];
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    $results = false;
    try {
        $db = new SQLite3($file, SQLITE3_OPEN_READONLY);
    } catch(Exception $ex) {
        $_SESSION['error'] = "SQL Error: ".$ex->getMessage();
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( ! runQuery($db, 'SELECT id, name FROM unesco_category') ) return;
    if ( ! runQuery($db, 'SELECT id, name FROM unesco_states') ) return;
    if ( ! runQuery($db, 'SELECT id, name FROM unesco_region') ) return;
    if ( ! runQuery($db, 'SELECT id, name FROM unesco_iso') ) return;
    if ( ! runQuery($db, 'SELECT id, name FROM unesco_site') ) return;

    // 3 163 5 163 1044 
    if ( ! checkCountTable($db, 'unesco_category', 3) ) return;
    if ( ! checkCountTable($db, 'unesco_states', 163) ) return;
    if ( ! checkCountTable($db, 'unesco_region', 5) ) return;
    if ( ! checkCountTable($db, 'unesco_iso', 163) ) return;
    if ( ! checkCountTable($db, 'unesco_site', 1044) ) return;

    // Look at contents with a WHERE clause
    if ( ! checkCountTable($db, 'unesco_category WHERE name="Mixed"', 1) ) return;
    if ( ! checkCountTable($db, 'unesco_iso WHERE name="gb"', 1) ) return;
    if ( ! checkCountTable($db, 'unesco_region WHERE name="Africa"', 1) ) return;
    if ( ! checkCountTable($db, 'unesco_states WHERE name="India"', 1) ) return;

    // Dig into Sites a bit
    if ( ! checkCountTable($db, 'unesco_site WHERE name="Hawaii Volcanoes National Park"', 1) ) return;
    if ( ! checkCountTable($db, 'unesco_site WHERE name="Hawaii Volcanoes National Park" AND year=1987', 1) ) return;
    if ( ! checkCountTable($db, 'unesco_site WHERE name="Hawaii Volcanoes National Park" AND year=1987 AND area_hectares = 87940.0', 1) ) return;

    // Do a bit of joining
    $query = 'SELECT COUNT(*) FROM unesco_site JOIN unesco_iso ON iso_id=unesco_iso.id WHERE unesco_site.name="Maritime Greenwich" AND unesco_iso.name = "gb"';
    if ( ! checkCountQuery($db, $query, 1) ) return;

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

if ( $RESULT->grade > 0 ) {
    echo('<p class="alert alert-info">Your current grade on this assignment is: '.($RESULT->grade*100.0).'%</p>'."\n");
}

if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}
?>
<p>
<form name="myform" enctype="multipart/form-data" method="post" >
To get credit for this assignment, perform the instructions below and 
upload your SQLite3 database here:<br/>
<input name="database" type="file"> 
(Must have a .sqlite3 suffix)<br/>
<input type="submit">
<p>
Do the assignment at 
<a href="https://www.dj4e.com/assn/dj4e_load.md" target="_blank">https://www.dj4e.com/assn/dj4e_load.md</a> 
</p>
<p>
When the
assignment is complete, upload your <b>db.sqlite3</b> file this auto grader.  Is you are using
PythonAnywhere you will need to download the file and then upload it to this autograder.
</p>
<p>
This autograder checks the schemas and contents of the five tables
(unesco_category, unesco_iso, unesco_region, unesco_site, and unesco_states)
that will be created when the assignment is completed properly.
</p>
</form>
</p>
