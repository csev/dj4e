Storing your Django Projects in GitHub
======================================

This excercise shows how to store your assignments in a private repository in 
<a href="https://www.github.com" target="_blank">GitHub</a>, 
if you have an account that supports a private repository.  Please don't put your
assignments for this site into a public repository on GitHub.

You can view a
<a href="https://www.youtube.com/watch?v=9FJwue2Eqao&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z&index=2" target="_blank">video walkthrough</a> of this assignment.

Go to GitHub, create a new private repo called `django_projects` - do not create
a README, .gitignore, or add a license.  You can do those things later - but for now
we want to make a new fresh and *empty* repository.

Go to your 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> 
account and start a bash shell.

Create a file

    cd ~/django_projects
    nano .gitignore

Put these three lines into the file and save it.

    __pycache__
    *.swp
    *.sqlite3

Remember that to see all the files in a folder (including those that start with a '.')
you need to type `ls -la`.

Still in your PythonAnywhere shell in the `~/django_projects` folder, run
the following commands:

    git init
    git config --global push.default simple
    git add *
    git add .gitignore
    git status
    git config --global user.email "youremail@umich.edu"
    git config --global user.name "Your X. Name"
    git config --global credential.helper cache   # Optional but convienent
    git config --global credential.helper 'cache --timeout=604800'  # Optional but convienent
    git commit -m "first commit" 
    git remote add origin https://github.com/--your-github-acct--/django_projects.git
    git push -u origin main
    (enter id and password for git)

Go to 

    https://github.com/--your-github-account---/django_projects

Verify the data has been pushed to the repo and verify that it is private.

If you get tired of typing your github credentials over and over, you can tell
the bash shell to cache them for a week using the following commands:

Local and Remote Repositories Out of Sync
-----------------------------------------

Sometimes when you have a repo and are working on files, and start typing
things like `git commit -a` or `git push` you start getting very strange
errors start appearing that imply that you can't do what you are trying to
do.

Often this is because you are doing things in your repo two different
places (i.e. your laptop and PythonAnywhere) and one or the other copies is
out of sync with the copy that you have stored in GitHub.

When your remote repository is "ahead" of your local repository, you 
will see the following error when you do a `git pull`:

    $ git status
    On branch main

    	modified:   mysite/settings.py

    $ git pull
    error: cannot pull with rebase: You have unstaged changes.
    error: please commit or stash them.

Solving this is pretty simple, the following sequence will work:

    git stash
    git pull
    git stash apply

This undoes your local changes (except for added files whch don't 
affect the pull) but "stashes" them. 
Then the pull will work and the `git stash apply` retrieves your "stashed" 
changes and re-applies them.  

Harder to Fix Problems
----------------------

If you end up in a situation where git is complaining about a "merge conflict"
you can Google around and find a solution.  But sometimes that is 
kind of obtuse and it is easier to grab a fresh copy of your repo 
and manually re-apply your changes.

If things are really messed up and `git` is complaining about a lock file,
or something else very mysterious, sometimes the quickest 
way to get going again is to take a step back
and then go forward again.  This process assumes that you have something in 
github to go back to.  

Here are the steps to restore your `django_projects` folder.  You may 
lose a few bits in this process but your git folder will work again.

First lets see what changes we have made because we will need to redo
the changes.

    cd ~/django_projects
    git status

Then rename your folder and check out a fresh copy of your repository:

    cd ~
    mv django_projects broken_version
    git clone https://github.com/drchuck/django_projects.git

Replacing `drchuck` with your github account.

Now you have a fresh new copy of your github repository.  Then go into
the saved copy and see which files you want to copy over into the newly
checked out copy of your repo:

    cd ~/broken_version
    git status

Then for each file you want to put back into your freshly checked out repo, do:

    cp mysite/mysite/settings.py ../django_projects/mysite/mysite/settings.py

You can also use the Linux `diff` command to see how files differ before making
the copy:

    diff mysite/mysite/settings.py ../django_projects/mysite/mysite/settings.py

Use tab completion to make sure that you are typing folders and files 
correctly.

If you are dealing with a merge conflict, you may still need to go into the
newly checked out folder and edit the files before committing.  The git
merge process puts little marks in the file to show where the conflicts
were found.  Just test your code before committing and uploading - any merge
lines in the file will be syntax errors in your application that you can
fix.

