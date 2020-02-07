DIY: Hello World / Sessions
===========================

In this application, you will make a new application in your `django_projects`
and add a bit of code to make use of sessions.  A key element of this assignment is
that we will tell you "what to do" and less exact steps to cut and paste.  It is time
for you to understand how to "Do It Yourself" (DIY).

This assignment will not have any models - just views.  In this assignment you will
be looking at the `dj4e-samples` code and figuring out how to read and adapt sample code.
You should make sure to get the latest updates by doing the following:

    cd ~/dj4e-samples
    git pull

Building a Main Page
--------------------

It is time for your the "/" URL in your application to actually refer to a real page instead
of throwing a "404 Not Found" error.  We will store this page (and other project-wide
bits) in an application named "home".  To get started:

    workon django3                  # as needed
    cd ~/django_projects/mysite
    python3 manage.py startapp home

Create an HTML file in `~/django_projects/mysite/home/templates/home/main.html` with the following HTML:

    <ul>
    <li><p><a href="/polls">A Polls Application</a></p>
    </ul>


You can add more HTML - but this list and links should be somewhere in the template.

Edit the file `~/django_projects/mysite/mysite/urls.py` and add following path route:

    path('', TemplateView.as_view(template_name='home/main.html')),

You will need to make sure to add the proper python imports at the top of the file to make this work.
There are lots of examples of the use of TemplateView in the `urls.py` files in `dj4e-samples`
and in the lecture materials.

Make sure to run:

    python3 manage.py check

To see if your changes have syntax errors, then Reload your web application and 
navigate to the top level path (i.e. no path).  You should no longer
get a "404" and instead be able to navigate to the polls application by clicking on the link.
Congratulations!  You now have a web site that is not broken.

Note that this page is not present on the <a href="https://djtutorial.dj4e.com/" target="_blank">
https://djtutorial.dj4e.com/</a> server.  It still shows "404" when you navigate to the top URL.

Playing With Sessions (DIY)
---------------------------

You next goal is to make a new application named `hello` and replicate the functionality
at https://samples.dj4e.com/session/sessfun except make it so that it responds to the
URL `/hello` in your Django project like in <a href="https://djtutorial.dj4e.com/hello" target="_blank">
https://djtutorial.dj4e.com/hello</a>

Add a link to `~/django_projects/mysite/home/templates/home/main.html` with the following HTML:

    <li><p><a href="/hello">Test the session</a></p>

This is the DIY part of this assignment - no more cutting and pasting instructions :)

There is lots of sample code in `dj4e-samples` - probably
too much code.  If you just copy and paste everything from `dj4e-samples` it will be difficult to
make this work.  Sometime when looking at code written by others, it is important to know what
is going on to what *not* to copy.

Also make sure to check any auto grader for any additional requirements for this assignment.

