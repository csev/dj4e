Loading a Data Model
====================

In this assignment you will develop a data model from a file of un-normalized data and
then build a script to load data in to that model.  Thie data is a simplified extraction
of the <a href="https://whc.unesco.org/en/list/" tatget="_blank">UNESCO World Heritage Sites</a> registry.
The un-normalized data is provided as both a spreadsheet and a CSF file:

<a href="dj4e_model/whc-sites-2018-small.csv" target="_blank">CSV Version</a>

<a href="dj4e_model/whc-sites-2018-small.xls" target="_blank">XLS Version</a>

The columns in the data are as follows:

    name,description,justification,year,longitude,latitude,
    area_hectares,category,states,region,iso

Getting Started
---------------

Make a new application under your `django_projects` called `unesco`.  This
application will not have any UI of its own - but you will be using the admin
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
  
    class Site(models.Model):
        name = models.CharField(max_length=128)
        iso = models.ForeignKey(Iso_code, on_delete=models.CASCADE)

    class Iso(models.Model) :
        name = models.CharField(max_length=128)

    ...

All of the columns from the CSV data must be represented somewhere in the
data model.

Once you have your model build, wun `makemigrations` and `migrate` to create
the database.

Reading CSV Files
-----------------

The next step is to build a Python script to read the CSV file:


<a href="dj4e_model/whc-sites-2018-small.csv" target="_blank">CSV Version</a>

and load it into your database, and then use the administration user interface
to verify that the data is properly loaded.   Here is a bit of sample code that
can easily read the CSV file in Python:

    import csv

    fh = open('whc-sites-2018-small.csv')
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
to a Person stored in the databas ethat can be used to fullfill a foreign key
later in the code.

    m = Membership(role=r,person=p, course=c)
    m.save()

The line to make the `Membership` row is the last thing that is done so all the 
foreign key connections can be made.

You will note that the code empties the three tables out every time and freshly reloads
all the data so the process can be run over and over.

Running the Script
------------------

Place the CSV file in the `unesco` folder along with your `load.py` file and then run
the script needs to be run in the Django shell from the project folder (i.e.
where the `manage.py` file resides):

    cd ~/django_projects
    python3 manage.py shell < unesco/load.py

It needs to be run this way so that lines like:

    from many.models import Person, Course, Membership

work properly.