You can change the folder paths from this example depending on 
what repo you are working with and where in your folder structure 
you are working.

References
----------

https://help.github.com/articles/caching-your-github-password-in-git/#platform-linux



Developing with Django on your computer
=======================================

As you start to develop more intricate Django applications, you might find it more
convienent to install Django on your local computer and then use github to move your
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

Also you should install git on your computer using any of the good tutorials out there.

You know Python and Django are correctly installed when these commands
show reasonable version numbers:

Linux/Mac:

    $ python --version
    Python 3.8.0
    $ python -m django --version
    3.2.5

If the above does not work on Windows, try:

    > py --version
    Python 3.8.0
    > py -m django --version
    3.2.5

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

Then lets checkout the dj4e-samples repo and get things started:

    cd Desktop      # If not already there
    cd django
    git clone https://github.com/csev/dj4e-samples
    cd dj4e-samples
    pip install -r requirements42.txt
    python manage.py check

Once you have the requirements installed and you are passing the `check` step
without errors, lets make a database and put a bit of data in the database:

    python manage.py migrate
    python manage.py createsuperuser
    python manage.py runscript gview_load
    python manage.py runscript many_load

To run the server get into the folder with `manage.py` and then:

    python manage.py runserver

Then navigate to http://localhost:8000 to see the page.

Then just for fun, open a second terminal / shell / command line and:

    cd Desktop
    cd django
    cd dj4e-samples
    python manage.py runserver 8001

Then navigate to http://localhost:8001 to see the page.

You can abort the `runserver` applications in the command line, switch to
a new folder and start runserver again.

This section shows how to get a Django application working - in the next section
we show you to work on your code on two computers and move
the changes back and forth.

Moving Code Between Your Laptop and PythonAnywhere
--------------------------------------------------

If you want, you can keep a local copy your `django_projects` synchronized with your
copy on PythonAnywhere using `github`.

If you are already using github to store your projects
------------------------------------------------------

Before you start doing this, make sure that your code in the PythonAnywhere shell
is fully checked in to GitHub:

    $ cd ~/django_projects
    $ git status
    On branch main
    Your branch is up-to-date with 'origin/main'.
    nothing to commit, working directory clean

If you have any outstanding git modifications on PYAW - clean it up and push it to the repo.

If you have not yet put your PythonAnywhere code on github
----------------------------------------------------------

If you have not yet uploaded your `django_projects` folder to github, first follow the
<a href="paw_github.md">these instructions</a> to get your application uploaded to github.

Checking your github code out onto your laptop
----------------------------------------------

Once your application is in github, you can simply check it out to your laptop computer
using commands like:

    cd ~         # or simple 'cd' for Windows
    cd Desktop
    cd django
    git clone https://github.com/drchuck/django_projects.git

Replacing `drchuck` with your github account.  This should bring a copy of your
application from github down to your computer and store it in the folder
`django_projects`.

Note that you should keep the `dj4e-samples` and `django_projects` next to each
other since they are both in github.

On your laptop:

    Desktop/django/dj4e-samples
    Desktop/django/django_projects

On PythonAnywhere:

    ~/dj4e-samples
    ~/django_projects


Working on your Code on your Laptop
-----------------------------------

So you are on your local laptop / computer and are making changes

    cd ~         # or simple 'cd' for Windows
    cd Desktop
    cd django
    cd django_projects
    cd mysite    # Or whatever project you want to work on
    # edit some files :)
    python manage.py makemigrations    # If you changed your models
    python manage.py migrate           # If you changed your models.py
    git status
    git add ....
    git commit -a
    git push

Then you can go into PythonAnywhere in a bash shell and type:

    cd ~/django_projects/mysite
    git pull
    python manage.py migrate         # If the pull included new migrations

Then in the "Web" tab reload your application and visit it.

As long as you follow the pattern of doing `git push` from your laptop/desktop and `git pull`
from PythonAnywhere, things will go very smoothly.

If you edit two places and push from one of the places, the push will work - but the push
won't work from the second place and pull won't work either becausee you have local changes.
If this is what you did, there is a simple workaround.  On the system where you have un-pushed
changes and want to do a pull before pushing, do this:

    git stash
    git pull
    git stash apply

This takes your un-pushed changes and hides them in the "stash", allowing the `git pull` to
work and then the `stash apply` re-modifies the files.

Most of the time this works if all you did is edited two places and tried to push from both.


