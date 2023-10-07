Using a Virtual Environment on PythonAnywhere
=============================================

Using a virtual environment is essential to everything you do in this course.  If you start a
new console / shell and do not activate the correct virtual environment - literally nothing will work,
you will get confused thinking yuor code is broken and start editing files and breaking your application
while the only thing you needed to do was activate the virtual environment.

In this document we show how to check your virtual envronment, activate yiur virtual environment, and
change your configuration so the virtual environment is automatically activated whenever you make a new
console.

Before You Start Any Assignment
-------------------------------

Before you start any assignment, make sure your bash shell has activated your virtual environment.
You know that you are in your virtual environment when you see the bash prompt as something like this:

    (django4) 12:08 ~/django_projects/mysite $

If you don't see `(django4)` at the beginning  your prompt, run the command:

    workon django4

Without the virtual environment activated, literally nothing will work.

Once you are in your virtual environment, it is good to double check that your application is in working
condition before you start making changes.  To verify that your project is not broken run:

    cd ~/django_projects/mysite
    python manage.py check

**Important:** If this has errors, do not continue until you figure out what is wrote with your application.
If your application is broken, running the commands below will *break it worse*.  So make sure to start with
a clean and working application.

Automatically Enabling Your Virtual Environment
-----------------------------------------------

Each time you start a new shell, you will need to activate your virtual environment.  It
is a lot simpler to do this automatically every time you login by editing the `.bashrc` file
in *your* home directory.

    /home/(your-account)/.bashrc

Look for lines near the end of the file that look like:

    # Load virtualenvwrapper
    source virtualenvwrapper.sh &> /dev/null

Add the following lines at the end of the file and save the file.

    # Auto switch into django4 virtual environment
    workon django4

The next time you start a console/shell, the shell should be using the `django4` environment
and you should see the virtual environment indicator in your shell prompt:

    (django4) 13:29 ~ $

Congratulations - you will no longer waste a bunch of time or break your application because
you forgot to activate your virtual environment.

