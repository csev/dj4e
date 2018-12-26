<?php
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

require "top.php";
require "nav.php";

?>
<div id="container">
<!--
<div style="margin-left: 10px; float:right">
<iframe width="400" height="225" src="https://www.youtube.com/embed/tuXySrvw8TE?rel=0" frameborder="0" allowfullscreen></iframe>
</div>
-->
<h1>DJango for Everybody</h1>
<p>
This web site is building a set of free / OER materials to help students
learn the DJango web development framework.  The site is 
<b>under construction</b> but you are welcome to make use of it as
it is being built.
</p>
<p>
The first part of this site is a set of autograders for 
DJango tutorials 1-4 available at:

<a href="https://docs.djangoproject.com/en/2.0/intro/" target="_blank">
https://docs.djangoproject.com/en/2.0/intro/
</a>
<h2>Notes</h2>
<ul>
<li>
<a href="assn">Assignments</a> (under construction)
</li>
<li>
<a href="https://mdntutorial.pythonanywhere.com" target="_blank">Sample Implementations for the MDN tutorial</a>
</li>

<li>
<a href="https://dj4e.pythonanywhere.com" target="_blank">Sample Implementations for the DJango Tutorial</a>
(add a path to this URL to get to the actual implementations)
</li>
<li>
<a href="https://help.pythonanywhere.com/pages/DeployExistingDjangoProject/" target="_blank">Installing DJango on PythonAnywhere</a>
</li>
<li><a href="code" target="_blank">Sample Code</a> (under construction)</li>
</ul>
<p>
This site uses <a href="http://www.tsugi.org" target="_blank">Tsugi</a> 
framework to embed a learning 
management system into this site and handle the autograders.  
If you are interested in collaborating
to build these kinds of sites for yourself, please see the 
<a href="http://www.tsugi.org" target="_blank">tsugi.org</a> website.
<h3>Copyright</h3>
<p>
All this material produced by Anthony Whyte and Charles Severance
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
require "foot.php";
