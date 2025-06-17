
Dealing with Errors in Django 5.2 Projects on PythonAnywhere
============================================================

When you get stuck on something while developing your Django Application
on PythonAnywhere this page has some possible solutions.

* What happens when your PythonAnywhere account expires after 90 days?
* When everything works but your application says 'Something went wrong :-('
* What happens when you don't activate your virtual environment (.ve52)
* Your line 16 seems to have a syntax error in the text editor in `manage.py`
* What to do when you see 'SyntaxError' when running `manage.py`
* What to do when the tutorial tells you to do a `python manage.py runserver`
* What to do when the tutorial tells you to access 'localhost:8000'
* How to fix two `mysite` folders
* How and when you exit the Django shell (>>> prompt)

What happens when your PythonAnywhere account expires after 90 days
-------------------------------------------------------------------

PythonAnywhere free accounts are great and can be used forever but at least once every 90 days you need to log in and press the "Run until 3 months from now" button under the `Web` tab. 

<a href="dj4e_errors/web_renew.png" target="_blank">Extending your free PythonAnywhere account</a> (screenshot)

When your account expires you will see a "Coming Soon" message when you attempt to access your web site.
The good news is you are one click away from extending the expiration date.  None of your files, databases,
or configuration is removed - when you extend the datam and press "Reload" your site will be back as
if nothing happened.

You can extend by 90 days any time - it does not hurt to extend your account even before it has
expired.  So if things are weird - one of the first things to check is whether your
PythonAnywhere account has expired.

When everything works but your application says 'Something went wrong :-('
--------------------------------------------------------------------------

When you have made some changes to your application and reloaded your web application
and navigate to your web page and you get an error message that says "Something went wrong :("
like this:

<center><img src="dj4e_errors/went-wrong.png" alt="An error page showing 'Something went wrong' from a PythonAnywhere application" style="border: 1px black solid; width:50%;"></center>

You can see how to debug this error in a video:

<a href="https://www.youtube.com/watch?v=9h9r_jfQG8E" target="_blank">Fixing Django Errors on PythonAnywhere (19:38)</a>

The following are instructions you can follow to diagnose and fix the 'Something went wrong error :('.

To fix this problem, start a console / bash shell on your account and make sure you are in your virtual
environment.  Then type these commands:

    cd ~/django_projects/mysite
    python manage.py check

This can give one of two possible outputs.  If things are going well the last line of output will be:

    System check identified no issues (0 silenced).

If there is an error, it will be pretty verbose:

    Traceback (most recent call last):
        File "/home/dj4e/django_projects/mysite/manage.py", line 21, in <module>
            main()
        File "/home/dj4e/django_projects/mysite/manage.py", line 17, in main
            execute_from_command_line(sys.argv)

        (About 20 more lines of traceback)

        File "/home/dj4e/django_projects/mysite/mysite/urls.py", line 20, in <module>
            path('polls/', include('pollz.urls')),
        File "/home/dj4e/.ve52/lib/python3.9/site-packages/django/urls/conf.py", line 38, in include
            urlconf_module = import_module(urlconf_module)
        File "/usr/local/lib/python3.9/importlib/__init__.py", line 127, in import_module
            return _bootstrap._gcd_import(name[level:], package, level)
        File "<frozen importlib._bootstrap>", line 1030, in _gcd_import
        File "<frozen importlib._bootstrap>", line 1007, in _find_and_load
        File "<frozen importlib._bootstrap>", line 972, in _find_and_load_unlocked
        File "<frozen importlib._bootstrap>", line 228, in _call_with_frames_removed
        File "<frozen importlib._bootstrap>", line 1030, in _gcd_import
        File "<frozen importlib._bootstrap>", line 1007, in _find_and_load
        File "<frozen importlib._bootstrap>", line 984, in _find_and_load_unlocked
     ModuleNotFoundError: No module named 'pollz'

This is a mess of output but you can get some clues.  The file it is upset about is `/home/dj4e/django_projects/mysite/mysite/urls.py`
and the what went wrong was that I mis-spelled `polls` as `pollz`.  

To solve this, in another tab edit the file, save it and re-run 

    python manage.py check

Until there are no errors.  Then try to reload your application and go back to the application page to see if you
there is no "Something went wrong" error any more.

If you see "Something went wrong" and `check` shows no errors
-------------------------------------------------------------

In a rare situation, `python manage.py check` shows no errors but your
application will still not load successfully.

To dig further you will need to look through the error log
under the `Web` tab on PythonAnywhere:

<center><img src="dj4e_install/error_logs.png" alt="An image showing the three log links under the Web tab in PythonAnywhere" style="border: 1px black solid;"></center>

Load the the `error` log and scroll to the very bottom to find the most
recent log error messages.  It can be quite long and take a while to get to the bottom.
When you get to the bottom, you will likely find some kind of error message.  It will likely
be a traceback and many lines long.  Most of the traceback is showing the Django files
your application is using.  So you will need to lok carefully and find where it is mentioning
a problem with something in one of your files.

This is a little tricky - so you need to be a bit of a detective to figure things out.

You *must* be in your virtual environment
---------------------------------------

If you are not in your virtual environment in a bash shell on PythonAnywhere
lots of things will fail.  You will not have access to the correct version
of Python and you will not have a proper installation of Django.  You can always
check which python you are running using the `--version` option.

In the example below we are running Python 2.7.12 (bad)
without the virtual environment and once we activate into the virtual environment we are 
using Python 3.8 and Django 3.1.

    17:33 ~ $ python --version
    Python 2.7.12
    17:33 ~ $ python -m django --version
    1.11.26
    17:33 ~ $ source ~/.ve52/bin/activate
    (.ve52) 17:33 ~ $ python --version
    Python 3.9.5
    (.ve52) 17:36 ~ $ python -m django --version
    4.2.7
    (.ve52) 17:33 ~ $ 

