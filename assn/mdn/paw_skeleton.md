Starting the MDN Tutorial
=========================

__You should not do this assignment until you are completely finished with all
of your Ads assignments.__

This assignment will switch your
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
account to a brand new <b>project</b>.  We won't delete your <b>mysite</b>
project - we will make a new project and point your PythonAnywhere Web application at
this new project - so your previous project will disapper from the web - but
still be there if you wanted to switch back.

__NOTE:__ If you find a complete solution to this assignment somewhere out there, do
*not* use it for these assignments.  The autograders expect you to do these assignments
*one at a time* and pass each autograder before going on to the next autograder.  If you find
some sample code outside of the actual tutorial pages - it is fine to look at it to check your
work, but you will get youself in trouble if you use it as a short-cut.

If you get completely confused, you can quite easily start over from the beginning.  See
the instructions at the bottom of this page to start over.

Checking Your Installation
--------------------------

We assume that you have a Django virtual environment all set up from your
previous assignments.  Start a shell and type:

    python -m django --version

It should put out a version like `4.2.7`

We will start the MDN tutorial at step 3.  You can read the first two steps -
but you can start at the "Skeleton Website" step.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/skeleton_website

*Note:* If you are submitting these assignments to the autograder, make sure you finish
grading of one assignment before starting on the next assignment.  The autograder deducts
points for haing *too many* features implemented.

Since you already have a `dango_projects` folder your first step in the tutorial does
not require a `mkdir` command - instead:

    cd ~/django_projects
    django-admin startproject locallibrary
    cd locallibrary

Edit the file `locallibrary/settings.py` and make the following changes:

    DEBUG = True                        # Do not change to False

    ALLOWED_HOSTS = ['*']               # Change

The tutorial does not tell you how to set the `STATIC_ROOT` value in your `settings.py`
so that you can serve static files.  Go find the `STATIC_URL` line in `settings.py` 
and add the `STATIC_ROOT` line below it.

    STATIC_URL = '/static/'

    STATIC_ROOT = os.path.join(BASE_DIR, 'catalog/static')  # New line

In order use the `os` library, you need to add an import to the top of your `settings.py`:

     import os


Continue with the Tutorial
--------------------------

Then continue with the tutorial at the step *Creating the catalog application*:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/skeleton_website#creating_the_catalog_application

starting with the command:

    cd ~/django_projects/locallibrary
    python manage.py startapp catalog

When you get to get to the instructions in the tutorual where you are
editing `locallibrary/urls.py`, *do not add* `permanent=True`
if the tutorial tells you to do so - just leave it out. 

Don't do this:

        path('', RedirectView.as_view(url='catalog/', permanent=True)),

Do this instead:

        path('', RedirectView.as_view(url='catalog/')),

At some point the instructions tell you to run

	python manage.py makemigrations

If you get an error - do not proceed any further in the tutorial until the `makemigrations`
works.  A `makemigrations` error might look like

    File "<frozen importlib._bootstrap_external>", line 846, in exec_module
    File "<frozen importlib._bootstrap_external>", line 983, in get_code
    File "<frozen importlib._bootstrap_external>", line 913, in source_to_code
    File "<frozen importlib._bootstrap>", line 228, in _call_with_frames_removed
    File "/home/mdntutorial/django_projects/locallibrary/locallibrary/urls.py", line 35
      ]
    ^
    SyntaxError: closing parenthesis ']' does not match opening parenthesis '(' on line 34

Figure out what is wrong and fix it, and re-run `makemigrations` until it succeeds and then
proceed with the tutorial.

Continue with the tutorial until it tells you to `python manage.py runserver` - instead do

	python manage.py check

and keep running `check` until there are no errors.

Remember that on PythonAnywhere, we __never__ do a `runserver` but instead use the Web
tab to point to and start our Django application.

Also we never navigate to `http://localhost:8000` - our web server is on PythonAnywhere and the next
section reconfigures your PythonAnywhere account to server the new `locallibrary` project.

Switching Your Web Application to a New Project
-----------------------------------------------

We need to go into the `Web` tab in PythonAnywhere and *switch* which project your
account is serving at your application URL.

Scroll down to the *Code:* section and make the following settings (replace 'drchuck'
with your PYAW account):

    Source code: /home/drchuck/django_projects/locallibrary
    Working directory: /home/drchuck/django_projects/locallibrary

**Important:** Edit the *WGSI Configuration File* and change `mysite` to `locallibrary` in two places
and save it.

The virtual environment should already be set up and does not need to change.

    Virtualenv: /home/drchuck/.virtualenvs/django42

Change `drchuck` above to your PythonAnywhere account name.

Then `Reload` your web application and visit its url to make sure you get the expected output.

    http://drchuck.pythonanywhere.com/catalog/

When you visit the page,
you *should* get an error, 'Page not found(404)'
(<a href="paw_skeleton/webapp_final.png" target="_blank">Sample Image</a>).
We are stopping this tutorial when the web site is still incomplete so that is normal.

You can ignore the instructions about putting everything in a github repo - it is not requried for this assignment.

Common Problems and How to Fix Them
-----------------------------------

If you received an "Error not found" page that does not look like the above image,
check to make sure that you have `DEBUG = True` in your `settings.py` file.  If you
set `DEBUG` to `False`, it will make it far more difficult to track down errors in
your code.  Setting it to `True` means that error pages give far more detail.

If you reload your web application and get the "Something went wrong :("
page when you access your web application, check on the "error.log" link
and scroll to the very bottom to see why your application will not start.

Starting Over
-------------

If you pasted in too much stuff or deleted a large amount of text, and want to start over
it is quite easy.  We just rename the `locallibrary` folder:

	cd ~/django_projects
    mv locallibrary broken1

and then go to the top of this file and do everything over starting with `startproject`.
