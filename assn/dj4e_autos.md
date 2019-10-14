Autos CRUD
==========

This assignment is to build a fully working CRUD (Create, Read, Update, and Delete)
application to manage automobiles and their makes (i.e. Ford, Hyundai, Toyota,
Tata, Audi, etc.).

This application will be based on this folder in the samples repo:

https://github.com/csev/dj4e-samples/tree/master/autos

**Do not clone this repository for this assignment**.  You will make a new
project and application in your `django_projects` folder and use this application
as *sample code*.

This application will be similar to:

https://projects.dj4e.com

The login information is as follows:

    Account: dj4e-projects
    Password: dj4e_nn_!

The 'nn' is a 2-digit number that by now, you should be able to easily guess.

Making a New Project
--------------------

Activate any virtual environment you need (if any) and go into your `django_projects` folder
and start a new application in your `dj4e` project (this project already should have the 'hello'
application from a
<a href="dj4e_hello.md">previous assignment</a>).

    workon django2  # as needed
    cd ~/django_projects/dj4e
    python3 manage.py startapp autos

The `autos` project is the first of several applications we will add to the `dj4e` project.

Extending the home (i.e. main) page
-----------------------------------

Since we will build a number of applications in this project, we will use the `home`
application to provide convienent urls to switch between applications.

And you should have a file `dj4e/home/templates/home/hello.html` that has the text for the top-level page.
You can keep the "Hello World" text in the page somewhere.

Add a link to the "/autos" url in `dj4e/home/templates/home/hello.html` and anything else the autograder needs:

    <ul>
    <li><a href="/autos">Autos CRUD</a>
    <ul>

It is a list because we will be adding more applications in future assignments. :)

Building the Autos Application
------------------------------

The essense of this task is to adapt the code from:

https://github.com/csev/dj4e-samples/tree/master/autos

and make it work in your `autos` project.

Here are some tasks:

* Create a template in `home/templates/registration` to support the login view.
(<a href="https://github.com/csev/dj4e-samples/blob/master/home/templates/registration/login.html" target="_blank">Example</a>)

* Copy the file from `dj4e-samples/home/templates/base_bootstrap.html` into
your `dj4e\home\templates` - this will be used in your autos templates and make our HTML look
better by applying the <a href="https://getbootstrap.com/docs/4.0/" target="_blank">Bootstrap</a>
and other styling libraries.

* Edit `dj4e/settings.py` add the autos application to the list of `INSTALLED_APPS`.
You can follow the pattern of the `HomeConfig` line in that file.

* Edit `dj4e/urls.py` and
add the `accounts/` so you can use the Django built in login features.
(<a href="https://docs.djangoproject.com/en/2.2/topics/auth/default/#module-django.contrib.auth.views" target="_blank">Authentication Views</a>).
Also edit `dj4e/urls.py` to route `autos/` urls to `autos/urls.py` file.

        from django.contrib import admin
        from django.urls import path, include
        from django.contrib.auth import views as auth_views

        urlpatterns = [
            path('', include('home.urls')),
            path('admin/', admin.site.urls),
            path('accounts/', include('django.contrib.auth.urls')),  # Keep
            path('autos/', include('autos.urls')),                   # Add
        ]

* Edit the `autos/urls.py` file to add routes for the list, edit, and delete pages for both autos and makes
(<a href="https://github.com/csev/dj4e-samples/blob/master/autos/urls.py" target="_blank">Example</a>)

* Edit the `autos/views.py` file to add views for the list, edit, and delete pages for both autos and makes.
It will make things a lot simpler in the long run if you convert the Make views to
the shorter form like the Auto views.
(<a href="https://github.com/csev/dj4e-samples/blob/master/autos/views.py" target="_blank">Example</a>)

* In your `views.py` file, you should *not* simply use the code for the `Make` views.  You
should rewrite the `Make` views using the same patterns as the `Auto` views.  If you
don't use the generic edit views on your `Make` views you will need to define the
appropriate `MakeForm` in your `forms.py` just like in the sample code.  The
best approach is to build your `views.py` *without* using
a `forms.py - but you can do it either way.

* Edit the `autos/models.py` file to add Auto and Makes models with a foreign
key from Autos to Makes.
(<a href="https://github.com/csev/dj4e-samples/blob/master/autos/urls.py" target="_blank">Example</a>)

<img src="svg/auto_model.svg" alt="A data model diagram showing Autos and Makes" style="display: block; margin-left: auto; margin-right: auto;align: center; max-width: 300px;">

* Run the commands to perform the migrations.

* Edit `autos\admin.py` to add the Auto and Make models to the django administration interface.
(<a href="https://github.com/csev/dj4e-samples/blob/master/autos/admin.py" target="_blank">Example</a>)

* Create a superuser so you can test the admin interface
and log in to the application.

* Create the necessary views in `autos\templates\autos` to support your views.
Note that the sample code uses a sub folder under `templates` to
make sure that templates are not inadvertently shared across multiple applications within a Django project.
(<a href="https://github.com/csev/dj4e-samples/blob/master/autos/templates" target="_blank">Example</a>)

Make sure to check the autograder for additional requirements.

References
----------

* <a href="https://github.com/csev/dj4e-samples/tree/master/autos" target="_blank">Autos CRUD Sample Code</a>

* <a href="dj_install.md" target="_blank">Installing Django Locally</a>

* <a href="../ngrok" target="_blank">Using ngrok to turn in your assignments</a>

* <a href="https://stackoverflow.com/questions/13808020/include-an-svg-hosted-on-github-in-markdown" target="_blank">Embedding SVG in Markdown</a>
