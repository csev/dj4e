Storing your code into GitHub
=============================

This excercise shows how to store your assignments in a private repository in 
<a href="https://www.github.com" target="_blank">GitHub</a>, 
if you have an account that supports a private repository.  Please don't put your
assignments into a public repository on GitHub.

Go to GitHub, create a new private repo called `django_projects` - do not create
a README, .gitignore, or add a license.  You can do those things later - but for now
we want to make a new fresh and *empty* repository.

Once yu have yout github repository, go to your 
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a> 
account and start a bash shell.

Create a file

    cd ~/django_projects
    nano .gitignore

Put these three lines into the file and save it.

    __pycache__
    *.swp
    *.sqlite

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
    git remote add origin https://github.com/--your-git-acct--/django_projects.git
    git push -u origin master
    (enter id and password for git)

Go to 

    https://github.com/csev/django_projects

Verify the data has been pushed to the repo and verify that it is private.

If you get tired of typing your github credentials over and over, you can tell
the bash shell to cache them for a week using the following caommands:

References
----------

https://help.github.com/articles/caching-your-github-password-in-git/#platform-linux


