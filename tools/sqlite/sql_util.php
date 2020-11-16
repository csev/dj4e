<?php

use \Tsugi\Util\Mersenne_Twister;

require_once "names.php";

function runQuery(&$db, $query) {
    $results = false;
    try {
        $results = @$db->query($query);
    } catch(Exception $ex) {
        $_SESSION['error'] = "SQL Error: ".$ex->getMessage()."<br/> Query: ".$query;
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }

    if ( $results === false ) {
        $_SESSION['error'] = "SQL Query Error: ".$db->lastErrorMsg()."<br/> Query: ".$query;
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
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
    $count = countQuery($db, $query);
    if ( $count < 0 ) {
        $_SESSION['error'] = "Query failed ".$query;
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    if ( $margin < 1 && $count != $expected ) {
        $_SESSION['error'] = "Ran query: ".$query." - Expected $expected records, found $count";
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    $lower = $expected - $margin;
    $upper = $expected + $margin;
    if ( $count < $lower || $count > $upper ) {
        $_SESSION['error'] = "Ran query: ".$query." - Expected between $lower and $upper records, found $count";
        header( 'Location: '.addSession('index.php') ) ;
        return false;
    }
    return true;
}
