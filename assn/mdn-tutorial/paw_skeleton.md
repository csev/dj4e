Starting the MDN Tutorial
=========================

You should not do this assignment until you are completely finished with all
of your Ads assignments.  This assignment will switch your
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
account to a brand new <b>project</b>.  We won't delete your <b>mysite</b>
project - we will make a new project and point your PythonAnywhere at
this new project.

Checking Your Installation
--------------------------

We assume that you have a Django 3 virtual environment all set up from your
previous assignments.  Start a shell and type:

    workon django3

Of course always make sure to type this command when you start a fresh shell.

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

Continuing with the Tutorial
----------------------------

Edit the file `locallibrary/settings.py` and make the following changes:

    DEBUG = True                        # Do not change to False

    ALLOWED_HOSTS = ['*']               # Change

    INSTALLED_APPS = [
        'django.contrib.admin',
        'django.contrib.auth',
        'django.contrib.contenttypes',
        'django.contrib.sessions',
        'django.contrib.messages',
        'django.contrib.staticfiles',
        'django_extensions',             # Add this line
        'catalog.apps.CatalogConfig',    # Add this line
    ]

Edit the file `locallibrary/urls.py` and update the code to look like the following:

    from django.contrib import admin
    from django.urls import path

    urlpatterns = [
        path('admin/', admin.site.urls),
    ]

    # Use include() to add paths from the catalog application
    from django.urls import include
    urlpatterns += [
        path('catalog/', include('catalog.urls')),
    ]

    #Add URL maps to redirect the base URL to our application
    from django.views.generic import RedirectView
    urlpatterns += [
        path('', RedirectView.as_view(url='/catalog/')),
    ]

Note that if you are following the MDN Tutorial for this step, it will suggest
that you add `, permanent=True` to the `path()` statement above.   Do **not** add
the `permanent` parameter or it will mess things up later.

Create the file `catalog/urls.py` and put the following lines in the file:

    from django.urls import path
    from . import views
    urlpatterns = [
    ]

While it is not essential to this assignment, it is a good idea to run check and migrations
at this point in time.  In the PYAW shell:

    workon django3
    cd ~/django_projects/locallibrary

    python3 manage.py check
    python3 manage.py makemigrations
    python3 manage.py migrate

If you get an error when you type the above `python3` commands that cannot
find the `django_extensions` you either have not set up your virtual environment
properly or did not use the `workon django3` command to switch into your
virtual environment in your shell.  When you are in a shell, you need to
be in the `django3` virtual environment or the `manage.py` commands will fail.

Switching Your Web Application to a New Project
-----------------------------------------------

We need to go into the `Web` tab in PythonAnywhere and *switch* which project your
account is serving at your application URL.

Scroll down to the *Code:* section and make the following settings (replace 'drchuck'
with your PYAW account):

    Source code: /home/drchuck/django_projects/locallibrary
    Working directory: /home/drchuck/django_projects/locallibrary

Edit the *WGSI Configuration File* and change `mysite` to `locallibrary` in two places
and save it.

The virtual environment should already be set up and does not need to change.

    Virtualenv: /home/drchuck/.virtualenvs/django3

Change `drchuck` above to your PythonAnywhere account name.

Then `Reload` your web application and visit its url to make sure you get the expected output.

    http://drchuck.pythonanywhere.com/catalog/

When you visit the page,
you *should* get an error, 'Page not found(404)'
(<a href="paw_skeleton/webapp_final.png" target="_blank">Sample Image</a>).
We are stopping this tutorial when the web site is still incomplete so that is normal.

Common Problems and How to Fix Them
-----------------------------------

If you received an "Error not found" page that does not look like the above image,
check to make sure that you have `DEBUG = True` in your `settings.py` file.  If you
set `DEBUG` to `False`, it will make it far more difficult to track down errors in
your code.  Setting it to `True` means that error pages give far more detail.

If you reload your web application and get the "Something went wrong :("
page when you access your web application, check on the "error.log" link
and scroll to the very bottom to see why your application will not start.

