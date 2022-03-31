<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\Util\Mersenne_Twister;

$MAX_UPLOAD_FILE_SIZE = 1024*1024;

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

    if ( ! runQuery($db, 'SELECT id, name FROM catalog_genre') ) return;
    if ( ! runQuery($db, 'SELECT id, first_name, last_name, date_of_birth, date_of_death FROM catalog_author') ) return;
    if ( ! runQuery($db, 'SELECT id, title, summary, isbn, author_id FROM catalog_book') ) return;
    if ( ! runQuery($db, 'SELECT id, imprint, due_back, status, book_id FROM catalog_bookinstance') ) return;
    if ( ! runQuery($db, 'SELECT id, book_id, genre_id FROM catalog_book_genre') ) return;

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
<a href="https://www.dj4e.com/assn/paw_models.md" target="_blank">https://www.dj4e.com/assn/paw_models.md</a>.
</p>
<p>
When the
assignment is complete, upload the resulting <b>db.sqlite3</b> file to this auto grader.  
If you are using PythonAnywhere download the file using the Files tab and then upload the
file to the autograder.
</p>
<p>
This autograder checks the schemas of the five tables
(catalog_author, catalog_book, catalog_book_genre, catalog_bookinstance, and catalog_genre)
that will be created when the assignment is completed properly.
</p>
</form>
</p>
