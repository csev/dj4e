<?php

if ( ! defined('COOKIE_SESSION') ) define('COOKIE_SESSION', true);

require_once "../tsugi/config.php";
require_once "Parsedown.php";

require_once "../top.php";
require_once "../nav.php";

if ( ! function_exists('endsWith') ) {
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
}

$url = $_SERVER['REQUEST_URI'];

$parts = parse_url($url);
if ( isset($parts['path']) ) $url = $parts['path'];
$pieces = explode('/',$url);

$file = false;
$contents = false;
if ( $pieces >= 2 ) {
   $file = $pieces[count($pieces)-1];
   if ( ! endsWith($file, '.md') ) $file = false;
   if ( ! $file || ! file_exists($file) ) $file = false;
}

if ( $file !== false ) {
    $contents = file_get_contents($file);
    $HTML_FILE = $file;
}

function x_sel($file) {
    global $HTML_FILE;
    $retval = 'value="'.$file.'"';
    if ( strpos($HTML_FILE, $file) === 0 ) {
        $retval .= " selected";
    }
    return $retval;
}


$OUTPUT->header();
?>
<style>
center {
    padding-bottom: 10px;
}
@media print {
    #chapters {
        display: none;
    }
}
a[target="_blank"]:after {
  content: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAQElEQVR42qXKwQkAIAxDUUdxtO6/RBQkQZvSi8I/pL4BoGw/XPkh4XigPmsUgh0626AjRsgxHTkUThsG2T/sIlzdTsp52kSS1wAAAABJRU5ErkJggg==);
  margin: 0 3px 0 5px;
}
</style>
<?php
$OUTPUT->bodyStart();
// $OUTPUT->topNav();

if ( $contents != false ) {
?>
<script>
function onSelect() {
    console.log($('#chapters').val());
    window.location = $('#chapters').val();
}
</script>
<div style="float:right">
<select id="chapters" onchange="onSelect();">
  <option <?= x_sel("paw_install.md") ?>>Installing DJango</option>
  <option <?= x_sel("paw_github.md") ?>>Using GitHub</option>
  <option <?= x_sel("paw_skeleton.md") ?>>Skeleton web site</option>
  <option <?= x_sel("paw_models.md") ?>>DJAngo Models</option>
  <option <?= x_sel("paw_admin.md") ?>>DJAngo Admin</option>
  <option <?= x_sel("paw_home.md") ?>>Home Page</option>
  <option <?= x_sel("paw_details.md") ?>>Detail Pages</option>
  <option <?= x_sel("paw_sessions.md") ?>>DJango Sessions</option>
</select>
</div>
<?php
    $Parsedown = new Parsedown();
    echo $Parsedown->text($contents);
} else {
?>
<p>
This is a set of supplementary documentation for use with this
web site.
</p>
<ul>
<li><a href="paw_install.md">Installing DJango</a></li>
<li><a href="paw_github.md">Using GitHub</a></li>
<li><a href="paw_skeleton.md">Skeleton web site</a></li>
<li><a href="paw_models.md">DJango Models</a></li>
<li><a href="paw_admin.md">DJango Admin</a></li>
<li><a href="paw_home.md">Home Page</a></li>
<li><a href="paw_details.md">Detail Pages</a></li>
<li><a href="paw_sessions.md">DJango Sessions</a></li>
</ul>
<p>
If you find a mistake in these pages, feel free to send me a fix using
<a href="https://github.com/csev/dj4e/tree/master/assn" target="_blank">Github</a>.
</p>
<?php
}
$OUTPUT->footerStart();
?>
<script>
// https://stackoverflow.com/questions/7901679/jquery-add-target-blank-for-outgoing-link
$(window).load(function() {
    $('a[href^="http"]').attr('target', function() {
      if(this.host == location.host) return '_self'
      else return '_blank'
    });
});
</script>
<?php
$OUTPUT->footerEnd();

