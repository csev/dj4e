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
    Python 3.11.6
    $ python -m django --version
    4.2.3

If the above does not work on Windows, try:

    > py --version
    Python 3.11.6
    > py -m django --version
    4.2.3

You also need to have `git` installed and available in the shell / command line.

Why Develop Locally?
--------------------

There are a number of advantages to doing development work locally:

* You never have to `Reload` your application.  The Django `runserver` process monitors
changes to your files and completely restarts itself as soon as any file changes in your
project.   This makes for much quicker edit-test cycles.

* You can use a real developer text editor like VScode or Cursor

* No more need to change the WGSI configuration file when you want to switch between
your project and some sample code - you can even run more than one application at the
same time on different ports.

* You can put debug `print()` statements and they come right out without having to look
at the error or server logs.  Error tracebacks and error logs come right out.

* You can work without a network connection!

These instructions assume you that you are already set up using Github and PythonAnywhere
and want to edit your applications on your computer
and present them on PythonAnywhere.

For some of the assignments, you might be able to use the <b>ngrok</a> application to share your
locally running application with the Internet and auto graders, but we have found situations where
ngrok does not work with the autograders for this course.  And as Dango is improving its
sequrity around sessions and CSRF protection, ngrok can disturb these processes and break.
So the only safe thing to do is have your application running on PythonAnywhere.

Developing locally and on PythonAnywhere at the same time is pretty challenging and for those
that don't already have a fstrong foundation of git and local installation and use of
virtual environments on your computers, you should avoid local develop and use
PythonAnywhere exclusively.

On Your Laptop
--------------

We suggest you make a folder somewhere to store all of your Django projects.  This folder
should be easy to find so you can use your IDE.

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

    python3.11 -m venv .venv
    source .venv/bin/activate
    python --version
    pip install -r requirements42.txt

    # Do this until there are no errors
    python manage.py check

Once you have the requirements installed and you are passing the `check` step
without errors, lets make a database and put a bit of data in the database:

    python manage.py makemigrations
    python manage.py migrate
    python manage.py createsuperuser

To run the server get into the folder with `manage.py` and then:

    python manage.py runserver

Then navigate to http://localhost:8000 to see the page.

Coming Back After You Have Closed Your Terminal/Shell
-----------------------------------------------------

Once you close the shell / command line you need to activate the correct virtual environment:

     cd ~
     cd Desktop
     cd django
     cd dj4e-samples
    source .venv/bin/activate
    python manage.py check

