Autos CRUD
==========

This assignment is to build a fully working CRUD (Create, Read, Update, and Delete)
application to manage automobiles and their makes (i.e. Ford, Hundai, Toyota,
Tata, Audi, etc.).

This application will be similar to:

https://projects.dj4e.com/crud

This application will be in effect a clone of:

https://github.com/csev/dj4e-samples/tree/master/dj4ecrud

We will make this in a new project.

Making a New Project
--------------------

Activate any virtual environment you need (if any) and go into your `django_projects` folder
and start a new application in your `dj4e` project:

    workon django2  # as needed
    cd ~/django_projects/dj4e
    python3 manage.py startapp autos

The `autos` project is the first of several applications we will add to the `dj4e` project.

Extending the home application
------------------------------

Since we will build a number of applications in this project, we will use the `home`
application to provide convienent urls to switch between applications.   If you did
not use a template for your home page, it would probably be a good idea to switch
to the template pattern as shown in:

https://github.com/csev/dj4e-samples/blob/master/templates/home/urls.py

Your `home\urls.py` should have a like like this

    path('', TemplateView.as_view(template_name='main.html')),

And you should have a file `home/templates/main.html` that has the text for the top-level page.

Add a link to the "/autos" url in `main.html` and anything else the autograder needs:

    <ul>
    <li><a href="/autos">Autos CRUD</a>
    <ul>

It is a list because we will be adding more applications in future assignments. :)

Building the Autos Application
------------------------------

The essense of this task is to just copy the code from:

https://github.com/csev/dj4e-samples/tree/master/dj4ecrud

and make it work in your `autos` project.

Here are some tasks:

* Edit `dj4e/urls.py` to route `autos/` urls to `autos/urls.py` file.  Also route the `accounts/` url to the 
Django built in login features and add the `admin/` route so you can work with your data.
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/dj4ecrud/urls.py" target="_blank">Example</a>)

* Edit the `autos/urls.py` file to add routes for the list, edit, and delete pages for both autos and makes
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/autos/urls.py" target="_blank">Example</a>)

* Edit the `autos/views.py` file to add views for the list, edit, and delete pages for both autos and makes.
You can ignore the `cleanup` stuff.
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/autos/views.py" target="_blank">Example</a>)

* Create the necessary templates in `home\templates\registration` to support the login / log out views.  
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/home/templates" target="_blank">Example</a>)

* Create the necessary views in `autos\templates\autos` to support your views.
Note that the sample code uses a sub folder under `templates` to
make sure that templates are not inadvertently shared across multiple applications within a Django project.
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/autos/templates" target="_blank">Example</a>)

* Edit the `autos/models.py` file to add Auto and Makes models with a foreign key from Autos to Makes.
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/autos/urls.py" target="_blank">Example</a>)

<img src="svg/auto_model.svg" alt="A data model diagram showing Autos and Makes" style="display: block; margin-left: auto; margin-right: auto;align: center; max-width: 300px;">

* Run the commands to perform the migrations.  

* Edit `autos\admin.py` to add the Auto and Make models to the django administration interface.
(<a href="https://github.com/csev/dj4e-samples/blob/master/dj4ecrud/autos/admin.py" target="_blank">Example</a>)

* Create a superuser so you can test the admin interface
and log in to the application.


Make sure to check the autograder for additional requirements.

References
----------

* <a href="https://github.com/csev/dj4e-samples/tree/master/dj4ecrud" target="_blank">AUtos CRUD Sample Code</a>

* <a href="dj_install.md" target="_blank">Installing Django Locally</a>

* <a href="../ngrok" target="_blank">Using ngrok to turn in your assignments</a>

* <a href="https://stackoverflow.com/questions/13808020/include-an-svg-hosted-on-github-in-markdown" target="_blank">Embedding SCG in Markdown</a>
