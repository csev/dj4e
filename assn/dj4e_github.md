Using Github on PythonAnywhere
==============================

This excercise shows how to store your assignments in a private repository in 
<a href="https://www.github.com" target="_blank">GitHub</a>, 
if you have an account that supports a private repository.  Please don't put your
assignments for this site into a public repository on GitHub.

You can view a
<a href="https://www.youtube.com/watch?v=9FJwue2Eqao&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z&index=2" target="_blank">video walkthrough</a> of this assignment.

Go to GitHub, create a new private repo called `django_projects` - do not create
a README, .gitignore, or add a license.  You can do those things later - but for now
we want to make a new fresh and *empty* repository.

If you are using PythonAnywhere, go to your 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> 
account and start a bash shell.

If you are using your own computer, install `git` and open a command window:

Edit a file called `django_projects/.gitignore` and put these three lines
into the file and save it.

    __pycache__
    *.swp
    *.sqlite3

Remember that in bash, to see all the files in a folder (including those that start with a '.')
you need to type `ls -la`.

In your bash shell/command line in the `django_projects` folder, run
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



