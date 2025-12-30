<?php
\Tsugi\Core\LTIX::getConnection();

use \Tsugi\Util\U;
use \Tsugi\Grades\GradeUtil;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Core\LTIX;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$ngrok_fails = array(
    "ngrok.com/signup",
    "Sign up for an ngrok account",
    "ngrok account and install your authtoken",
);

// Get any due date information
$dueDate = SettingsForm::getDueDate();
$penalty = $dueDate->penalty;

if ( $dueDate->message ) {
    echo('<p style="color:red;">'.$dueDate->message.'</p>'."\n");
}

function webauto_setup() {
    global $client;
    $client = new HttpBrowser(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
    // $client = new Client();
    // $client->setMaxRedirects(5);
    // $client->getClient()->setSslVerification(false);
}

function webauto_get_html($crawler, $showSource=false) {
    global $ngrok_fails;
    try {
        $html = $crawler->html();
    }
    catch (Exception $e) {
        error_out("Could not find HTML ".$e->getMessage());
        error_log("Could not find HTML ".$e->getMessage());
        throw new Exception("Could not retrieve HTML from page");
    }
    if ( strpos($html,"<th>Exception Value:</th>") > 0 ) {
        $title = false;
        try {
            $nodeValues = $crawler->filterXPath('//title')->each(function ($node, $i) {
                return $node->text();
            });
            if ( is_array($nodeValues) && count($nodeValues) ) $title = $nodeValues[0];
        } catch(Exception $e) {
            echo("<p>Badly formatted URL</p>\n");
        }
        line_out("It appears that there is a Django error on this page");
        if ( $title ) error_out($title);
    }
    // Check for ngrok failures
    $ngrok_fail = false;
    foreach($ngrok_fails as $fail) {
        if ( stripos($html, $fail) !== false ) $ngrok_fail = true;
    }
    if ( $ngrok_fail ) {
        error_out("It appears that your ngrok tunnel is not working properly.");
    }
    showHTML("Show retrieved page",$html, $showSource);

    // https://stackoverflow.com/questions/1084741/regexp-to-strip-html-comments
    $html = preg_replace('/<!--(.*)-->/Uis', '', $html);
    return $html;
}

function webauto_get_meta($crawler, $name) {
    try {
        $retval = $crawler->filterXpath('//meta[@name="'.$name.'"]')->attr('content');
        if ( $retval == '42-42' ) $retval = '';
    } catch(Exception $e) {
        $retval = '';
    }
    return $retval;
}

function togglePre($title, $html) {
    global $div_id;
    global $base_url_path;
    $div_id = $div_id + 1;
    $text = _m('Show/Hide Retrieved Page');
    $detail = _m('characters of HTML retrieved');
    if ( $base_url_path ) {
        $host = parse_url($base_url_path, PHP_URL_HOST);
        $html = str_replace('<head>','<head><base href="https://'.$host.'">',$html);
    }
    echo("<script> var retrieve_".$div_id." = '".base64_encode($html)."';</script>\n");
    echo('<strong>'.htmlpre_utf8($title));
    echo(' '.strlen($html).' '.$detail."\n");
    echo('<a href="#" onclick="sendToIframe('.$div_id.', atob(retrieve_'.$div_id.'));dataToggle('."'".$div_id."'".');');
    echo(';return false;" class="btn btn-primary">');
    echo($text."</a></strong>\n");
    echo('<iframe id="'.$div_id.'" style="display:none; border: solid green 3px; width:90%; height: 300px;">'."\n");
    echo("<pre>\n");
    echo(htmlpre_utf8($html));
    echo("</pre>\n");
    echo("</iframe><br/>\n");
}

function showHTML($message, $html, $showSource=false) {
    global $OUTPUT;
    global $webauto_http_status;
    global $SHOW_SOURCE;
    global $WEBAUTO_EXPECT_ERROR;

    if ( $showSource || (isset($SHOW_SOURCE) && $SHOW_SOURCE)) {
        togglePre("", "<pre>\n".htmlentities($html)."\n</pre>\n");
    } else {
        togglePre("", $html);
    }
    $pos = strpos($html,'<b>Fatal error</b>');
    if ( isset($WEBAUTO_EXPECT_ERROR) && is_array($WEBAUTO_EXPECT_ERROR) && in_array($webauto_http_status, $WEBAUTO_EXPECT_ERROR) ) {
        line_out("Page returned exptected HTTP status=$webauto_http_status");
    } else if ( $webauto_http_status != 200 ) {
        error_out("Page may have errors, HTTP status=$webauto_http_status");
    } else if ( $pos !== false ) {
        error_out("Your application seems to have a fatal error");
    }
}

function getMD5() {
    global $USER, $LINK, $CONTEXT;
    $check = md5($USER->id+$CONTEXT->id);
    return $check;
}

function getCheck() {
    $check = substr(getMD5(),0,8);
    return $check;
}

function titleNote() {
    nameNote(true);
}

function nameNote($title=false) {
    global $USER, $LINK, $CONTEXT;
    global $check;
    // $check = substr(md5($USER->id+$LINK->id+$CONTEXT->id),0,8);
    $check = webauto_get_check();
?>
<p>
To receive a grade for this assignment, include
<?php
echo("this string <strong>".$check."</strong> \n");
if ( $title ) {
    echo('in the &lt;title&gt; tag in all the pages of your application.');
} else {
    echo('on the pages of your application.');
}
?>
</p>
<?php
}

function getUrl($sample, $SECONDS_BEFORE_RETRY=0) {
    global $USER, $OUTPUT, $access_code;
    global $base_url_path, $URL_IN_USE;
    global $SECONDS_BEFORE_RETRY;
    global $passed, $failed, $nograde;

    if ( !isset($passed) ) $passed = 0;
    if ( !isset($failed) ) $failed = 0;
    if ( !isset($nograde) ) $nograde = false;

    if ( isset($access_code) && $access_code ) {
        if ( isset($_GET['code']) ) {
            if ( $_GET['code'] != $access_code ) {
                die('Bad access code');
            }
        } else {
            echo('<form>Please enter the access code:
            <input type="text" name="code"><br/>
            <input type="submit" class="btn btn-primary" value="Access">
            </form>');
            return false;
        }
    }

    if ( ! isset($SECONDS_BEFORE_RETRY) ) $SECONDS_BEFORE_RETRY = 0;
    if ( $SECONDS_BEFORE_RETRY > 2 ) {
        echo('<script>
            var seconds_before_retry = '.$SECONDS_BEFORE_RETRY.';
            var first_update = true;
            function decrement_counter() {
                seconds_before_retry--;
                if ( seconds_before_retry > 0 ) {
                    let minutes = Math.round(seconds_before_retry/60);
                    console.log("Countdown", seconds_before_retry, "seconds", minutes, "minutes");
                    if ( seconds_before_retry % 10 == 0 ) {
                        console.log("This is a good time to hand-test your application while you are waiting.  Most assignments include instructions on how to hand-test your application.");
                    }
                    if ( minutes > 1 ) {
                        document.getElementById("countdown").textContent = minutes+" minutes";
                    } else if ( first_update || seconds_before_retry <= 10 || ( seconds_before_retry > 10 && seconds_before_retry % 10 == 0 ) ) {
                        document.getElementById("countdown").textContent = seconds_before_retry+" seconds";
                    }
                    first_update = false;
                    setTimeout(decrement_counter, "1000");
                } else {
                    document.getElementById("test-rerun").removeAttribute("disabled");
                    document.getElementById("test-rerun").textContent = "Submit";
                }
            }
            setTimeout(decrement_counter, "1000");

            </script>
        ');
    }

    if ( isset($_GET['url']) ) {
        echo('<p><a href="#" class="btn btn-primary" id="test-rerun" ');
        if ( $SECONDS_BEFORE_RETRY > 2 && ! $USER->instructor ) {
            echo(' disabled ');
        }
        echo('onclick="$(\'#test-rerun\').text(\'Test running...\');greyOut();');
        echo('window.location.href = window.location.href; return false;">');
        if ( $SECONDS_BEFORE_RETRY > 2 ) {
            echo('Please wait <span id="countdown">... </span> (rate limit)');
        } else {
            echo("Re-run this test");
        }
        echo("</a></p>\n");
        echo('<script>
            function hideBelow() {
                const marker = document.getElementById("disappear-start");

                let sibling = marker.nextSibling;
                while (sibling) {
                    let next = sibling.nextSibling;
                    sibling.remove();
                    sibling = next;
                }

            }
            function greyOut() {
                document.querySelectorAll("#disappear-start ~ *").forEach(el => {
                    el.style.opacity = "0.4";          // greyed-out look
                    el.style.pointerEvents = "none";   // makes it feel disabled
                    el.style.filter = "grayscale(100%)";
                });
            }
        </script>');

        if ( isset($_SESSION['lti']) ) {
            $retval = GradeUtil::gradeUpdateJson(array("url" => $_GET['url']));
        }

        try {
            $pieces = parse_url(trim($_GET['url']));
            if ( isset($pieces['scheme']) && isset($pieces['host']) ) {
                $base_url_path = $pieces['scheme'] . '://' . $pieces['host'];
                if ( isset($pieces['port']) && $pieces['port'] != 0 && $pieces['port'] != 80 && $pieces['port'] != 443 ) {
                    $base_url_path .= ':' . $pieces['port'];
                }
                $URL_IN_USE = $_GET['url'];
                return trim($URL_IN_USE);
            }
            echo("<p>Badly formatted URL</p>\n");
        } catch(Exception $e) {
            echo("<p>Badly formatted URL</p>\n");
        }
    }

    echo('<form>');
    echo('<input type="hidden" name="'.session_name().'" value="'.session_id().'">');
    echo('    Please enter the URL of your web site to grade:<br/>
        <input type="text" name="url" value="'.$sample.'" size="100"><br/>');
    if ( isset($_GET['code']) ) {
        echo('<input type="hidden" name="code" value="'.$_GET['code'].'"><br/>');
    }

    echo('<button type="submit" id="test-rerun" class="btn btn-primary" ');
    if ( $SECONDS_BEFORE_RETRY > 2 && ! $USER->instructor ) {
        echo(' disabled ');
    }
    echo(' onclick="$(\'#evaluate_spinner\').show();return true;">');
    if ( $SECONDS_BEFORE_RETRY > 2 ) {
        echo('Please wait <span id="countdown">...</span> (rate limit)');
    } else {
        echo('Evaluate');
    }
    echo("</button>\n");
?>
<img src="<?= $OUTPUT->getSpinnerUrl() ?>" id="evaluate_spinner" style="display:none;">
</p>
</form>
<?php
    if ( $USER->displayname ) {
        echo("By entering a URL in this field and submitting it for
        grading, you are representing that this is your own work.  Do not submit someone else's
        web site for grading.
        ");
    }

    echo("<p>You can run this autograder as many times as you like and the highest
    grade will be recorded.  Make sure to double-check the course Gradebook to verify
    that your grade has been sent.</p>\n");
    return false;
}

function checkPostRedirect($client) {
    global $passed, $failed;
    line_out("Checking to see if the POST redirected to a GET");
    $method = $client->getRequest()->getMethod();
    if ( $method == "GET" ) {
        markTestPassed("POST Redirect Check");
    } else {
        markTestFailed('Expecting POST to Redirected to a GET - found '.$method);
    }
}

function markTestPassed($message=false) {
    global $passed;
    if ( $message ) {
        success_out("Test completed: ".$message);
    } else {
        success_out("Test completed.");
    }
    $passed++;
}

function markTestFailed($message=false) {
    global $failed;
    if ( $message ) {
        error_out("Test failed: ".$message);
    } else {
        error_out("Test failed.");
    }
    $failed++;
}

function webauto_test_passed($grade, $url) {
    global $USER, $OUTPUT;

    success_out("Test completed - congratulations");

    if ( ! isset($_SESSION['lti']) ) {
        line_out('Not setup to return a grade..');
        return false;
    }

    if ( $USER->instructor ) {
        line_out('Instructor grades are not sent..');
        return false;
    }

    $LTI = $_SESSION['lti'];

    $old_grade = isset($LTI['grade']) ? $LTI['grade'] : 0.0;

    if ( $grade < $old_grade ) {
        line_out('New grade is not higher than your previous grade='.$old_grade);
        line_out('Sending your previous high score');
        $grade = $old_grade;
    }

    GradeUtil::gradeUpdateJson(json_encode(array("url" => $url)));
    $debug_log = array();
    $retval = LTIX::gradeSend($grade, false, $debug_log);
    $success = false;
    if ( $retval == true ) {
        $success = "Grade sent to server (".$grade.")";
    } else if ( is_string($retval) ) {
        $failure = "Grade not sent: ".$retval;
    } else {
        echo("<pre>\n");
        var_dump($retval);
        echo("</pre>\n");
        $failure = "Internal error";
    }

    if ( strlen($success) > 0 ) {
        success_out($success);
        error_log($success);
	echo("\n<!--\n");
   	$OUTPUT->dumpDebugArray($debug_log);
	echo("\n-->\n");
    } else if ( strlen($failure) > 0 ) {
        error_out($failure);
        error_log($failure);
   	$OUTPUT->dumpDebugArray($debug_log);
    } else {
        error_log("No status");
    }

    return true;
}

function webauto_get_check() {
    $check = substr(webauto_get_check_full(),0,8);
    return $check;
}

function webauto_get_check_full() {
    global $USER, $CONTEXT;
    $check = md5($USER->id+$CONTEXT->id);
    return $check;
}


function webauto_check_title($crawler) {
    global $USER, $LINK, $CONTEXT;
    $check = webauto_get_check();

    try {
        $title = $crawler->filterXPath('//title')->text();
    } catch(Exception $ex) {
        return "Did not find title tag";
    }

    if ( stripos($title,$check) !== false ) {
        return true;
    }
    if ( $USER->displayname && strlen($USER->displayname) > 0 ) {
        if ( stripos($title, $USER->displayname) !== false ) return true;
    }

    return "Did not find name or '$check' in title tag";
}

function webauto_compute_effective_score($perfect, $passed, $penalty) {
    $score = $passed * (1.0 / $perfect);
    if ( $score < 0 ) $score = 0;
    if ( $score > 1 ) $score = 1;
    if ( $passed > $perfect ) $passed = $perfect;
    if ( $penalty == 0 ) {
        $scorestr = "Score = $score ($passed/$perfect)";
    } else {
        $scorestr = "Raw Score = $score ($passed/$perfect) ";
        $score = $score * (1.0 - $penalty);
        $scorestr .= "Effective Score = $score after ".$penalty*100.0." percent late penalty";
    }
    line_out($scorestr);
    return $score;
}

function webauto_check_post_redirect($client) {
    global $passed;
    line_out("Checking to see if there was a POST redirect to a GET");
    $method = $client->getRequest()->getMethod();
    if ( $method == "get" ) {
        $passed++;
    } else {
        markTestFailed('Expecting POST to Redirect to GET - found '.$method);
    }
}

function webauto_get_radio_button_choice($form,$field_name,$choice)
{
    line_out("Looking for '$field_name' with '$choice' as the label");
    if ($form->has($field_name) ) {
        $field = $form->get($field_name);
        $type = $field->getType();
        if ( $type == "radio" ) {
            success_out("Found '$field_name' radio buttons");
        } else {
            error_out("Could not find radio buttons for form input '$field_name'");
            return false;
        }
    }

    $value = false;
    $allNodes = webauto_recurse_children($form->getFormNode());
    // foreach($formnode->childNodes as $node){
    // Loop through and find all the radios
    $labels = array();
    $radios = array();
    foreach($allNodes as $node){
       if ( $node->nodeName == "#text") continue;
       // echo("\n<pre>Node name \n");echo($node->nodeName);echo("\n</pre>\n");
       if ( $node->nodeName == "label") {
            $for = $node->getAttribute("for");
            $text = $node->textContent;
            // echo("\n<pre>\n for: $for text: $text\n</pre>\n");
            if ( is_string($for) && is_string($text) && strlen($for) > 0 && strlen($text) > 0 ) {
              $labels[] = array($text, $for);
            }
          continue;
       }

       if ( $node->nodeName == 'input' && $node->getAttribute("type") == "radio" ) {
            $id = $node->getAttribute("id");
            $value = $node->getAttribute("value");
            // echo("\n<pre>\n id: $id value: $value\n</pre>\n");
            if ( is_string($id) && is_string($value) && strlen($id) > 0 && strlen($value) > 0 ) {
              $radios[] = array($id, $value);
            }
          continue;
       }
    }
    // echo("\n<pre> ---- \n");var_dump($labels);var_dump($radios);echo("\n</pre>\n");

    $failure = false;
    if ( count($labels) < 1 ) {
      error_out("Could not find any label tags with 'for' attributes");
      $failure = true;
    }

    if ( count($radios) < 1 ) {
      error_out("Could not find any radio inputs with 'id' attributes");
      $failure = true;
    }

    if ( $failure ) {
        echo("\n<!--- \n");var_dump($labels);var_dump($radios);echo("\n-->\n");
        return false;
    }

    foreach($labels as $label) {
        if ( $choice == $label[0] ) {
            $id = $label[1];
            foreach($radios as $radio) {
                if ( $id == $radio[0] ) {
                    $value = $radio[1];
                    line_out("Found choice=$choice value=$value");
                    return $value;
                }
            }
         }
     }

    error_out("Could not form input '$field_name' with label of '$choice'");
    return false;
}


function webauto_recurse_children($startnode) {
    $nodes = array();
    foreach($startnode->childNodes as $node){
        $nodes[] = $node;
        if ( is_object($node) && $node->childNodes->length > 0 ) {
            $more = webauto_recurse_children($node);
            $nodes = array_merge($nodes, $more);
        }
    }
    return $nodes;
}

function webauto_get_form_with_button($crawler,$text, $text2=false)
{
    $msg = 'Did not find form with a "'.$text.'" button';
    if ( ! is_object($crawler) ) {
        error_out($msg);
        throw new Exception($msg);
    }
    $html = $crawler->html();;
    if ( strpos($html, $text) === false && strpos($html, $text2) === false) {
        error_out($msg);
        throw new Exception($msg);
    }

    if ( is_string($text2) ) {
        try {
            $form = $crawler->selectButton($text2)->form();
            markTestPassed('Found form with "'.$text2.'" button');
            return $form;
        } catch(Exception $ex) {
            // pass and drop through
        }
    }

    try {
        $form = $crawler->selectButton($text)->form();
        markTestPassed('Found form with "'.$text.'" button');
        return $form;
    } catch(Exception $ex) {
        markTestFailed($msg);
        throw new Exception($msg);
    }
}

function webauto_get_href($crawler,$text, $message=false)
{
    if ( $crawler == false ) return false;
    $html = $crawler->html();
    $msg = 'Did not find anchor tag with "'.$text.'"';
    if ( is_string($message) ) $msg .= ' ' . $message;
    if ( strpos($html, $text) === false) {
        if ( stripos($html, $text) !== false ) $msg .= ' (check your case)';
        error_out($msg);
        throw new Exception($msg);
    }

    try {
        $link = $crawler->selectLink($text)->link();
        markTestPassed('Found an anchor tag with "'.$text.'" button');
        return $link;
    } catch(Exception $ex) {
        markTestFailed($msg);
        throw new Exception($msg);
    }
}

function webauto_get_url_from_href($crawler,$text,$message=false)
{
    $href = webauto_get_href($crawler,$text, $message);
    if ( ! $href ) return false;
    $url = $href->getURI();
    return $url;
}

function webauto_extract_url($crawler,$text, $message=false)
{
    try {
        $url = webauto_get_url_from_href($crawler,$text, $message);
    } catch(Exception $ex) {
        return false;
    }
    return $url;
}

// http://api.symfony.com/4.0/Symfony/Component/DomCrawler/Form.html
function webauto_change_form($form, $name, $value, $message=false)
{
    try {
        $x = $form->get($name);
    } catch(Exception $ex) {
        $msg = 'Did not find form field named "'.$name.'"';
        if ( is_string($message) ) $msg .= ' ' . $detail;
        error_out($msg);
        throw new Exception($msg);
    }
    line_out("Changing form field '$name' to be $value");
    $x->setValue($value);
}

function webauto_submit_form($client, $form) {
    /* This can blow up - bug in symfony that they won't fix */
    try {
        $crawler = $client->submit($form);
    } catch(Exception $ex) {
        error_out("Submitting the form caused an error - often this is an '<input type=file' field with the wrong name");
        return $crawler;
    }
	$response = $client->getInternalResponse();
	$status = $response->getStatusCode();
	if ( $status != 200 ) {
		error_out("Submitting the form caused an error http code=".$status);
	}
	return $crawler;
}

function webauto_search_for_many($html, $needles)
{
    $retval = true;
    foreach($needles as $needle ) {
        $check = webauto_search_for($html,$needle.'');
        $retval = $retval && $check;
    }
    return $retval;
}

function webauto_search_for($html, $needle, $ignorecase=true)
{
    if ( strpos($html,$needle) > 0 ) {
        markTestPassed("Found '$needle'");
        return true;
    } else if ( $ignorecase && stripos($html,$needle) > 0 ) {
        error_out("Warning: Found '$needle' but with incorrect case");
        markTestPassed("Found '$needle'");
        return true;
    } else {
        markTestFailed("Could not find '$needle'");
        return false;
    }
}

function webauto_search_for_not($html, $needle, $message=false)
{
    if ( stripos($html,$needle) === false ) {
        markTestPassed("Did not find '$needle' (test passed - it is not supposed to be in the output)");
        return true;
    } else {
        markTestFailed("Should not have found '$needle' ".$message);
        return false;
    }
}

function webauto_search_for_menu($html)
{
    global $MENU_WARNING_ONCE ;

    if ( ! isset($MENU_WARNING_ONCE) ) $MENU_WARNING_ONCE = 0;

    // TODO: Give this teeth
    if ( $MENU_WARNING_ONCE < 3 &&  stripos($html,"ChucksList") > 0 ) {
        error_out("Your application should not be named 'ChucksList'");
        $MENU_WARNING_ONCE++;
    }

    $needle = '<nav';
    if ( strpos($html,$needle) > 0 ) {
        markTestPassed("Found menu bar at the top of the page");
        return true;
    } else {
        markTestFailed("Could not find menu bar at the top of the page");
        return false;
    }
}

/* Deprecated */
function webauto_get_url($client, $url, $message=false) {
    return webauto_retrieve_url($client, $url, $message);
}

/* Returns a crawler */
function webauto_retrieve_url($client, $url, $message=false) {
    global $base_url_path;
    global $webauto_http_status;
    line_out(" ");
    if ( $message ) header_out($message);
    echo("<b>Loading URL:</b> ".htmlentities($url));
    $the_url = str_replace('"',"&quot;", $url);
    if ( strpos($the_url, '/') === 0 ) $the_url = $base_url_path . $the_url;
    echo(' (<a href="'.$the_url.'" target="_blank">Open URL</a>)');
    echo("<br/>\n");
    flush();
    try {
        $crawler = $client->request('GET', $url);
        $response = $client->getResponse();
        $webauto_http_status = $response->getStatusCode();
    } catch(\Exception $e) {
        error_out($e->getMessage());
        return false;
    }
    return $crawler;
}

function webauto_dont_want($html, $needle)
{
    if ( stripos($html,$needle) > 0 ) {
        markTestFailed("Found something that should not be there: '$needle'");
        return true;
    } else {
        markTestPassed("Did not find '$needle'");
        return false;
    }
}

function webauto_testrun($url) {
    return strpos($url,'dj4e.com') !== false || strpos($url,'index.htm') !== false ||
        strpos($url,'mdntutorial.pythonanywhere.com') !== false ||
        strpos($url,'drchuck.pythonanywhere.com') !== false ||
        strpos($url,'dj4e.pythonanywhere.com') !== false ||
        strpos($url,'http://localhost') !== false;
}

// <option value="46">LU_42</option></select>
function quoteBack($html, $pos) {
    $end = -1;
    for($i=$pos; $i > 0; $i-- ){
        if ( $end == -1 && $html[$i] == '"' ) {
            $end = $i;
            continue;
        }
        if ( $end != -1 && $html[$i] == '"' ) {
            return substr($html, $i+1, $end-$i-1);
        }
    }
    return "";
}

function trimSlash($url) {
    if ( strlen($url) < 2 ) return($url);
    $ch = substr($url, strlen($url)-1, 1);
    if ( $ch != '/' ) return $url;
    return substr($url, 0, strlen($url)-1);
}

function get_favicon($client, $base_url_path) {
    $favicon_url = $base_url_path . '/favicon.ico';

    line_out("Loading the favicon from ".$favicon_url." ...");
    $crawler = $client->request('GET', $favicon_url);
    $status = 404;
    if ( $crawler !== false ) {
        $response = $client->getResponse();
        $status = $response->getStatusCode();
    }

    if ( $status != 200 ||$crawler === false ) {
        $favicon_url = $base_url_path . '/static/favicon.ico';
        line_out("Loading the favicon from ".$favicon_url." ...");
        $crawler = $client->request('GET', $favicon_url);
    }

    if ( $crawler !== false ) {
        $response = $client->getResponse();
        $status = $response->getStatusCode();
    }

    if ( $status !== 200 ) {
        error_out("Unable to load favicon status=".$status);
        return false;
    }
    $content = $response->getContent();
    return $content;
}

// Two tons of meta..
function check_code_and_version($crawler) {
    global $RESULT, $URL_IN_USE;

    if ( isset($URL_IN_USE) && is_string($URL_IN_USE) && strpos($URL_IN_USE, 'dj4e.com') > 0 ) return;

    $dj4e_code = webauto_get_meta($crawler, 'dj4e-code');
    $dj4e_version = webauto_get_meta($crawler, 'dj4e-version');

    if ( $dj4e_code == "99999999" ) $dj4e_code = false;
    if ( U::strlen($dj4e_code) < 1 && U::strlen($dj4e_version) ) return;

    try {
        $json = json_decode($RESULT->getJSON());
    } catch(Exception $e) {
        $json = new \stdClass();
    }

    if ( strlen($dj4e_code) > 0 ) {
        $dj4e_codes = array();
        if ( isset($json->dj4e_codes) && is_array($json->dj4e_codes) ) $dj4e_codes = $json->dj4e_codes;
        if ( ! in_array($dj4e_code, $dj4e_codes) ) $dj4e_codes[] = $dj4e_code;
        $json->dj4e_codes = $dj4e_codes;
    }

    if ( strlen($dj4e_version) > 1 ) {
        $dj4e_versions = array();
        if ( isset($json->dj4e_versions) && is_array($json->dj4e_versions) ) $dj4e_versions = $json->dj4e_versions;
        if ( ! in_array($dj4e_version, $dj4e_versions) ) $dj4e_versions[] = $dj4e_version;
        $json->dj4e_versions = $dj4e_versions;
    }
    $RESULT->setJSON(json_encode($json));
}

function print_user_and_password($user1account, $user1pw, $user2account=null, $user2pw=null) {
?>
<pre>
<span id="account1"><?= htmlentities($user1account) ?></span> (<a href="#" onclick="copyToClipboard(this, $('#account1').text());return false;">copy</a>) / <span id="password1"><?= htmlentities($user1pw) ?></span> (<a href="#" onclick="copyToClipboard(this, $('#password1').text());return false;">copy</a>)
<?php if ( is_string($user2account) ) { ?>
<span id="account2"><?= htmlentities($user2account) ?></span> (<a href="#" onclick="copyToClipboard(this, $('#account2').text());return false;">copy</a>) / <span id="password2"><?= htmlentities($user2pw) ?></span> (<a href="#" onclick="copyToClipboard(this, $('#password2').text());return false;">copy</a>)
<?php } ?>
</pre>
<?php
}

function warn_about_ngrok($url) {
    if ( ! is_string($url) ) return;
    if ( str_contains($url, 'pythonanywhere.com') ) return;
    if ( str_contains($url, 'dj4e.com') ) return;
    if ( str_contains($url, 'localhost') ) return;
?>
<p>This works with assignments on <a href="https://www.pythonanywhere.com/" target="_blank">PythonAnywhere</a>.  You <b>may</b> be able to pass the
autograder with a Django application hosted elsewhere or accessible through a reverse proxy like <b>ngrok</b> but you might also encounter problems.
</p>
<?php
}

function webauto_dump_html($html) {
    echo("<pre>\n");
    echo(htmlentities($html));
    echo("</pre>\n");
}

function warn_about_testrun($url) {
   if ( webauto_testrun($url) ) {
       line_out("You are running the autograder on a test server - ".$url);
       line_out("This submission will not generate a grade");
   }
}

function webauto_append_suffix($url, $suffix) {
    $newurl = $url . '/' . $suffix;
    $newurl = substr($newurl, 0, 8).str_replace('//', '/', substr($newurl, 8));
    $newurl = substr($newurl, 0, 8).str_replace('//', '/', substr($newurl, 8));
    $newurl = substr($newurl, 0, 8).str_replace('//', '/', substr($newurl, 8));
    return $newurl;
}

function header_out($message) {
    echo('<hr/>');
    line_bold($message);
}

function line_bold($message) {
    echo('<b>'.htmlentities($message).'</b><br/>');
}

function webauto_expect_error(int $code) {
    global $WEBAUTO_EXPECT_ERROR;
    if ( !isset($WEBAUTO_EXPECT_ERROR) || ! is_array($WEBAUTO_EXPECT_ERROR) || $code == -1 ) {
        $WEBAUTO_EXPECT_ERROR = array();
    }
    $WEBAUTO_EXPECT_ERROR[] = $code;
}

    
