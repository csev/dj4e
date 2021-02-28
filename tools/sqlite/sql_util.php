<?php

use \Tsugi\Util\Mersenne_Twister;

require_once "names.php";

function addQueryCount($msg) {
    global $GOOD_QUERY;
    // $GOOD_QUERY = $GOOD_QUERY - 1;
    if ( $GOOD_QUERY < 1 ) return $msg;
    $retval = "Queries: ".$GOOD_QUERY. ", " . $msg;
    return $retval;
}

function runQuery(&$db, $query) {
    global $GOOD_QUERY;
    $results = false;
    try {
        $results = @$db->query($query);
    } catch(Exception $ex) {
        $_SESSION['error'] = addQueryCount("SQL Error: ".$ex->getMessage()."<br/> Query: ".$query);
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }

    if ( $results === false ) {
        $_SESSION['error'] = addQueryCount("SQL Query Error: ".$db->lastErrorMsg()."<br/> Query: ".$query);
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    $GOOD_QUERY = $GOOD_QUERY + 1;
    return $results;
}

function countQuery(&$db, $query) {
    $results = runQuery($db, $query);
    if ( ! $results ) return -1;
    while ($row = $results->fetchArray()) {
        return($row[0]);
    }
    return -1;
}

function checkCountTable(&$db, $table, $expected, $margin=0) {
    $query = 'SELECT COUNT(*) FROM '.$table;
    return checkCountQuery($db, $query, $expected, $margin);
}

function checkCountQuery(&$db, $query, $expected, $margin=0) {
    global $GOOD_QUERY;
    $count = countQuery($db, $query);
    if ( $count < 0 ) {
        $_SESSION['error'] = addQueryCount("Failed query ".$query);
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    if ( $margin < 1 && $count != $expected ) {
        $_SESSION['error'] = addQueryCount("Failed query: ".$query." - Expected $expected records, found $count");
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    $lower = $expected - $margin;
    $upper = $expected + $margin;
    if ( $count < $lower || $count > $upper ) {
        $_SESSION['error'] = addQueryCount("Failed query: ".$query." - Expected between $lower and $upper records, found $count");
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    return true;
}
