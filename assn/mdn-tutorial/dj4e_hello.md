DIY: Hello World
================

At this point in the course we are going to start over and build a series of
applications in a Django project.  To review terminology, in the work that you
have done so far:

* `django_projects` is a folder and/or a github repository that contains multiple
Django projects

* `locallibrary` is a *project* in that folder that can contain many applications.  The
project configuration is in the folder `localllibrary/locallibrary`.

* `catalog` is an *application* within the `locallibrary` project that has models,
views, templates, admin configurations, etc.

We first need to make a new project within our `django_projects` folder.   It is time
to finish and then stop working on the `locallibrary` project.
Just keep it working so you can refer back to it as you build new applications.

Making a New Project and Application
-------------------------------------

Start a shell with virtual environment (if needed) and go into your `django_projects` folder
and start a new project and an application:

    workon django2                  # as needed
    cd ~/django_projects
    django-admin startproject dj4e

    cd ~/django_projects/dj4e
    python3 manage.py startapp home

Starting Django in the new project (local computer)
---------------------------------------------------

Only use this if yu are running on your local compuer.  Skip to the
next set of instructions if you are on PythonAnywhere.

    cd ~/django_projects/dj4e
    python3 manage.py runserver

In general as you make changes to the files below, runserver will monitor
for file changes and restart itself although sometimes you do want to abort
runserver and restart it manually to make sure it sees every new change.

Switching to the new project (on PythonAnywhere)
------------------------------------------------

Under your Web tab, Set the following:

    Source Code:   /home/drchuck/django_projects/dj4e
    Working Directory:   /home/drchuck/django_projects/dj4e

The virtual environment should be pointing to your `django2` virtual environment:

    /home/drchuck/.virtualenvs/django2

Replace `drchuck` above with your PythonAnywhere account name.

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

Of course once you make these changes and other chenges below,
you need to Reload your application.

Files to Edit/Create
--------------------

These are the steps to build your "Hello World" application.

* Make folders `django_projects/dj4e/home/templates` and `django_projects/dj4e/home/templates/home/`

* Create `django_projects/dj4e/home/templates/home/hello.html` and put in some text that says "Hello World ... " and
some additional text about cats and/or any text or meta tag
that the autograder is asking for.

* Create the `django_projects/dj4e/home/urls.py` file to add a path that routes the '' path to a direct template view
pointing at a file `django_projects/dj4e/home/templates/home/hello.html` making sure to import `TemplateView` in the top
of the file:

        from django.urls import path
        from django.views.generic import TemplateView

        app_name='home'
        urlpatterns = [
            path('', TemplateView.as_view(template_name='home/hello.html'), name='home'),
        ]   

* Edit the `django_projects/dj4e/dj4e/settings.py`, make sure `DEBUG` is set to True, fix `ALLOWED_HOSTS` and add the home 
application to `INSTALLED_APPS`:

        DEBUG = True                   # Make sure we see tracebacks in the UI

        ALLOWED_HOSTS = [ '*' ]        # Allow access from anywhere

        INSTALLED_APPS = [
            ...
            'home.apps.HomeConfig',    #  Add this
        ]


* Edit `django_projects/dj4e/dj4e/urls.py` to include the `django_projects/dj4e/home/urls.py` file.  Do *not* add any redirect
route like we used in the locallibrary / catalog application.  It should look like the following

        from django.contrib import admin
        from django.urls import path, include

        urlpatterns = [
            path('', include('home.urls')),
            path('admin/', admin.site.urls),
        ]

