Skeleton Web Site
=================

This is our PythonAnywhere variant of the next step of the Mozilla Developer Network
tutorial:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/skeleton_website

Go into a PYAW shell and create a new application:

    cd ~/django_projects
    django-admin startproject locallibrary

    cd ~/django_projects/locallibrary
    python3 manage.py startapp catalog

At this point, you might want to push your newly added files to github so as you make changes,
you can see what files you have changed and what the differences are.

    cd ~/django_projects/locallibrary
    git status
    git add .
    git commit -a
    git push

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

Edit the file `locallibrary/urls.py` and append the following lines to the file:

    # Use include() to add paths from the catalog application
    from django.urls import include
    from django.urls import path
    urlpatterns += [
        path('catalog/', include('catalog.urls')),
    ]

    #Add URL maps to redirect the base URL to our application
    from django.views.generic import RedirectView
    urlpatterns += [
        path('', RedirectView.as_view(url='/catalog/', permanent=True)),
    ]

Edit the file `catalog/urls.py` and put the following lines in the file:
    
    from django.urls import path
    from . import views
    urlpatterns = [
    ]


    python3 manage.py makemigrations
    python3 manage.py migrate

Web Tab

    Source code: /home/mdntutorial/django_projects/locallibrary
    Working directory: /home/mdntutorial/django_projects/locallibrary

Edit and change 'mytestsite' to 'locallibrary' (Twice)

    WSGI configuration file:/var/www/mdntutorial_pythonanywhere_com_wsgi.py

