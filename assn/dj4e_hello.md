Hello World
===========

At this point in the course we are going to start over and build a series of
applications in a Django project.  To review terminology, in the work that you
have done so far:

* `django_projects` is a folder and/or a github repository that contains multiple
Django projects

* `locallibrary` is a *project* in that folder that can contain many applications.  The
project configuration is in the folder `localllibrary/locallibrary`.

* `catalog` is an *application* within the `locallibrary` project that has models,
views, templates, admin configurations, etc.

At this point you should be doing development
locally and uploading it to PythonAnywhere or just using ngrok to turn it in. If
you don't have a compure that can run Django locally you can continue to use
PythonAnywhere but increasingly examples will be shown unsing local development
patterns.

This application will end up with a similar structure to

https://github.com/csev/dj4e-samples/tree/master/autos

**Do not clone this repository**.  It is just to be used as sample code as you
build your new project and application in your `django_projects` folder.

We first need to make a new project within our `django_projects` folder.   It is time
to stop working on `locallibrary` - just keep it working so you can refer back to it
as you build new applications.

Making a New Project
--------------------

Activate any virtual environment you need (if any) and go into your `django_projects` folder
and start a new project and an application:

    workon django2  # as needed
    cd ~/django_projects
    django-admin startproject dj4e

    cd ~/django_projects/dj4e
    python3 manage.py startapp home

If you have Django Locally
--------------------------

    cd ~/django_projects/dj4e
    python3 manage.py runserver

In general as you make changes to the files below, runserver will monitor
for file changes and restart itself although sometimes you do want to abort
runserver and restart it manually to make sure it sees every new change.

Files to Edit
-------------

These are the steps to build your "Hello World" application.

* Make folders `dj4e/home/templates` and `dj4e/home/templates/home/`

* Create `dj4e/home/templates/main_home.html` and put in some text that says "Hello World ... " and
some additional text about cats and/or any text or meta tag
that the autograder is asking for.

* Edit the `dj4e/home/urls.py` file to add a path that routes the '' path to a direct template view
pointing at a file `dj4e/home/templates/main/home.html`

    path('', TemplateView.as_view(template_name='home/hello.html'), name='home'),

* Edit the `dj4e/dj4e/settings.py`, fix `ALLOWED_HOSTS` and add the home application:

        INSTALLED_APPS = [
            ...
            'home.apps.HomeConfig',    <--- Add
        ]

* Edit `dj4e/dj4e/urls.py` to include the `dj4e/home/urls.py` file.  Do *not* add any redirect
route like we used in the locallibrary / catalog application.  It should look like the following

    from django.contrib import admin
    from django.urls import path, include

    urlpatterns = [
        path('', include('home.urls')),
        path('admin/', admin.site.urls),
    ]


If you are running Django on PythonAnywhere
-------------------------------------------

Under your Web tab, Set the following:

    Source Code:   /home/--your account--/django_projects/dj4e
    Working Directory:   /home/--your account--/django_projects/dj4e

You should leave the virtual environment setting the same - pointing to your `django2`
virtual environment.

Your WGSI Configuration file under the Web tab on PythonAnywhere
should be replaced with this text:

    import os
    import sys

    path = os.path.expanduser('~/django_projects/dj4e')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'dj4e.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

Of course you need to Reload your application as you make changes to the files in this 
Django project.  If you are running locally, the `runserver` process will automatically
restart itself whenever you change a file in the `dj4e` folder.

References
----------

* <a href="https://github.com/csev/dj4e-samples/tree/master/hello" target="_blank">Hello World Sample Code</a>

* <a href="https://github.com/csev/dj4e-samples/tree/master/tmpl" target="_blank">Templates Sample Code</a>

* <a href="dj_install.md" target="_blank">Installing Django Locally</a>

* <a href="../ngrok" target="_blank">Using ngrok to turn in your assignments</a>

