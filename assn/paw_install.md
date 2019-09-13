Installing Django on PythonAnywhere
===================================

Before you start this assignment, you should already have signed up for a 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
account and be logged in on your account.  You should be able to complete all
of the exercises in this course using a free PythonAnywhere account.

This is a set of instructions to go through the first step of the 
Mozilla Developer Network (MDN) Django tutorial to get 
Django intalled on your PythonAnywhere account.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/development_environment

This is adapted from the PYAW documentation for following the Django tutorial.

https://help.pythonanywhere.com/pages/FollowingTheDjangoTutorial/

Feel free to look at that page as well.

You can view a
<a href="https://www.youtube.com/watch?v=lPpIubhqWR4&index=2&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z" target="_blank">video walkthrough</a> of this assignment.

You can do all of the assignments on your local computer instead 
of PythonAnywhere.  You will need to use the
<a href="../ngrok">ngrok</a> application
to submit your assignments to the autograder.

**Note:** If you are submitting these assignments to the auto grader you 
should complete each assignment and then submit it and get full credit before
moving on to the next assignment.  Because the assignments build on one another the application that you have build by the last step of the tutorial
will no longer pass the earlier autograders.

Instructions
------------

Once you have created your PYAW account, start a `bash` shell
and set up a virtual environment with Python 3.x and Django 2.

    mkvirtualenv django2 --python=/usr/bin/python3.6
    pip3 install django ## this may take a couple of minutes

Note if you exit and re-start a new shell on PythonAnywhere - you need the following command
to get back into your virtual environment in the new bash shell.

    workon django2

Lets make sure that your django was installed successfully with the following command:

    python3 -m django --version
    # This should show something like 2.1.4 

Lets also get a copy of the sample code for DJ4E checked out so you can look at sample code
as the course progresses and install some important additional Django software libraries using 
`pip3`.

    cd ~
    git clone https://github.com/csev/dj4e-samples
    cd dj4e-samples
    pip3 install -r requirements.txt
    python3 manage.py makemigrations
    python3 manage.py migrate

In the PYAW shell, continue the steps from the MDN:

    cd ~
    mkdir django_projects
    cd django_projects
    django-admin startproject mytestsite

In the PYAW web interface navigate to the `Web` tab to create a new web application.  If you
have not already done so, add a new web application.  Select `manual configuration` and Python
3.6.  Once the webapp is created, you need to make a few changes to the settings for the web
app and your application.

    source code: /home/--your-account---/django_projects/mytestsite
    working directory: /home/--your-account---/django_projects/mytestsite
    virtualenv: /home/--your-account---/.virtualenvs/django2

Then edit the *WGSI Configuration File* and put the following code into it.
Make sure to delete the existing contenxt of the file and replace it with the text below.
This is slightly different from the sample in the PythonAnywhere tutorial.

    import os
    import sys

    path = os.path.expanduser('~/django_projects/mytestsite')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'mytestsite.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

You need to edit the file `~/django_projects/mytestsite/mytestsite/settings.py` and change
the allowed hosts line (around line 28) to be:

     ALLOWED_HOSTS = [ '*' ]                                                                                                        

There are three ways to edit files in your PythonAnywhere environment, ranging from the easiest
to the coolest.  You only have to edit the file one of these ways.

(1) Go to the main PythonAnywhere dashboard, browse files, navigate to the correct folder and edit the file

    /home/mdntutorial/django_projects/mytestsite/mytestsite/settings.py

(2) Or in the command line:

    cd ~/django_projects/mytestsite/mytestsite/
    nano settings.py

    Save the File by pressing 'CTRL-X', 'Y', and Enter

(3) Don't try this most difficult and most cool way to edit files on Linux without a helper
if it is your first time with the `vi` text editor.
    
    cd ~/django_projects/mytestsite/mytestsite/
    vi settings.py

Once you have opened `vi`, cursor down to the `ALLOWED_HOSTS` line,
position your cursor between the braces and press the
`i` key to go into 'INSERT' mode, then type your new text and press the `esc` key when you are
done.  To save the file, you type `:wq` followed by `enter`.  If you get lost press `escape` `:q!`
`enter` to get out of the file without saving.

If you aleady know some _other_ command line text editor in Linux, you can use it to edit files.  In general,
you will find that it often quicker and easier to make small edits to files in the command line
rather than a full screen UI.  And once you start deploying real applications in production
environments like Google, Amazon, Microsoft, etc.. all you will have is command line.

Once this file has been edited, on the PYAW Web tab, `Reload` your web application, wait a few seconds and check
that it is up and running:

    http://--your-account--.pythonanywhere.com/

Here is a
<a href="paw_install/index.htm" target="_blank">Sample</a>
of what the resulting page should look like.
