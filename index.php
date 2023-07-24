<?php
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

require "top.php";
require "nav.php";

?>

<!-- Fix for smartphone screen responsiveness -->
<style>
code {
  word-break: break-word;
}
</style>

<div id="container">
<div style="margin-left: 10px; float:right">
<img src="images/Chuck_16x9_SakaiCar_DJ4E_small.png" onclick='window.location.href="https://www.sakaiger.com/sakaicar";' target="_blank" style="padding: 5px; width:360px;">
</div>
<h1>Django for Everybody</h1>
<?php
if (version_compare(PHP_VERSION, '8.0.0') >= 0) echo('<p style="color:red;"><b>The autograders associated with this site do not work beyond PHP 7.</b></p>'."\n");
?>
<p>
This web site is building a set of free materials, lectures, and assignments to help students
learn the Django web development framework.  
You can take this course and receive a certificate at:
<ul>
<li><a href="https://www.coursera.org/specializations/django" target="_blank">Coursera: Django for Everybody Specialization</a></li>
<li><a href="https://www.edx.org/xseries/michiganx-django-for-everybody" target="_blank">edX: Django for Everybody XSeries Program</a></li>
<!--
<li><a href="https://www.futurelearn.com/programs/django" target="_blank">FutureLearn: Django for Everybody</a></li>
-->
<li><a href="https://www.youtube.com/watch?v=o0XbHvKxw7Y" target="_blank">FreeCodeCamp: Django for Everybody</a>
<li><a href="https://online.umich.edu/series/django/" target="_blank">Free certificates for University of Michigan students and staff</a></li>
</ul>
</p>
<p>
We use the free 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> hosting environment
to deploy and test our Django projects and applications.  You can keep using this hosting environent
to develop and deploy your Django applications after you complete the course.
</p>
<h2>Technology</h2>
<p>
This site uses <a href="http://www.tsugi.org" target="_blank">Tsugi</a> 
framework to embed a learning 
management system into this site and handle the autograders.  
If you are interested in collaborating
to build these kinds of sites for yourself, please see the 
<a href="http://www.tsugi.org" target="_blank">tsugi.org</a> website.
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
