Loading a Data Model
====================

In this assignment you will develop a data model from a file of un-normalized data and
then build a script to load data in to that model.  Thie data is a simplified extraction
of the <a href="https://whc.unesco.org/en/list/" tatget="_blank">UNESCO World Heritage Sites</a> registry.
The un-normalized data is provided as both a spreadsheet and a CSF file:

<a href="dj4e_model/whc-sites-2018-small.csv" target="_blank">CSV Version</a>

<a href="dj4e_model/whc-sites-2018-small.xls" target="_blank">XLS Version</a>

The columns in are as follows:

    name,description,justification,year,longitude,latitude,
    area_hectares,category,states_name,region,iso_code

You are to design a database model that represents this flat data across
multiple tables using "third-normal form" - which basically means that
columns that have vertical dumplication

Application Specification
-------------------------

The specification for this application is available at the following URL:

<a href="../tools/dj4e/02spec.php?assn=02cats.php" target="_blank">Cats Database CRUD</a>

Make sure to check the autograder for additional requirements.

