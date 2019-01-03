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
