<?php

if ( ! isset($_GET['assn'] ) ) die('No assignment');

$assn = $_GET['assn'];

// if ( strpos($assn, '02') !== 0 ) die('Bad format');
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
track data about <?= strtolower($title_plural) ?> <?= $assignment_examples ?>.
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
<p>
<b>Note:</b> You must upload a ZIP file of your application to the LMS
to receive full credit for the exam even if the autograder gives you a perfect score.
If your application is not working at the end of the exam period (even
if the autograder still is giving you a score of zero), make sure to ZIP
up your code and upload it to the LMS so we can hand grade your exam
and give you a more appropriate score than the autograder.
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
<a href="https://github.com/csev/dj4e-samples/tree/master/autos" target="_blank">https://github.com/csev/dj4e-samples/tree/master/autos</a>
</pre>
</li>
</ul>
<?php } ?>
<?php if ( $reference_implementation ) { ?>
<h2>Sample Implementation</h2>
<p>
You can experiment with a reference implementation at:
</p>
<p>
<a href="<?= $reference_implementation ?>" target="_blank"><?= $reference_implementation ?></a>
</p>
<p>
The login information is as follows:
<pre>
    Account: dj4e-crud
    Password: dj4e_nn_!
</pre>
The 'nn' is a 2-digit number that by now, you should be able to easily guess.
</p>

<?php } ?>
<h2 clear="all">Specifications / Tasks</h2>
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
This can be added as a new application to your <b>mysite</b> project.  You do not have to remove
existing applications, simply add a new <b><?= $main_lower_plural ?></b> application.
Activate any virtual environment you need (if any) and go into your `django_projects` folder
and start a new application in your `mysite` project (this project already should have
<?php if ($main_lower_plural == 'autos') { ?>
a 'hello' application from a previous assignment):
<?php } else { ?>
'hello' and 'autos' applications from previous assignments):
<?php } ?>
<pre>
    workon django4                   # or django3 needed
    cd ~/django_projects/mysite
    python manage.py startapp <?= $main_lower_plural ?>
</pre>
</li>
<li>
Edit the <b>django_projects/mysite/mysite/settings.py</b> to update the INSTALLED_APPS:
<pre>
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'home.apps.HomeConfig',
    'autos.apps.AutosConfig',
<?php if ($main_lower_plural != 'autos') { ?>
    '<?= $main_lower_plural ?>.apps.<?= ucfirst($title_plural) ?>Config',    &lt;---- Add this
<?php } ?>
]
</pre>
</li>
<li>
<p>
Edit the <b><?= $main_lower_plural ?>/models.py</b> file to add <?= $main_title ?> and <?= $lookup_title ?> models
as shown below with a foreign key from <?= $main_title ?> to <?= $lookup_title ?>.
<pre>
from django.db import models
from django.core.validators import MinLengthValidator

class <?= $lookup_title ?>(models.Model):
    name = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(2, "<?= $lookup_title ?> must be greater than 1 character")]
    )

    def __str__(self):
        return self.name

class <?= $main_title ?>(models.Model):
    nickname = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(2, "Nickname must be greater than 1 character")]
    )
<?php
foreach($fields as $field ) {
    if ( $field['type'] == 'i' ) {
        echo('    '.$field['name']." = models.PositiveIntegerField()\n");
    } else {
        echo('    '.$field['name']." = models.CharField(max_length=300)\n");
    }
}
?>
    <?= $lookup_lower ?> = models.ForeignKey('<?= $lookup_title ?>', on_delete=models.CASCADE, null=False)

    def __str__(self):
        return self.nickname
</pre>
<li>
Run the commands to perform the migrations.
</li>
<li>
Add a link to <b>django_projects/mysite/home/templates/home/main.html</b> that has the text for the top-level page.
<pre>
    &lt;ul&gt;
<?php if ($main_lower_plural != 'autos') { ?>
    &lt;li&gt;&lt;a href="/autos"&gt;Autos CRUD&lt;/a&gt;
<?php } ?>
    &lt;li&gt;&lt;a href="/<?= $main_lower_plural ?>"&gt;<?= $main_title_plural ?> CRUD&lt;/a&gt;
    &lt;ul&gt;
</pre>
</li>
<li>
You should add a route to your <b>django_projects/mysite/mysite/urls.py</b> as follows:
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
</li>
<li>
Create the <b><?= $main_lower_plural ?>/urls.py</b> file to add routes for the list,
edit, and delete pages for both <?= $main_lower_plural ?> and <?= $lookup_lower_plural ?>.
You do not need to change the <b>main</b> or <b>lookup</b> urls
in <b><?= $main_lower_plural ?>/urls.py</b> -
<?php if ( $main_lower_plural != 'autos' ) { ?>
<p>
You should change the 'name=' values and class name on the paths from the sample application so you don't conflict
with the 'autos' application:
<pre>

urlpatterns = [
    path('', views.<?= $main_title ?>List.as_view(), name='all'),
    path('main/create/', views.<?= $main_title ?>Create.as_view(), name='<?= $main_lower ?>_create'),
    path('main/&lt;int:pk&gt;/update/', views.<?= $main_title ?>Update.as_view(), name='<?= $main_lower ?>_update'),
    ...
]
</pre>
</p>
<?php } ?>

