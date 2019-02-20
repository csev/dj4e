<?php

if ( ! isset($_GET['assn'] ) ) die('No assignment');

$assn = $_GET['assn'];

if ( strpos($assn, '02') !== 0 ) die('Bad format');
if ( strpos($assn, '.php') === false ) die('Bad format');

$SPEC_ONLY = true;
require_once($assn);

if ( !isset($title_singular) ) {
    die('Fields not set');
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?= $assignment_type ?>: <?= $title_singular ?> Database CRUD</title>
<style>
li { padding: 5px; }
pre {padding-left: 2em;}
</style>
</head>
<body style="margin-left:5%; margin-bottom: 60px; margin-right: 5%; font-family: sans-serif;">
<h1><?= $assignment_type ?>: <?= $title_singular ?> Database CRUD</h1>
<p>
In this <?= $assignment_type_lower ?> you will build a web based application to
track data about <?= strtolower($title_plural) ?>.
We will only allow logged in users
to track <?= strtolower($title_plural) ?>.
</p>
<?php if ( $assignment_type == 'Exam' || $assignment_type == "Sample Exam" ) { ?>
<h1><?= $assignment_type ?> Rules
</h1>
<p>
<?php
    if ( $assignment_type == "Sample Exam" ) {
        echo('<b>(If this were a real exam)</b> ');
    }
?>
In order for us to consider your exam for grading, you must read the
statement below and if you agree with the statement sign and date below
and turn the entire exam packet in at the end of the exam.
If you do not return this signed exam sheet before you leave the room,
your exam will not be graded and you will receive a zero on this exam.
</p>
<div style="border:2px solid black; padding: 5px; margin: 5px; width:100%"><b>
This examination represents my own work and I have neither
received nor given anyone any aid on this examination.
<pre>

SIGNATURE: ________________________________________________

PRINT NAME: __________________________________________________

Date:  _______________
</pre>
</b>
</div>
<p>
<?php
    if ( $assignment_type == "Sample Exam" ) {
        echo('If this were a real exam, it would be ');
    } else {
        echo('This exam is');
    }
?>
open-book, open notes, open network, and you can use
any of your prior work for the class to complete the exam.
You cannot listen to audio or watch any videos during the exam.
You cannot get any help from any other person. You also cannot give
any help to any other person. We will grade partial
solutions so you should hand in your work at the end of the
exam even is it is not 100% complete. Please do not discuss the
nature of the exam with anyone except the teaching staff until
we tell you that all students have completed the exam.
</p>
<?php } else { ?>
<h1>Resources</h1>
<p>There are several resources you might find useful:
<ul>
<li>Recorded lectures, sample code and chapters from
<a href="http://www.dj4e.com" target="_blank">www.dj4e.com</a>
</ul>
<li>
The sample CRUD code that we covered in class and used in previous assignments.
<pre>
<a href="https://github.com/csev/dj4e-samples/tree/master/dj4ecrud" target="_blank">https://github.com/csev/dj4e-samples/tree/master/dj4ecrud</a>
</pre>
</li>
</ul>
<?php } ?>
<h2 clear="all">General Specifications</h2>
<p>
Here are some general specifications for this <?= $assignment_type_lower ?>:
<ul>
<li>
Use the Django-provided features for login and log out just as in the provided sample code.
<li>
The auto-grader-required <b>meta</b> tag must be in the head area for all of the pages
for this <?= $assignment_type_lower ?>.
</li>
<li>
This can be added as a new application to your <b>dj4e</b> project.  You do not have to remove
existing applications, simply add a new <b><?= $main_lower_plural ?></b> application.   You should add a route
to your <b>dj4e/urls.py</b> as follows:
<pre>
urlpatterns = [
    path('', include('home.urls')),
    path('admin/', admin.site.urls),
    path('accounts/', include('django.contrib.auth.urls')),
<?php if ( $main_lower_plural != 'autos' ) { ?>
    path('autos/', include('autos.urls')),
<?php } ?>
    path('<?= $main_lower_plural ?>/', include('<?= $main_lower_plural ?>.urls')),
]
</pre>
<li>
You must follow the URL patterns within your application that are used in the sample CRUD code.
You do not need to change the <b>main</b> or <b>lookup</b> urls
in <b><?= $main_lower_plural ?>/urls.py</b> -
URLs for your new app should look like:
<pre>
/<?= $main_lower_plural ?>/main
</pre>
</ul>
<?php if ( $reference_implementation ) { ?>
<h2>Sample Implementation</h2>
<p>
You can experiment with a reference implementation at:
</p>
<p>
<a href="<?= $reference_implementation ?>" target="_blank"><?= $reference_implementation ?></a>
</p>
<?php } ?>
<h2>Using the Autograder</h2>
<p>
This <?= $assignment_type_lower ?> will be automatically graded and so your web server will need an 
Internet-accessible URL so you can submit it for autograding.  You can do this either using
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> or 
<a href="https://www.ngrok.com" target="_blank">Ngrok</a>.
Instructions for using ngrok are available at:
</p>
<p>
<a href="http://www.dj4e.com/ngrok" target="_blank">http://www.dj4e.com/ngrok</a>
</p>
<p>
Please see the process for handing in the <?= $assignment_type_lower ?> at the end of this document.
</p>
<p>
<b>Important:</b> The autograder will demand that your &lt;meta&gt; tag is in the 
head area of your document.  If the autograder does not find the tag,
it will run all the tests but will not treat the grade as official.
</p>
<h2>Creating models for this application</h2>
<p>
The data models for this assignment should be as follows:
<pre>
from django.db import models
from django.contrib.auth.models import User
from django.core.validators import MinLengthValidator

class <?= $lookup_lower_title ?>(models.Model):
    name = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(2, "<?= $lookup_lower_title ?> must be greater than 1 character")]
    )

    def __str__(self):
        return self.name

class <?= $main_lower_title ?>((models.Model) :
    nickname = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(2, "Nickname must be greater than 1 character")]
    )
<?php
$first = True;
foreach($fields as $field ) {
    if ( ! $first ) echo(",\n");
    $first = False;
    if ( $field['type'] == 'i' ) {
        echo('    '.$field['name'].' = models.PositiveIntegerField()');
    } else {
        echo('    '.$field['name'].' = models.CharField(max_length=300)');
    }
}
echo("\n");
?>
    <?= $lookup_lower ?> = models.ForeignKey('<?= $lookup_lower_title ?>', on_delete=models.CASCADE, null=False)

    def __str__(self):
        return self.nickname

</pre>

<h1>What To Hand In</h1>
<p>
This <?= $assignment_type_lower ?> will be autograded by a link that you will be provided with in the LMS
system.   When you launch the autograder, it will prompt for a web-accessible URL
where it can access your web application.  
<?php if ( $assignment_type == 'Exam') { ?>
Please also have in a ZIP of your source code (entire project)
in case there is a need to verify your work or assign partial credit.
<?php } ?>
</p>
<hr/>
Provided by: <a href="http://www.dj4e.com/" target="_blank">www.dj4e.com</a>
<center>
<?php if ( strpos($assignment_type,'Exam') !== false ) { ?>
Copyright Charles R. Severance - All Rights Reserved
<?php } else { ?>
Copyright Creative Commons Attribution 3.0 - Charles R. Severance
<?php } ?>
</center>
</body>
</html>
