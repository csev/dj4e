Developing with Django on your computer
=======================================

As you start to develop more intricate Django applications, you might find it more
convienent to install Django on your local computer and then use githib to move your
tested code into your 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
server so you it is available on the Internet for grading or otherwise sharing.

There are many sources of tutorial material on how to install Python3 and Django on
your computer.  Since the rest of this material uses the Mozilla Developer Network
tutorials, you might as well use it:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/development_environment

You can use a virtual environment or if you have a suitable version of Python 3.x on your
computer and can use `pip` to install a suitable version of Django for your whole computer,
there is no need to put things in a virtual environment.

However you get Python 3 and Django installed, you know you are good when these commands
show reasonable versions for these commands:

Linux/Mac:

    $ python3 --version
    Python 3.6.0
    $ python3 -m django --version
    2.0.5

If the above does not work on Windows, try:

    > py --version
    Python 3.6.0
    > py -m django --version
    2.0.5

You also need to have `git` installed and available in the shell / command line.

Why Develop Locally?
--------------------

There are a number of advantages to doing development work locally:

* You never have to `Reload` your application.  The Django `runserver` process monitors
changes to your files and completely restarts itself as soon as any file changes in your
project.   This makes for much quicker edit-test cycles.

* You can use a fancy text editor like VScode, Atom, or Sublime.

* No more need to change the WGSI configuration file when you want to switch between
your project and some sample code - you can even run more than one application at the
same time on different ports.

* You can put debug `print()` statements and they come right out without having to look
at the error or server logs.  Error tracebacks and error logs come right out.

* You can work without a network connection!

These instructions assume you that you are already set up using Github and PythonAnywhere 
and want to edit your applications on your computer
and present them on PythonAnywhere.  But you can always use the
<a href="../ngrok">ngrok</a> application
to submit your assignments to the autograder if you like.

Before You Start
----------------

Before you start doing this, make sure that your code in the PythonAnywhere shell
is fully checked in to GitHub:

    $ cd ~/django_projects
    $ git status
    On branch master
    Your branch is up-to-date with 'origin/master'.
    nothing to commit, working directory clean

If you have any outstanding git stuff on PYAW - clean it up and push it ot the repo.

On Your Laptop
--------------

We suggest you make a folder somewhere to store all of your Django projects.  This folder
should be easy to find so you can use your cool VSCode, Atom, or Sublime text editor.

Linux / MacOS / Windows bash shell:

    cd ~
    cd Desktop
    mkdir django

Windows Command Line:

    cd
    cd Desktop
    mkdir django

Then lets checkout the dj4e-samples repo

    cd Desktop      # If not already there
    cd django
    git clone https://github.com/csev/dj4e-samples

Then lets setup the database for the main sample applications:

    cd dj4e-samples
    cd samples
    python3 manage.py migrate 
    python3 manage.py runserver 

Then navigate to http://localhost:8000 to see the page.

Then just for fun, open a second terminal / shell / command line and:

    cd Desktop
    cd django
    cd dj4e-samples
    cd samples
    python3 manage.py runserver 8001

Then navigate to http://localhost:8001 to see the page.

You can abort the `runserver` applications, switch to a new folder and start runserver again.

You can check out more than one set of projects into the `django` folder.   Here I check 
out my private repo with the solutions to the assignments so I can work on them.

    cd ~           # or simple 'cd' for Windows
    cd Desktop
    cd django
    git clone https://github.com/--your-github-account--/django_projects
    cd django_projects
    cd locallibrary
    python3 manage.py migrate
    python3 manage.py runserver

Then navigate to http://localhost:8000 to see the page.

You only have to do the `migrate` once right after the checkout - or whenever
you change models.

Also, remember that the `runserver` process automatically restarts itself if any files in the
project are updated.

Developing Locally and and Moving to PythonAnywhere
---------------------------------------------------

So you are on your local laptop / computer and are making changes

    cd ~         # or simple 'cd' for Windows
    cd Desktop
    cd django
    cd django_projects
    cd locallibrary
    # edit some files :)
    python3 manage.py makemigrations    # If you changed your models
    python3 manage.py migrate           # If you changed your models.py
    git status
    git add ....
    git commit -a
    git push

Then you can go into PythonAnywhere ina bash shell and type:

    cd ~/django_projects/locallibrary
    git pull
    python3 manage.py migrate         # If the pull included new migrations


Then in the "Web" tab reload your application and visit it.

As long as you follow the pattern of doing `git push` from your laptop/desktop and `git pull`
from PythonAnywhere, things will go very smoothly.

If you edit two places and push from one of the places, the push will work - but the push
won't work from the second place and pull won't work either becausee you have local changes.
If this is what you did, there is a simple workaround.  On the system where you have un-pushed changes and want to do a pull befor pushing, do this:

    git stash
    git pull
    git stash apply

This takes your un-pushed changes and hides them in the "stash", allowing the `git pull` to 
work and then the `stash apply` re-modifies the files.

Most of the time this works if all you did is edited two places and tried to push from both.


