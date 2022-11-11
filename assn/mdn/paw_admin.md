Django Admin Site
=================

Our next step is to explore the LocalLibrary administration web site that
allows us to create, read, update, and delete data in our database.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Admin_site

If you are submitting this assignment to the DJ4E autograder for this assignment,
it would be a good idea to check the autograder for specific instructions that
the autograder requires for this assignment.

Complete the following sections of the Admin tutorial:

* Edit `~/django_projects/locallibrary/catalog/admin.py` and register the four models
* Create a superuser (The autograder will ask you to make a second superuser)
Note that it is OK to have more than one super user and you can log in as any
super user and edit or delete the other superuser accounts.
* Reload your application under the `Web` tab in
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
* Log in to the admin site.  Insead of using http://localhost:8000/admin, simply add `/admin` to the end of
your PythonAnywhere site (i.e. like
<a href="http://mdntutorial.pythonanywhere.com/admin" target="_blank">http://mdntutorial.pythonanywhere.com/admin</a>.
* Create some books (The autograder will ask you to create one specific book and author)
* Continue into the Advanced Configuration
* Register a Model Admin class
* Configure the list views - Note that you need to edit `~/django_projects/locallibrary/catalog/models.py` at one point
* Add the list filter
* Organize the detail view layout
* Enable inline editing of associated records

Just as a reminder, when you are running on PythonAnywhere you **do not need** to do a:

    python manage.py runserver  # Don't do this on PythonAnywhere

Everytime you make a configuration change.  But if you are running on
PythonAnywhere and make a configuration change you **do** need to
go into the `Web` tab and `Reload` the web server to re-read your updated configuration.  There is
not harm in reloading your web on PythonAnywhere application too often.  So when in doubt, reload :)

Making a Fresh Database
-----------------------
If you want to experiment a bit and you want to wipe out your database and start over, do the following:

    cd ~/django_projects/locallibrary
    rm db.sqlite3
    python manage.py migrate

This will wipe out all of your tables and the data in those tables and create fresh and empty tables.
Note that this process will also wipe out your superuser accounts and all data you have entered.

The `db.sqlite3` file is a normal file - you can back it up and/or copy over it - just make sure to `Reload`
your web application when you change your database.

Some Common Mistakes
--------------------

Sometimes students set `Debug=False` in the `locallibrary/settings.py` file.
This should be set to `True`.

Sometimes when commands like

    python manage.py migrate

run without error, but the web application will not start on
PythonAnywhere, check to make sure your virtual environment is
properly set under the Web tab.  If you did the installation
properly and created a `django4`
virtual environment, the virtual environment should be set to:

    /home/drchuck/.virtualenvs/django4

Replacing "drchuck" with your PythonAnywhere account name.

