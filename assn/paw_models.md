Django Models
=============

Our next step is to add some models to our LocalLibrary application so we can store
data in our database.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Models

You can view a
<a href="https://www.youtube.com/watch?v=2-QFePlm7GA&list=PLlRFEj9H3Oj5e-EH0t3kXrcdygrL9-u-Z&index=5" target="_blank">video walkthrough</a> of this assignment.

Read and understand the tutorial, and when you get to the section titled
<a href="https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Models#Defining_the_LocalLibrary_Models" target="_blank">Defining the LocalLibrary Models</a>, 
go to your
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
account and start a bash shell.

First, lets build the models for the the dj4e-samples if you have not already done so:

    workon django2    # If needed
    cd ~/dj4e-samples
    python3 manage.py makemigrations
    python3 manage.py migrate

Then continue working on the `locallibrary` project:

    workon django2
    cd ~/django_projects/locallibrary

Make sure that you have properly edited the `locallibrary/settings.py` file to register your catalog
application within the locallibrary project by adding `CatalogConfig` line:

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

Edit the file `catalog/models.py` and add the `Genre` model using as described in the tutorial.
You can edit the file with `nano`, `vi`, or the PythonAnywhere web interface.  Once you have added
the model, run the migrations from `~/django_projects/locallibrary`

    python3 manage.py makemigrations
    python3 manage.py migrate

If you are using `git`, you can see what files have been modified / created:

    git status

The git output would be as follows:

    modified:   catalog/models.py
    Untracked files:
        catalog/migrations/0001_initial.py   

The `git` command won't show the `db.sqlite3` file has changed because we have told `git`
not to track the database file in the `.gitignore` file

Lets take a quick look at the contents of the `db.sqlite3` file in your bash shell:

    sqlite3 db.sqlite3 
    SQLite version 3.11.0 2016-02-15 17:29:24
    Enter ".help" for usage hints.
    sqlite> .tables
    auth_group                  catalog_genre
    auth_group_permissions      django_admin_log
    auth_permission             django_content_type
    auth_user                   django_migrations
    auth_user_groups            django_session
    auth_user_user_permissions
    sqlite> .mode column
    sqlite> select * from catalog_genre;
    sqlite> .schema catalog_genre
    CREATE TABLE "catalog_genre" (
        "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT, 
        "name" varchar(200) NOT NULL);
    sqlite> .quit  

You can learn more about the command line mode of `sqlite3` at their web site:

https://www.sqlite.org/cli.html

Continue editing the `catalog/models.py` file and add the Book, BookInstance, and Author models
according to the tutorial.

Also add the `language` field to the correct table as discussed in the "Challenge" section 
at the end of the tutorial.

Once your models.py file is complete, run the migrations again:

    cd ~/django_projects/locallibrary
    python3 manage.py makemigrations
    python3 manage.py migrate

You can repeat the process of editing the `models.py` file and re-running the migrations until you get them
right.

If you are using the autograder for this assignment, you will need to upload the
`db.sqlite3` file.  If you are using PythonAnywhere you can use the Files tab
to download the file to your computer and then upload it to the autograder.

If You Are Keeping Your Projects GitHub
---------------------------------------

At this point, once your models are working, you might want to check it into
github and tag it.

    cd ~/django_projects/locallibrary
    git status
    git add catalog/migrations/*
    git commit -a -m "Models tutorial complete"
    git push

You might also want to tag this version of the code in case you need to come back to it:

    git tag models
    git push origin --tags

