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

    cd ~/django_projects/locallibrary

Make sure that you have properly edited the `locallibrary/settings.py` file
to register your catalog
application within the locallibrary project by adding `CatalogConfig` line.

Edit the file `catalog/models.py` and add the `Genre`, `Book`, `BookInstance`, and `Author` models
as described in the tutorial.  Once you have added models models, run the migrations
from `~/django_projects/locallibrary`

    cd ~/django_projects/locallibrary
    python manage.py makemigrations

    Migrations for 'catalog':
      catalog/migrations/0001_initial.py
        - Create model Author
        - Create model Book
        - Create model BookInstance
        - Create model Genre
        - Add field genre to book

If the `makemigrations` encounters errors, stop, fix the error and re-run `makemigrations` until
it is successfull.

    python manage.py migrate

Then add the `Language` model to your `models.py` as discussed in the "Challenge" section
at the end of the tutorial.  You can skim and think through the "design a model" part of the
tutorial - but for the sake of time, here is a Language model you can use:

    class Language(models.Model):
        """Model representing a Language (e.g. English, French, Japanese, etc.)"""
        name = models.CharField(max_length=200,
                            unique=True,
                            help_text="Enter the book's natural language (e.g. English, French, Japanese etc.)")

        def get_absolute_url(self):
            """Returns the url to access a particular language instance."""
            return reverse('language-detail', args=[str(self.id)])

        def __str__(self):
            """String for representing the Model object (in Admin site etc.)"""
            return self.name

    class Meta:
            constraints = [
                UniqueConstraint(
                    Lower('name'),
                    name='language_name_case_insensitive_unique',
                    violation_error_message = "Language already exists (case insensitive match)"
                ),
            ]

Since we have changed the `models.py` by adding the `Language` model, we need to run the migrations again:

    cd ~/django_projects/locallibrary
    python manage.py makemigrations

    Migrations for 'catalog':
      catalog/migrations/0002_language.py
        + Create model Language
          catalog/migrations/0002_language_and_more.py
            - Create model Language

You can repeat the process of editing the `models.py` file and re-running the makemigrations until you get them
right and then run `migrate` to actually create/update the tables in the database.

    python manage.py migrate

If you are using the autograder for this assignment, you will need to upload the
`~/django_projects/locallibrary/db.sqlite3` file.  If you are using PythonAnywhere you can use the Files tab
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

