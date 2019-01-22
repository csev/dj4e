Storing your DJango Projects in GitHub
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

Still in your PythnonAnywhere shell in the `~/django_projects` folder, run
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
    git push -u origin master
    (enter id and password for git)

Go to 

    https://github.com/--your-github-account---/django_projects

Verify the data has been pushed to the repo and verify that it is private.

If you get tired of typing your github credentials over and over, you can tell
the bash shell to cache them for a week using the following commands:

When Things go Wrong
--------------------

Sometimes when you have a repo and are working on files, and start typing
things like `git commit -a` or `git push` you start getting very strange
errors start appearing that imply that you can't do what you are trying to
do.

Sometimes if you are trying to do a `git pull` and you made some local changes, the following sequense will work:

    git stash
    git pull
    git stash apply

This undoes your local changes (except for added files) but "stashes" them. 
Then the pull will work and the `git stash apply` pulls gets your "stashed" 
changes and re-applies them.  Then a `git push` might work.

If things are really messed up and `git` is complaining about a lock file
or something very mysterious, sometimes the quickest way is to take a step back
and then go forward again.  This process assumes that you have something in 
github - here are the steps to restore your `django_projects` folder.  You may 
lose a few bits in this process but your git folder will work again.

First lets see what changes we have made because we will need to redo
the changes.

    cd ~/django_projects
    git status

Then rename your folder and check out a fresh copy of your repository:

    cd ~
    mv django_projects broken_version
    git clone https://github.com/--your-github-acct--/django_projects.git

Now you have a fresh new copy of your github repository.  Then go into
the saved copy and see which files you want to copy over into the newly
checked out copy of your repo:

    cd ~/broken_version
    git status

Then for each file you want to put back into your check'ed out repo, do:

    cp locallibrary/locallibrary/settings.py ../django_projects/locallibrary/locallibrary/settings.py

Use tab completion to make sure that you are typing folders and files 
correctly.

You can change the folder paths from this example depending on what repo you are working with
and where in your folder structure you are working.

References
----------

https://help.github.com/articles/caching-your-github-password-in-git/#platform-linux


