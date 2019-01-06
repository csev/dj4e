<?php
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

require "top.php";
require "nav.php";

?>
<div id="container">
<div style="margin-left: 10px; float:right">
<img src="images/Chuck_16x9_gears_DJ4E.jpg" onclick='window.location.href="https://www.youtube.com/watch?v=lPpIubhqWR4&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z&index=2";' style="padding: 5px; width:300px;">
</div>
<h1>DJango for Everybody</h1>
<p>
This web site is building a set of free / OER materials to help students
learn the DJango web development framework.  The site is 
<b>under construction</b> but you are welcome to make use of it as
it is being built.
</p>
<p>
The first part of this site is a set of autograders for 
Mozilla Developer Network (MDN) at
<a href="https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django" target="_blank">
https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django
</a>
</p>
<p>
We use the free 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> hosting environment
to deploy and test our DJango projects and applications.
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
The material produced on this site is by Anthony Whyte and Charles Severance
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
