Milestone 4: Data Model
=======================

For this assignment, begin to develop your data model.  Usually your data model
has one central table and then a number of tables that are connected either
directly or indirectly via foreign keys.   

The most common connection is a one-to-many connection.  These one-to-many 
tables are often called a "lookup table" because they replace replicated
string data in a column with an integer foreign key pointing to a row 
in the lookup table.  For example, when you are making a database of automobiles,
the name of the make (i.e. Ford, Chrysler, Tata, Kia, etc.) is stored in a lookup
table.

The next most common connection is a many-to-many connection that uses a 
junction / connection table table.   Classic examples of many-to-many tables are
friend lists or membership in groups.  Blog post comments would be another example
where one-to-many makes the most sense.  Each user can make more than on comment on
a blog post and each blog post can have comments for more than one user.

For this assignment you will build a diagram of your data model and a `models.py`
file.  We are looking for a `models.py` that describes your tables (models) and fields
and captures the foreign key relationships.   It does not have to be ready-for production
yet.   It is more of a draft and so it is nice to make it a little easier to change
if you get comments.

You can use any tool to draw your data model diagram, from a whiteboard and paper to a complete
modeling tool.   At this point focus on something that is easy to edit and change as
other people look at it and give you comments.