</li>
<li>
Edit the <b><?= $main_lower_plural ?>/views.py</b> file to add/edit views for the
list, edit, and delete pages for both <?= $main_lower_plural ?> and <?= $lookup_lower_plural ?>.
</li>
<li>
Add the appropriate templates to <b><?= $main_lower_plural ?>/templates</b> following the naming
conventions for the templates.
</li><li>
If you have not already done so,
create the necessary templates in <b>home/templates/registration</b> to support the login / log out views.
</li>
<li>
Edit <b><?= $main_lower_plural ?>/admin.py</b> to add the <?= $main_title ?> and <?= $lookup_title ?> models to the
Django administration interface.
</li>
<li>
If you have not already done so, create a superuser so you can test the admin interface and log in to the application.
</li>
</ul>
<h1>Hand-Testing Your Application</h1>
<p>
While it might be tempting to edit all your code following the above
instructions and immediately send it to the autograder, you usually can save
a lot of time by running a quick hand-check of your application.  Errors
are much easier to see in a browser than in the autograder.  Do all these steps:
<ul>
<li>Log in to your application</li>
<li>Go to the main page to list all the <?= $main_lower_plural ?></li>
<li>Add a new <?= $lookup_lower ?></li>
<li>View all the <?= $lookup_lower_plural ?></li>
<li>Update a <?= $lookup_lower ?></li>
<li>Add a new <?= $main_lower ?></li>
<li>View all the <?= $main_lower_plural ?></li>
<li>Update a <?= $main_lower ?></li>
<li>Delete a <?= $main_lower ?></li>
<li>View all the <?= $lookup_lower_plural ?></li>
<li>Delete a <?= $lookup_lower ?></li>
</ul>
You should be able to do this hand test in about a minute once you do it
a few times.
</p>
<p>
Once you are confident that all the views are working without error, then you
can send it to the autograder.
</p>
<h1>Using the Autograder</h1>
<p>
This <?= $assignment_type_lower ?> will be automatically graded.  You will have
unlimited attempts in the autograder until the deadline for submission.   Your web server will need an
Internet-accessible URL so you can submit it for autograding.  You can do this either using
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>.
</p>
<p>
Please see the process for handing in the <?= $assignment_type_lower ?> at the end of this document.
</p>
<p>
If the autograder complains about a missing "dj4e" meta tag, add or edit it in
your <b>home/templates/base_bootstrap.html</b> file:
<pre>
&lt;meta name="dj4e" content="--provided-by-autograder--"&gt;
</pre>
If the autograder does not find the tag, it will run all the tests but
will not treat the grade as official.
</p>
<p>
If the autograder complains about a missing "dj4e-code" meta tag, add or edit it in
your <b>home/templates/base_bootstrap.html</b> file:
<pre>
&lt;meta name="dj4e-code" content="<span id="dj4e-code">missing</span>"&gt;
</pre>
If you are adding an application to an existing Django project that you have already run through
the autograder, you probably already have good value for both meta tags in your template file.
</p>
<h1>What To Hand In</h1>
<p>
This <?= $assignment_type_lower ?> will be autograded by a link that you will be provided with in the LMS
system.   When you launch the autograder, it will prompt for a web-accessible URL
where it can access your web application.
<?php if ( $assignment_type == 'Exam' || $assignment_type == 'Sample Exam') { ?>
Please also have in a ZIP of your source code of your new application
in case there is a need to verify your work or assign partial credit.
</p>
<p>
If you are doing your work on PythonAnywhere, go into a console shell and create a ZIP file as follows:
<pre>
    cd ~/django_projects/mysite
    rm -f <?= $main_lower_plural ?>.zip
    zip -r <?= $main_lower_plural ?>.zip <?= $main_lower_plural ?> -x '*pycache*'
</pre>
Then download the ZIP file from `django_projects/mysite` using the
Files tab of PythonAnywhere and upload the ZIP file back up to the LMS.  Some browsers
(i.e. Safari on the Mac) automatically extract the ZIP into a folder.  If this
happens simply compress it again to make a ZIP to upload.
</p>
<p>
If you are doing your work locally on a Mac or Windows and using ngrok to submist your assignment, you can make a ZIP by using the "Compress
Folder" feature.
</p>
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
<script>
var d= new Date();
var code = "42"+((Math.floor(d.getTime()/1234567)*123456)+42)
document.getElementById("dj4e-code").innerHTML = code;
</script>
</body>
</html>
