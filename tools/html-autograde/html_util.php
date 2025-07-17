<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Grades\GradeUtil;
use \Tsugi\Util\LTI;
use \Tsugi\Util\Net;
use \Tsugi\Blob\BlobUtil;

function getTitleString() {
    $retval = getTitleCode();
    return $retval;
}

function getTitleCode() {
    global $USER, $LINK, $CONTEXT;
    $check = md5($USER->id+$LINK->id+$CONTEXT->id);
    return substr($check,0,6);
}

function getTitleCheck() {
    global $USER, $LINK, $CONTEXT;
    $check = md5($USER->id+$LINK->id+$CONTEXT->id);
    if ( $USER->displayname && strlen($USER->displayname) > 0 ) {
        $check = $USER->displayname;
    }
    return $check;
}

function tagExists($dom, $tagname) {
    try {
        $nodes = $dom->getElementsByTagName($tagname);
        if ($nodes->length>=1) {
            goodmessage("Found $tagname tag");
            return true;
        }
    } catch(Exception $ex) {
        badmessage("Error looking for $tagname tag");
        return false;
    }
    badmessage("Did not find $tagname tag...");
    return false;
}

function progressMessage($grade, $possgrade) {
    echo ($grade .' out of ' . $possgrade ."\n\n");
}

function getTag($dom, $tagname) {
    try {
        $nodes = $dom->getElementsByTagName($tagname);
        if ($nodes->length >= 1 ) foreach ($nodes as $node) {
            return $node;
        }
    } catch(Exception $ex) {
        return false;
    }
    return false;
}

function getTagText($dom, $tagname) {
    $node = getTag($dom, $tagname);
    if ( $node ) return $node->nodeValue;
    return $false;
}

function getTagCount($dom, $tagname) {
    try {
        $nodes = $dom->getElementsByTagName($tagname);
        return $nodes->length;
    } catch(Exception $ex) {
        return false;
    }
    return false;
}

function titleCheck($dom) {
    $title = getTagText($dom, 'title');
    if ( ! $title ) return false;
    if ( strpos($title, getTitleCheck()) !== false ) return true;
    if ( strpos($title, getTitleCode()) !== false ) return true;
    return false;
}

function doMessage($condition, $goodmessage=false, $badmessage=false) {
    if ( $condition ) {
        goodmessage($goodmessage);
    } else { 
        badmessage($badmessage);
    }
}

function goodmessage($goodmessage=false) {
    if ( $goodmessage ) echo("<span class=\"correct\">".htmlentities($goodmessage)."</span>\n");
}

function badmessage($badmessage=false) {
    if ( $badmessage ) echo("<span class=\"incorrect\">".htmlentities($badmessage)."</span>\n");
}


/* Return:
 * (1) False - no data
 * (2) A string - it is an error
 * (3) True - things are good and the html is in the session
 */
function checkHTMLPost() {
    if ( ! isset($_GET['url']) ) return false;

    $fdes = $_GET['url'];

    $data = file_get_contents($fdes);
    if ( $data === false ) {
        return 'Could not retrieve file data from '.$fdes;
    }

    if ( strlen($data) > 250000 ) {
        return 'Please upload a file less than 250K';
    }

    $dom = new \DOMDocument;
    $retval = @$dom->loadHTML($data);
    if ( $retval !== true ) {
        
        echo("<pre>\n");
        echo("Unable to parse your HTML\n");
        echo(htmlentities($data));
        echo("\n");
        die();
    }
    
    // Put the data into session to allow us to process this in the GET request
    // echo("<pre>\n");echo(htmlentities($data));die();
    $_SESSION['html_data'] = $data;
    return true;
}

function validateHTML($data) {
    global $CFG;
    $val_error=false;
    if ( $CFG->OFFLINE ) {
        echo("Skipped validator because we are offline\n");
    } else {
        $validator = 'https://validator.w3.org/nu/?out=json&parser=html5';
        echo("Sending ".strlen($data)." characters to the validator.\n$validator ...\n");
        $return = Net::doBody($validator, "POST", $data,
            "Content-type: text/html; charset=utf-8\nUser-Agent: Autograder_www.dj4e.com");

        echo(htmlentities(LTI::jsonIndent($return)));
        $json = json_decode($return);
        if ( !isset($json->messages) || ! is_array($json->messages) ) {
            echo "<span>Did not get a correct response from the validator</span>\n";
            echo "URL: ".htmlentities($validator)."\n";
            echo "Data length: ".strlen($return)."\n";
            echo("<!-- Validator Output:\n");
            echo(htmlentities(LTI::jsonIndent($return)));
            echo("\n-->\n");
            // If validator does not work, bypass the check and assume valid
            echo("Validator not working - assuming valid HTML...\n");
            return true;
        }

        $count = 0;
        foreach($json->messages as $item)
        {
            if($item->type == 'error')
            {
                $count++;
            }
        }
        if ( $count > 0 ) {
           // echo("Validator Output:\n");
           // echo(htmlentities(LTI::jsonIndent($return)));
           unset($_SESSION['html_data']);
           return false;
        }
    }
    return true;
}

function getUrl($sample) {
    global $USER;

    if ( isset($_GET['url']) ) {
        echo('<p><a href="#" onclick="window.location.href = window.location.href; return false;">Re-run this test</a></p>'."\n");
        if ( isset($_SESSION['lti']) ) {
            $retval = GradeUtil::gradeUpdateJson(array("url" => $_GET['url']));
        }
        return $_GET['url'];
    }

    echo('<form>');
    echo('<input type="hidden" name="'.session_name().'" value="'.session_id().'">');
    echo('    Please enter the URL of your web site to grade:<br/>
        <input type="text" name="url" value="'.$sample.'" size="100"><br/>');
    echo('<input type="submit" class="btn btn-primary" value="Evaluate">
        </form>');
    if ( $USER->displayname ) {
        echo("By entering a URL in this field and submitting it for
        grading, you are representing that this is your own work.  Do not submit someone else's
        web site for grading.
        ");
    }

    echo("<p>You can run this autograder as many times as you like and the last submitted
    grade will be recorded.  Make sure to double-check the course Gradebook to verify
    that your grade has been sent.</p>\n");
    return false;
}
