<?php
use \Tsugi\Util\Net;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

require "top.php";
require "nav.php";

?>
<div id="container">
<h3>
Using the Autograder with ngrok
</h3>
<p>
The assignments for this course are expected to be run on 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> and as such
they have a globally accessible URL like
<a href="https://mdntutorial.pythonanywhere.com" target="_blank">https://mdntutorial.pythonanywhere.com</a>
and can be directly submitted to the autograder for testing.
</p>
<p>
For whatever reason, you might prefer to install and run Django locally on your computer.
This is a fine solution for the course, but to submit the applications on your local computer
to the autograder, you need to use a tool like 
<a href="https://www.ngrok.org" target="_blank">ngrok</a> or
<a href="https://localtunnel.github.io/" target="_blank">localtunnel</a>.
</p>
<p>
This document serves as a quick guide for using ngrok to submit your applications to the
autograder.
</p>
<h3>
Installing ngrok for Autograding
</h3>
<p>
Installing ngrok is very simple, you can download a ZIP file from 
<a href="https://ngrok.com/" target="_blank">https://ngrok.com/</a> and 
unzip that file anywhere on your computer.  You might want to read the documentation
on the web site to familiarize yourself with ngrok.  They have nice diagrams that 
explain how ngrok works and why ngrok is needed to allow access to your local web server.
</p>
<h3>Running ngrok on Apple</h3>
<p>
Download the ngrok.zip file to your <b>Downloads</b> folder and then extract it by double clicking on
the downloaded file and it will unzip and produce a single file called <b>ngrok</b>.  You can put this 
file anywhere on your computer but for now we will just execute it from the <b>Downloads</b> folder.
Make sure your Django application is up and running and then 
open up a Terminal Window as follows:
<pre>
$ cd Downloads/
$ ls
ngrok               ngrok_2.0.19_darwin_amd64.zip
$ ./ngrok http 8000

Tunnel Status       online                                            
Version             2.0.19/2.0.19                                     
Web Interface       http://127.0.0.1:4040                             
Forwarding          http://c5343c6e.ngrok.io -&gt; localhost:8000        
Forwarding          https://c5343c6e.ngrok.io -&gt; localhost:8000       
                                                                                
Connections         ttl     opn     rt1     rt5     p50     p90       
                    0       0       0.00    0.00    0.00    0.00 
</pre>
Replace "8000" with whatever port your web server is running on.  Then nagivate in your browser
to the address that ngrok has chosen for you.  Do not include the port number on the ngrok url.
<pre>
http://c5343c6e.ngrok.io
</pre>
At that point you should see the same thing as you would see if you went to 
<pre>
http://localhost:8000/
</pre>
And you can go to paths other than the root like:
<pre>
http://c5343c6e.ngrok.io/catalog
</pre>
Your local web server will be visible to the Internet at the ngrok-chosen address
until you end the <b>ngrok</b> application.  To terminate the <b>ngrok</b> on the 
Apple, simply press "CTRL-C" to aport the program.  At that point, your local web
server can no longer be accessed through ngrok.
</p>
<p>
Each time you run <b>ngrok</b> you will get a new address unless you sign up and pay for an
address that does not change each time you run it.
</p>
<h3>Running ngrok on Windows</h3>
<p>
Download the ngrok.zip file to your <b>Downloads</b> folder and then extract it by clicking on
the downloaded file and selecting "Extract All".  It will make a folder like "ngrok_2.0.19_windows_386"
and in that folder, you will find a single file named <b>ngrok.exe</b>.  You 
You can put this 
file anywhere on your computer but for now we will just execute it from the <b>Downloads</b> folder.
Make sure your Django application is up and running and then 
open up a Command Line window as follows:
<pre>
C:\...&gt; cd Downloads\ngrok_2.0.19_windows_386
C:\...&gt; ngrok http 8000

Tunnel Status       online                                            
Version             2.0.19/2.0.19                                     
Web Interface       http://127.0.0.1:4040                             
Forwarding          http://c5343c6e.ngrok.io -&gt; localhost:8000        
Forwarding          https://c5343c6e.ngrok.io -&gt; localhost:8000       
</pre>
Replace "8000" with whatever port your web server is running on.  Then nagivate in your browser
to the address that ngrok has chosen for you.  Do not include the port number on the ngrok address.
<pre>
http://c5343c6e.ngrok.io
</pre>
At that point you should see the same thing as you would see if you went to 
<pre>
http://localhost:8000/
</pre>
And you can go to paths other than the root like:
<pre>
http://c5343c6e.ngrok.io/catalog
</pre>
Your local web server will be visible to the Internet at the ngrok-chosen address
until you end the <b>ngrok</b> application.  To terminate the <b>ngrok</b> on the 
Apple, simply press "CTRL-Z" to aport the program.  At that point, your local web
server can no longer be accessed through ngrok.
</p>
<p>
Each time you run <b>ngrok</b> you will get a new address unless you sign up and pay for an
address that does not change each time you run it.
</p>
