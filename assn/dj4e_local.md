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
    pip install -r requirements4.txt
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