Each time you start a new bash shell, you need to type ` source ~/.ve52/bin/activate`.  If you
leave and come back to a shell that is still running, if you see the '(.ve52)'
in your prompt - you do not have to re-run the `source` command.  It just needs
to be done once per shell (or done automatically in the `.bashrc`).

There are several errors that you might get if your virtual environment is
not activated:

* TypeError: argument of type 'PosixPath' is not iterable

The problem with these errors is that you think you are supposed to edit files
like `manage.py` or `settings.py` but the files are already just fine, you are
just using the wrong versions of software.

Your manage.py looks incorrect in the PythonAnywhere editor
-----------------------------------------------------------

First and most important - you should not ever change the contents of the `manage.py` file.
The file is built by Django for you and does not need to be changed.

However, if you open `manage.py` in the PythonAnywhere text editor even to look at it,
it will show a little red "X" on line 16 or 17 indicating a syntax error.

<img src="dj4e_tutpaw/file-manage-py-bad.png" style="width: 80%;"/>

The problem (much like the virtual environment problem) is the wrong version of Python.  The text editor
is looking at the file as a Python 2.x file and not as a Python 3.x file.  So the file
<a href="dj4e_tutpaw/file-manage-py-bad.png" target="_blank">shows a syntax error</a>.

*Do not fix this error*.

If the error really bothers you, edit the first line of the file and change `python` to `python3` and save
the file.   The <a href="dj4e_tutpaw/file-manage-py-good.png" target="_blank">error will magically disappear</a>.
Do not make any other changes to the file.

If you came here after you tried to fix the syntax error and made it worse, simply
look closely at every line of <a href="dj4e_tutpaw/file-manage-py-good.png" target="_blank">the correct file</a>
and fix your file.  Usually the problem is indentation, a line has been deleted or something was moved around.

And if you are reading this after you made a mistake and edited your `manage.py` - here
is a fresh copy of the file you can use:

Fresh copy of [manage.py](dj4e_install/manage.py.txt) (link)

If you see a `SyntaxError` in 'manage.py' in the shell
------------------------------------------------------

If the `check` identifies errors, do not go on to the rest of the assignment 
once you can run `check` and there are no errors.  If you see this error:

    python manage.py check
      File "manage.py", line 17
        ) from exc
             ^
    SyntaxError: invalid syntax

Do *not* edit your `manage.py` file - the problem is never in that file.

There are several possible reasons for this:

* Check your virtual environment (above)
* Make sure you have not edited your `manage.py` (above)

Don't use `runserver` on PythonAnywhere
---------------------------------------

Just as a note, you *never* run the `runserver` command on PythonAnywhere.  Often the
tutorials have you make a bunch of changes to yout files and then tell you to do:

    python manage.py runserver

This functionality is replaced by the "reload" button on your Web tab.  So
if you are reading any Django instructions that say to do a `runserver`, instead do a

    python manage.py check

And then reload the application in the PythonAnywhere Web tab.  There is a short cut
to relead your application on the PythonAnywhere file editor.  It is a little icon 
in the upper right <img src="dj4e_tutpaw/file-reload.png"/> of the editor that
reloads the application.  Usually you save the file, 
press reload and then check if your application worked.

Don't Use `localhost` URLs on PythonAnywhere
--------------------------------------------

Usually, right after the tutorial tells you to `python manage.py runserver` it
tells you to navigate to a url that looks like:

    http://127.0.0.1:8000/

Once you have reloaded your application you need to go to the URL that PythonAnywhere
has assigned to your application.

    http://(your-account).pythonanywhere.com/
    http://drchuck.pythonanywhere.com/

If the tutorial tells you to go to a URL like

    http://127.0.0.1:8000/polls

Add the `polls` to your URL.

    http://drchuck.pythonanywhere.com/polls

You do not need to add `django_projects` or `mysite` to your URL - this is all captured
in the settings under the 'Web' tab in the PythonAnywhere user interface.

Do you have two mysite folders?
-------------------------------

For some reason students who finish this assignment often end up making their `mysite` folder twice.
They end up with a folder in their home directory and in their `django_projects` subfolder.

<center><img src="dj4e_install/install_cleanup.png" alt="An image showing a mysite folder in django_projects and in the home directory with instructions to remove the one in the home directory" style="border: 1px black solid; width:80%;"></center>

It is a good idea to remove the extra folder in your home directory after making sure that the right
code is in your `django_projects/mysite` folder.   It is really frustrating to have two folders and
do a bunch of work in one of the folders that does not actually affect your running application.

So you might as well clean this up right away if you see it.

How and when you exit the Django shell
--------------------------------------

In tutorial 2, you edit `models.py` and run the Django Shell, then you edit
the `models.py` file again and then run the shell again. What the tutorial does
not mention is the need to exit and restart the shell any time you change
`models.py`.  The tutorial tells you to run the shell again but it does not
tell you to exit the existing shell first - so you might see an error like this:

    (.ve52) 17:16 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some django shell stuff

    >>> python manage.py shell
    File "<console>", line 1
        python manage.py shell
            ^
    SyntaxError: invalid syntax
    >>> 

The correct way is to exit the shell and restart it.

    (.ve52) 17:20 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some django shell stuff

    >>> quit()
    (.ve52) 17:20 ~/django_projects/mysite (master)$ 

Then you edit your `models.py` and *re-start* the Django shell from the
`bash` console:

    (.ve52) 17:24 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some more django shell stuff

    >>> quit()
    (.ve52) 17:20 ~/django_projects/mysite (master)$ 

After a while you will understand that you need to be in `bash` (dollar sign prompt)
to run bash commands and be in the Django shell (>>> prompt) to run Django commands.



