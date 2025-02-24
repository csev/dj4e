Batch Loading a Data Model
==========================

Sometimes, we need to build a Django model and pre-load it with data from a file or other
source.

In this assignment, we are going to fill your `polls` model from a CSV file.  Each poll question
has a question and one or more choices.  The sample data looks as follows:

    Answer to the Ultimate Question,123,42,86
    What is your name,lancelot,arthur
    What is your quest,Seek the grail,Learn Django,Understand Python OO
    What is your favourite color,blue,yellow,red
    What is the airspeed of an unladen swallow,20,12,33

You can download the data for this assignment at <a href="dj4e_batch.csv" target="_blank">dj4e_batch.csv</a>.

You can parse the rows with the `csv` library or `split(',')`.  If there are less than two items in the resulting list,
you can ignore the line.  If there are more than two items the `[0]` item is the question
and the `[1:]` items are the choices.

Designing a Database Model
--------------------------

Since we are reusing the `polls` data model with `Choices` and `Questions` we are already past
the "design" phase.  But in order to better understand the `polls` data model,
as an exercise, you should look at your `models.py` and draw the model using
<a href="https://en.wikipedia.org/wiki/Entity%E2%80%93relationship_model" target="_blank">
Crow's-Foot Notation</a>.
You can use paper, or a layout tool - one way or another your
diagram should have two boxes and one lines - and the line should be properly labelled
as a "many" or a "one" end.

Getting Started
---------------

Also make a folder called `scripts` and add an `__init__.py` file to it.  The `__init__.py` file
is needed in order to store Python objects in the `scripts` folder.

    cd ~/django_projects/mysite
    mkdir scripts
    touch scripts/__init__.py

Add `django_extensions` to your `INSTALLED_APPS` in `mysite/mysite/settings.py`:

    INSTALLED_APPS = [
        'django.contrib.admin',
        'django.contrib.auth',
        'django.contrib.contenttypes',
    ...
        'django_extensions', # Add
    ]

At this point you should run:

    python manage.py check

To make sure that your Django environment is configured properly.

Copying the data file into your application
-------------------------------------------

You need to copy the CSV file into the `scripts` folder.  If the `wget` command is available
you can use it to download the file:

    cd ~/django_projects/mysite/scripts
    wget https://www.dj4e.com/assn/dj4e_batch.csv

Creating the batch script
-------------------------

Start with putting this code into your `scripts` folder `(~/django_projects/mysite/scripts)`
as the file `polls_load.py`.

    import csv  # https://docs.python.org/3/library/csv.html

    import datetime
    from django.utils import timezone

    from polls.models import Question, Choice

    def run():
        print("=== Polls Loader")

        Choice.objects.all().delete()
        Question.objects.all().delete()
        print("=== Objects deleted")

        fhand = open('scripts/dj4e_batch.csv')
        reader = csv.reader(fhand)
        next(reader)  # Advance past the header

        for row in reader:
            print(row)

            # Replace these comments to

            # Make a new Question and save it

            # Loop through the choice strings in row[1:] and add each choice,
            # connect it to the question and save it

            # Read and review the code for creating and saving Question objects
            # in Tutorial 2

        print("=== Load Complete")

Because the question and choices are all one line, you will need some different parsing and
data model code than for example `cats_load.py` from the course samples and lectures.

The pattern for creating an object, saving it, and making child objects and connecting them to a parent
object is well covered in Tutorial 2:

<a href="https://docs.djangoproject.com/en/4.2/intro/tutorial02/" target="_new">https://docs.djangoproject.com/en/4.2/intro/tutorial02/</a>

Running the Script
------------------

You run the script from the project folder (i.e.  where the `manage.py` file resides):

    cd ~/django_projects/mysite
    python manage.py runscript polls_load

It needs to be run this way so that lines like:

    from polls.models import Question, Choice

work properly.

Note that you should be able to run the script over and over because of the first thing the script
does is delete all the existing questions and choices.

Once your script runs, you should verify that the questions and choices made it into your database
using either your `/polls` application or the `/admin` feature of your django application.

Possible Errors
---------------

If you get an error message about `No module named django_extensions` you forgot
to type `workon django42`  to activate your virtual environment:

    $ python manage.py check
    Traceback (most recent call last):
      File "/home/drchuck/django_projects/mysite/manage.py", line 21, in <module>
        main()

            ... blah blah blah traceback messages ....

      File "<frozen importlib._bootstrap>", line 984, in _find_and_load_unlocked
    ModuleNotFoundError: No module named 'django_extensions'

    $ workon django42
    (django42) $ python manage.py check
    System check identified no issues (0 silenced).
    

Checking Your Data By Hand
--------------------------

You can also hand-check your data by running a few queries on
your data before turning it in to make sure the data makes
it into the right tables:

    $ sqlite3 db.sqlite3
    SQLite version 3.24.0 2018-06-04 14:10:15
    Enter ".help" for usage hints.

    sqlite> SELECT COUNT(*) FROM polls_question;
    26
    sqlite> SELECT COUNT(*) FROM polls_choice;
    104
    sqlite> SELECT COUNT(*) FROM polls_question WHERE question_text LIKE '%what%';
    15
    sqlite> SELECT COUNT(*) FROM polls_question JOIN polls_choice ON polls_question.id = polls_choice.question_id
       WHERE polls_question.question_text = 'What is your quest';
    3
    sqlite> .quit
    $

Upload to the Autograder
------------------------

When the data passes your manual tests, you can download `db.sqlite3` from PythonAnywhere
and then upload it to the autograder.

<center><img src="dj4e_batch/pyaw_download.png" alt="An image pointing to the download icon on the Files tab on PythonAnywhere" style="width: 50%; border: 1px black solid;"></center>


Resetting Your Database
-----------------------

If the autograder complains that your file is somehow too big,
or you have been changing your `models.py` and your `makemigrations`
is asking you how to convert existing columns,
or you just
want to start with a fresh database, you can run the following commands.

    $ cd ~/django_projects/mysite
    $ rm db.sqlite3
    $ rm */migrations/0*
    $ python manage.py makemigrations
    $ python manage.py migrate
    $ python manage.py runscript polls_load

Make sure you run these commands in the correct folder
(i.e. `~/django_projects/mysite`).  You can run this process in any Django
project but your database is completely reset (i.e. admin and login accounts
are deleted as well).  This also completely rebuilds your migrations
from your latest `models.py` file(s).

