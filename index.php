<?php
use \Tsugi\Util\U;
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;
use \Tsugi\UI\Pages;

require "top.php";
require "nav.php";

$warn_domain = "@umich.edu";

?>

<!-- Fix for smartphone screen responsiveness -->
<style>
code {
  word-break: break-word;
}
</style>

<div id="container">
<div style="margin-left: 10px; float:right">
<iframe width="400" height="225" src="https://www.youtube.com/embed/oxJQB4f2MMs?rel=0" frameborder="0" allowfullscreen title="Django for Everybody course introduction video"></iframe>
</div>
<h1>Django for Everybody</h1>
<?php
$front_page_text = null;
if ( isset($_SESSION['id']) && isset($_SESSION['context_id']) ) $front_page_text = Pages::getFrontPageText($_SESSION['context_id']);
if ( $front_page_text ) {
    echo("<p>\n");
    echo $front_page_text;
    echo("</p>\n");
} 
?>
<p>
This web site is building a set of free materials, lectures, and assignments to help students
learn the Django web development framework.  
You can take this course and receive a certificate at:
<ul>
<li><a href="https://www.coursera.org/specializations/django" target="_blank" rel="noopener noreferrer">Coursera: Django for Everybody Specialization</a></li>
<li><a href="https://www.edx.org/xseries/michiganx-django-for-everybody" target="_blank" rel="noopener noreferrer">edX: Django for Everybody XSeries Program</a></li>
<li><a href="https://www.youtube.com/watch?v=o0XbHvKxw7Y" target="_blank" rel="noopener noreferrer">FreeCodeCamp: Django for Everybody</a></li>
<li><a href="https://online.umich.edu/series/django/" target="_blank" rel="noopener noreferrer">Free certificates for University of Michigan students and staff</a></li>
</ul>
</p>
<p>
We use the free 
<a href="https://www.pythonanywhere.com" target="_blank" rel="noopener noreferrer">PythonAnywhere</a> hosting environment
to deploy and test our Django projects and applications.  You can keep using this hosting environment
to develop and deploy your Django applications after you complete the course.
</p>
<?php
$month = date('n');
$email = U::get($_SESSION, 'email');
if ( ($month == 1 || $month == 9 ) && U::endsWith($email, $warn_domain) ) {
?>
<p style="border: 2px red solid; margin: 5px; padding: 5px;">
You are logged in using an <?= $warn_domain ?> email address.  If you are taking
this course for credit (SI664 or SI357), you need to do your assignments through the campus LMS
(i.e. Canvas) to get credit for the assignments.
</p>
<?php
}
?>
<h2>Technology</h2>
<p>
This site uses <a href="http://www.tsugi.org" target="_blank" rel="noopener noreferrer">Tsugi</a> 
framework to embed a learning 
management system into this site and handle the autograders.  
If you are interested in collaborating
to build these kinds of sites for yourself, please see the 
<a href="http://www.tsugi.org" target="_blank" rel="noopener noreferrer">tsugi.org</a> website.
</p>
<h3>Copyright</h3>
<p>
The material produced specifically for this site is by Charles Severance and others
and is Copyright Creative Commons Attribution 3.0 
unless otherwise indicated.  
</p>
<!--
<?php
echo("IP Address: ".Net::getIP()."\n");
echo(Output::safe_var_dump($_SESSION));
var_dump($USER);
?>
-->
</div>
<?php 
require "footer.php";
