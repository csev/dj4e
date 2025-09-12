<!DOCTYPE html>
<?php
require_once("../assn_util.php");
$json = loadPeer("peer.json");
?>
<html>
<head>
<title>Assignment: <?= $json->title ?></title>
</head>
<body style="margin-left:5%; margin-bottom: 60px; margin-right: 5%; font-family: sans-serif;">
<h1>Assignment: <?= $json->title ?></h1>
<p>
<?= $json->description ?>
<p>
You should already have your system set up on PythonAnywhere and have completed the HTML
assignment before starting this assignment.  The HTML assignment sets up your application to
serve static files and we will need that set up to do this assignment.
</p>
<p>
In this assignment, you will transform from this:
<center>
<a href="01-No-style.png" target="_blank">
<img src="01-No-style.png" width="80%" border="2px"></a>
</center>
To this:
<center>
<a href="02-style.png" target="_blank">
<img src="02-style.png" width="80%" border="2px"></a>
</center>
Using only CSS.
</p>
<h1>Resources</h1>
<p>There are several sources of information so you can do the assignment:
<ul>
<li>Lectures and materials on <i>Cascading Style Sheets</i> from
<a href="https://www.dj4e.com/lessons/css" target="_blank">www.dj4e.com</a></li>
</ul>
</p>
<h1>Pre-Requisites</h1>
<p>
<ul>
<li><p>Please figure out how to use "inspect element" in your prowser.
We will be using what in effect is a built-in browser debugger for many
tasks in this course.
</p></li>
</ul>
</p>
<h1>Tasks</h1>
<p>
Here are the tasks for this assignment.  These tasks can be done by editing HTML and
CSS files in your `site` folder on PythonAnywhere.
<ul>
<li><p>
Make a folder <b>django_projects/mysite/site/css</b>.
</p></li>
<li><p>Take this <a href="index.txt" target="_blank">this file</a> and
copy/paste the contents into
<b>django_projects/mysite/site/css/index.htm</b>.  You will not change this file.
<li><p>Take <a href="blocks.txt" target="_blank">this file</a>
and copy/paste the contents into
<b>django_projects/mysite/site/css/blocks.css</b> in the same folder as the above file.
</p>
<li><p>Edit the <b>blocks.css</b> and add the CSS rules so
the HTML file looks like the above image when you view
<pre>
https://your-account.pythonanywhere.com/site/css/index.htm
</pre>
file in your browser.
(<a href="https://dj4e.pythonanywhere.com/site/css/index.htm" target="_blank">Example that *looks* correct but is not a solution at all</a>)
</p></li>
<li><p>The four boxes have five pixel borders with different colors and five pixels
of margin and padding.  It is probably simplest to use
<a href="https://www.w3schools.com/css/css_positioning.asp" target="_blank">fixed positioning</a>
to get the
boxes to stay near to the corners of the screen even when you resize.  Make the boxes
width be <b>25%</b> so the width changes as you resize your browser.</p></li>
<li><p>Center the link at the top of the page.  Use your developer console / inspect element
feature of your browser to visit <a href="https://www.dj4e.com/" target="_blank">
https://www.dj4e.com/</a> and figure out the background color, font, and text color
used in the top navigation bar and replicate for the link to DJ4E
in your <b>index.htm</b>.
</p></li>
You might find that using the
<a href="https://www.w3schools.com/css/css_border_shorthand.asp" target="_blank">border shortcut</a>
in your CSS instead of the separate "border" values makes the CSS validtor "happier".
<li><p>Your CSS must pass the validator at:
<pre>
<a href="https://jigsaw.w3.org/css-validator" target="_blank">https://jigsaw.w3.org/css-validator</a>
</pre>
</p></li>
</ul>
<p>
Note - that as you change files like `blocks.css` you may need to
 make sure that when you hit 'refresh' that a new copy of the file is loaded.
On some browsers, you can press 'Shift-Refresh' to force a reload
of the cache so you get a fresh copy of the file.
</p>
<h1>What To Hand In</h1>
<p>
For this assignment you will hand in:
<ol>
<?php
foreach($json->parts as $part ) {
    echo("<li>$part->title</li>\n");
}
?>
</ol>
</p>


<h1>Sample Screen Shots</h1>
<p>Passing the CSS validator:
<p>
<center>
<a href="06-css-validator.png" target="_blank">
<img src="06-css-validator.png" width="80%" border="2px"></a>
</center>
</p>
<p>Using Inspect Element:
<p>
<center>
<a href="04-inspect-element.png" target="_blank">
<img src="04-inspect-element.png" width="80%" border="2px"></a>
</center>
</p>


<p style="padding-top:30px;">
Provided by: <a href="http://www.dj4e.com/" target="_blank">
www.dj4e.com</a> <br/>
</p>
<center>
Copyright Creative Commons Attribution 3.0 - Charles R. Severance
</center>
</body>
</html>
