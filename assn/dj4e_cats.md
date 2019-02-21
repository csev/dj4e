Cats CRUD
=========

This assignment is to build a fully working CRUD (Create, Read, Update, and Delete)
application to manage cats and their breeds (i.e. Tabby, Persian, Main Coon,
Siamese, Manx, etc.).

This application will be similar to:

https://projects.dj4e.com/cats

The login information is as follows:

    Account: dj4e-projects
    Password: dj4e_nn_!

The 'nn' is a 2-digit number that by now, you should be able to easily guess.

This application will be in effect a clone of of your previous assigment but different. 
You will use your autos assignment as a prototype for this cats assignment.

Making a New Project
--------------------

Activate any virtual environment you need (if any) and go into your `django_projects` folder
and start a new application in your `dj4e` project (this project already should have 'hello'
and 'autos' applications from previous assignments):

    workon django2  # as needed
    cd ~/django_projects/dj4e
    python3 manage.py startapp cats

Extending the home (i.e. main) page
-----------------------------------

Add a link to `home/templates/main.html` that has the text for the top-level page.

    <ul>
    <li><a href="/autos">Autos CRUD</a>
    <li><a href="/cats">Cats CRUD</a>
    <ul>

Application Specification
-------------------------

The spec for this application is available at the following URL:

<a href="../tools/dj4e/02spec.php?assn=02cats.php" target="_blank">Cats Database CRUD</a>

Building the Cats Application
-----------------------------

Here are some tasks:

* Edit `dj4e/urls.py` to route `cats/` urls to `cats/urls.py` file.  Comment out the route of autos to keep
things simple.

        urlpatterns = [
            path('', include('home.urls')),
            path('admin/', admin.site.urls),
            path('accounts/', include('django.contrib.auth.urls')),
            # path('autos/', include('autos.urls')),
            path('cats/', include('cats.urls')),
        ]
    
* Edit the `cats/urls.py` file to add routes for the list, edit, and delete pages for both cats and breeds.

* Edit the `cats/views.py` file to add views for the list, edit, and delete pages for both cats and breeds.

* Create the necessary templates in `home\templates\registration` to support the login / log out views.  

* Edit the `cats/models.py` file to add Cat and Breed models as per the specification with a foreign key from Cat to Breed.

* Run the commands to perform the migrations.  

* Edit `cats\admin.py` to add the Cat and Breed models to the django administration interface.

* Create a superuser so you can test the admin interface
and log in to the application.

* Create the necessary views in `cats\templates\cats` to support your views.
Note that the sample code uses a sub folder under `templates` to
make sure that templates are not inadvertently shared across multiple applications within a Django project.

Make sure to check the autograder for additional requirements.

