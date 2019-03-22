Loading a Data Model
====================

In this assignment you will develop a data model from a file of un-normalized data and
then build a script to load data in to that model.  Thie data is a simplified extraction
of the <a href="https://whc.unesco.org/en/list/" tatget="_blank">UNESCO World Heritage Sites</a> registry.
The un-normalized data is provided as both a spreadsheet and a CSF file:

<a href="dj4e_model/whc-sites-2018-clean.csv" target="_blank">CSV Version</a>

<a href="dj4e_model/whc-sites-2018-small.xls" target="_blank">XLS Version</a>

The columns in the data are as follows:

    name,description,justification,year,longitude,latitude,
    area_hectares,category,states,region,iso

Getting Started
---------------

Make a new project under your `django_projects` called `loader`.  Then 
make an application called `unesco`.  This application will not have any
 UI of its own - but you will be using the admin
UI so you will need a super user.

Design a Data Model
-------------------

You are to design a database model that represents this flat data across
multiple tables using "third-normal form" - which basically means that
columns that have vertical duplication, such as region:

    category    states                 region                      iso

    Cultural    Afghanistan            Asia and the Pacific        af
    Cultural    Afghanistan            Asia and the Pacific        af
    Cultural    Albania                Europe and North America    al
    Cultural    Albania                Europe and North America    al
    Cultural    Algeria                Arab States                 dz
    Mixed       Algeria                Arab States                 dz
    Cultural    Algeria                Arab States                 dz
    Cultural    Algeria                Arab States                 dz

You will make a DJango model that describes the tables and foreign keys
sufficient to represent this data efficiently with no vertical duplication.
Numbers and dates do not have to have their own tables.

Name the core model `Site`, use singular names for all of the table/model
names.  Use the exact name of the column for the model field names and
foreign key names.  Here is a subset of the `models.py`:

    from django.db import models

    class Category(models.Model) :
        name = models.CharField(max_length=128)

        def __str__(self) :
            return self.name

    ...

    class Site(models.Model):
        name = models.CharField(max_length=128)
        year = models.IntegerField(null=True)
        category = models.ForeignKey(Category, on_delete=models.CASCADE)

        ....

        def __str__(self) :
            return self.name


All of the columns from the CSV data must be represented somewhere in the
data model.

Also add the models to `admin.py`:

    from django.contrib import admin

    # Register your models here.

    from unesco.models import Site, Category, ...

    admin.site.register(Site)
    admin.site.register(Category)
    ...

Once you have your model build, run `makemigrations` and `migrate` to create
the database.

Reading CSV Files
-----------------

The next step is to build a Python script to read the CSV file:


<a href="dj4e_model/whc-sites-2018-clean.csv" target="_blank">CSV Version</a>

and load it into your database, and then use the administration user interface
to verify that the data is properly loaded.   Here is a bit of sample code that
can easily read the CSV file in Python:

    import csv

    fh = open('unesco/whc-sites-2018-clean.csv')
    rows = csv.reader(fh)
    i = 0
    for row in rows:
        if len(row[0]) < 1 : continue
        print(row[0])
        i = i + 1
        if i > 5 : break

Note that the first row of the CSV contains the variable names so it should be
skipped.   This only prionts out the name field for the first five rows of the CSV
file.   You can play with this to explore how the CSV reader sees this file.

Loading Data Into Your Database
-------------------------------

Once you can read through the file, it is time to load it into the database through
the data model.  There is a simple example of how to write such a script in the
DJ4E-Samples respoistory:

<a href="https://github.com/csev/dj4e-samples/tree/master/samples/many" target="_blank">Many-to-Many / Loader</a>

See the file `load.csv` and `load.py` for and exmaple of how you look through a file,
insert model data and make foreign key connections.  A key technique is in this bit of code:

    try:
        p = Person.objects.get(email=row[0])
    except:
        print("Inserting person",row[0])
        p = Person(email=row[0])
        p.save()

This code insures that there is a row in the Person table for the email address
that was just read `row[0]`.  The email address may or may not already be in the table
from a previous row so the first thing that we do is use `get()` to retrieve the row.
If this works, we have loaded the correct `Person` object.  If the `get()` fails,
it triggers the `except` clause that creates a new person and calls `save()` to add it to the
`Person` table.  One way or another, by the end if this segment of code `p` contains a reference
to a Person stored in the database that can be used to fullfill a foreign key
later in the code.

    m = Membership(role=r,person=p, course=c)
    m.save()

The line to make the `Membership` row is the last thing that is done so all the
foreign key connections can be made.

You will note that the code empties the three tables out every time and freshly reloads
all the data so the process can be run over and over.

Dealing with Empty Columns
--------------------------

Your data will be more complex than the sample, You will need to deal with situations
where an integer column like the `year` will be empty.  First, add `null=True` to numeric columns
that can be empty in your `models.py`.   Then before inserting the `Site` record, check the year to
see if it is a valid integer and if it is not a valid integer set it to `None` which will become
`NULL` (or empty) in the data base when inserted:

    try:
        y = int(row[3])
    except:
        y = None

    ...

    site = Site(name=row[0], description=row[1], year=y, ... )
    site.save()

You will need to do this for each of the numeric fields that might be missing.

Running the Script
------------------

Place the CSV file in the `unesco` folder along with your `load.py` file and then run
the script needs to be run in the Django shell from the project folder (i.e.
where the `manage.py` file resides):

    cd ~/django_projects
    python3 manage.py shell < unesco/load.py

It needs to be run this way so that lines like:

    from unesco.models import Site, Iso, ....

work properly.

Checking Your Data By Hand
--------------------------

You might want to hand-check your data by running a few queries on
your data before turning it in to make sure the data makes
it into the right tables:

    $ sqlite3 db.sqlite3
    SQLite version 3.24.0 2018-06-04 14:10:15
    Enter ".help" for usage hints.
    sqlite> SELECT count(id) FROM unesco_states;
    163
    sqlite> SELECT count(id) FROM unesco_site;
    1044
    sqlite> SELECT count(id) FROM unesco_states where name="India";
    1
    sqlite> SELECT count(id) FROM unesco_site WHERE name="Hawaii Volcanoes National Park" AND year=1987 AND area_hectares = 87940.0;
    1
    sqlite> SELECT COUNT(*) FROM unesco_site JOIN unesco_iso ON iso_id=unesco_iso.id WHERE unesco_site.name="Maritime Greenwich" AND unesco_iso.name = "gb";
    1
    sqlite>



