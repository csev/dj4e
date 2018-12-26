Moving your code into github
============================

Go to github, create a new private repo called `django_projects`

Go into a PythnonAnywhere shell

    cd ~/django_projects
    git init
    git add *
    git status
    git config --global user.email "youremail@umich.edu"
    git config --global user.name "Your X. Name"
    git commit -m "first commit" 
    git remote add origin https://github.com/csev/django_projects.git
    git push -u origin master
    (enter id and password for git)

Go to 

    https://github.com/csev/django_projects

Verify the data has been pushed to the repo and verify that it is private.

If you get tired of typing your github credentials over and over, you can tell
the bash shell to cache them for a week using the following caommands:

https://help.github.com/articles/caching-your-github-password-in-git/#platform-linux

    git config --global credential.helper cache
    git config --global credential.helper 'cache --timeout=604800'

