Moving your code into github
============================

Go to github, create a new private repo called `django_projects`

Create a file

    ~/django_projects/.gitignore

    __pycache__
    *.swp
    *.sqlite

Go into a PythnonAnywhere shell

    cd ~/django_projects
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


