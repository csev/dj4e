Moving your code into github
============================

Go to github, create a new private repo `mdn-tutorial`

Go into a PythnonAnywhere shell

    cd ~/mdn-tutorial
    git init
    git add *
    git status
    git config --global user.email "csev@umich.edu"
    git config --global user.name "Charles R. Severance"
    git commit -m "first commit" 
    git remote add origin https://github.com/csev/mdn-tutorial.git
    git push -u origin master
    (enter id and password for git)

Go to 

    https://github.com/csev/mdn-tutorial

Verify the data has been pushed.  And verify that it is private.
    


https://help.github.com/articles/caching-your-github-password-in-git/#platform-linux

Cache for  week.

    git config --global credential.helper cache
    git config --global credential.helper 'cache --timeout=604800'

