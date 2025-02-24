<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Util\U;
use \Tsugi\Util\LTI;
use \Tsugi\Util\Mersenne_Twister;

$MAX_UPLOAD_FILE_SIZE = 3*1024*1024;

require_once "sql_util.php";

$app_name = "polls";
// $app_name = "polls2";

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

    if ( ! U::endsWith($fdes['name'],'.sqlite3') ) {
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
        $_SESSION['error'] = "Temporary name not found: ".$fdes['name'].' - your file may be too large';
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    $file = $fdes['tmp_name'];

    $fh = fopen($file,'r');
    $prefix = fread($fh, 104);
    fclose($fh);
    if ( ! U::startsWith($prefix,'SQLite format 3') ) {
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

    $GOOD_QUERY = 0;
    if ( ! runQuery($db, "SELECT id, question_text, pub_date FROM {$app_name}_question") ) return;
    if ( ! runQuery($db, "SELECT id, choice_text, votes, question_id FROM {$app_name}_choice") ) return;

    // 3 163 5 163 1044
    if ( ! checkCountTable($db, "{$app_name}_question", 26) ) return;
    if ( ! checkCountTable($db, "{$app_name}_choice", 104) ) return;

    // Look at contents with a WHERE clause
    if ( ! checkCountTable($db, "{$app_name}_question WHERE question_text='What is your favourite season'", 1) ) return;
    if ( ! checkCountTable($db, "{$app_name}_question WHERE question_text='What is your name'", 1) ) return;
    if ( ! checkCountTable($db, "{$app_name}_question WHERE question_text='Which command do you use to exit the SQLite comand line tool'", 1) ) return;
    if ( ! checkCountTable($db, "{$app_name}_question WHERE question_text LIKE '%what%'", 15) ) return;

    if ( ! checkCountTable($db, "{$app_name}_choice WHERE choice_text='42'", 2) ) return;
    if ( ! checkCountTable($db, "{$app_name}_choice WHERE choice_text='PHP'", 1) ) return;
    if ( ! checkCountTable($db, "{$app_name}_choice WHERE choice_text='Spicy'", 1) ) return;
    if ( ! checkCountTable($db, "{$app_name}_choice WHERE choice_text='None'", 1) ) return;
    //
    // Do a bit of joining
    $query = "SELECT COUNT(*) FROM {$app_name}_question JOIN {$app_name}_choice ON {$app_name}_question.id = {$app_name}_choice.question_id WHERE {$app_name}_question.question_text = 'What is your quest'";
     
    if ( ! checkCountQuery($db, $query, 3) ) return;

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
<h1>Polls Loading One-to-Many Data</h1>
<p>
<form id="upload_form" name="myform" enctype="multipart/form-data" method="post" >
To get credit for this assignment, perform the instructions below and
upload your SQLite3 database here:<br/>
<input id="upload_file" name="database" type="file">
(Must have a .sqlite3 suffix and be &lt; 3M)<br/>
<input type="submit" value="<?= __('Check database') ?>">
<p>
Do the assignment at
<a href="https://www.dj4e.com/assn/dj4e_batch.md" target="_blank">https://www.dj4e.com/assn/dj4e_batch.md</a>
</p>
<p>
When the
assignment is complete, upload your <b>db.sqlite3</b> file this auto grader.  If you are using
PythonAnywhere you will need to download the file and then upload it to this autograder.
</p>
<p>
This autograder checks the schemas and contents of the two tables
(polls_question and polls_choice)
that will be created when the assignment is completed properly.
</p>
</form>
<?php
$OUTPUT->footerStart();
?>
<script>
$("#upload_form").submit(function() {
  console.log('Checking file size');
  if (window.File && window.FileReader && window.FileList && window.Blob) {
      var file = $('#upload_file')[0].files[0];
      var max = 3000000;
      var maxstr = '3M';
      if (file && file.size > max ) {
          alert("File " + file.name + " must be < " + maxstr);
      return false;
    }
  }
});
</script>
<?php
global $FOOTER_DONE;
$FOOTER_DONE = true;
$OUTPUT->footerEnd();
