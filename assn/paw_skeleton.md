Skeleton Web Site
=================

This is our PythonAnywhere variant of the next step of the Mozilla Developer Network
tutorial:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/skeleton_website

You can view a
<a href="https://www.youtube.com/watch?v=6_JHiJvXu-I&index=3&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z" target="_blank">video walkthrough</a> of this assignment.

*Note:* If you are submitting these assignments to the autograder, make sure you finish
grading of one assignment before starting on the next assignment.  The autograder deducts
points for hainv *too many* features implemented.

Go to your
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
account and start a bash shell,
go into your virtual environment and create a new application:

    workon django2
    cd ~/django_projects
    django-admin startproject locallibrary

    cd ~/django_projects/locallibrary
    python3 manage.py startapp catalog

Edit the file `locallibrary/settings.py` and make the following changes:

    ALLOWED_HOSTS = ['*']   <-- Change

    INSTALLED_APPS = [
        'django.contrib.admin',
        'django.contrib.auth',
        'django.contrib.contenttypes',
        'django.contrib.sessions',
        'django.contrib.messages',
        'django.contrib.staticfiles',
        'catalog.apps.CatalogConfig',    <--- Add
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
that you add `, permanent=True` to the `path()` statement above.   Do not add
the `permanent` parameter or it will mess things up later.

Create the file `catalog/urls.py` and put the following lines in the file:

    from django.urls import path
    from . import views
    urlpatterns = [
    ]

While it is not essential to this assignment, it is a good idea to run the migrations
at this point in time.  In the PYAW shell:

    cd ~/django_projects/locallibrary

    python3 manage.py makemigrations
    python3 manage.py migrate

On the Web tab
<a href="paw_skeleton/webapp_config.png" target="_blank">Sample image</a>,
update the following values: 

    Source code: /home/dj4e/django_projects/locallibrary
    Working directory: /home/dj4e/django_projects/locallibrary

Change `dj4e` above to your PythonAnywhere account name.

Edit and change 'mytestsite' to 'locallibrary' (Twice) in the 
WGSI configuration file. The virtual environment should already be
set up and does not need to change.

The `Reload` your web application and visit its url to make sure you get the expected output.

    http://mdntutorial.pythonanywhere.com/catalog/

When you visit the page,
you *should* get an error, 'Page not found(404)'
(<a href="paw_skeleton/webapp_final.png" target="_blank">Sample Image</a>).
We are stopping this tutorial when the web site is still incomplete so that is normal.

