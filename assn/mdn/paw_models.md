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

Lets go into the locallibrary project:

    workon django3
    cd ~/django_projects/locallibrary

Make sure that you have properly edited the `locallibrary/settings.py` file
to register your catalog
application within the locallibrary project by adding `CatalogConfig` line.

Edit the file `catalog/models.py` and add the `Genre` model using as described in the tutorial.
You can edit the file with `nano`, `vi`, or the PythonAnywhere Files tab interface.  Once you have added
the model, run the migrations from `~/django_projects/locallibrary`

    python3 manage.py makemigrations
    python3 manage.py migrate

Lets take a quick look at the contents of the `db.sqlite3` file in your bash shell:

    $ sqlite3 db.sqlite3 
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

If Your Model/Database Gets Messed Up
-------------------------------------

If you want to clean up your database and start with a
fresh database using the following instructions:

    cd ~/django_projects/locallibrary
    rm */migrations/00*
    rm db.sqlite3
    python manage.py makemigrations
    python manage.py migrate
    python manage.py createsuperuser    # If needed

You need to recreate the superuser because it is stored in the database
and the `rm` command emptied out your database.  You can do
this process any time your database feels like it is messed up.  But
you have to re-enter all your accounts and data.

