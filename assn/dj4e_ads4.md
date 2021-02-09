Classified Ad Web Site - Milestone 4
====================================

In this assignment, you will expand your classified ads web site to add search and tags 
functionality equivalent to:

https://chucklist.dj4e.com/m4

Adding Search
-------------

The `well` sample application contains code you can adapt to implement the code to search
the title and text:

https://samples.dj4e.com/well/

To avoid getting too much broken at one time - it is probably a good idea to make search work
and then further evolve your code to support tags.

Adding Support for Tags
-----------------------

The `tagme` application adds a `tags` field to the model and and adds support for tags
to the user interface and search code.

https://samples.dj4e.com/tagme/

You should also review the documentation for the `django-taggit` library at:

https://django-taggit.readthedocs.io/en/latest/

You might find the easiest path is to use the `taggit` documentation to make your changes,
looking at the `tagme` code to verify what you are doing.

Manual Testing
--------------

It is always a good idea to manually test your application before submitting it for grading.  Here
are a set of manual test steps:

* Log in to your application and create several ads add a tag to one of the ads.  Make sure that
you put unique words that are only in the title, text, and tag so you can verify that you can search
for a word in any of the three places.

* Go into the detail page for an ad and verify that it shows you the title, text, and tag(s) that you entered

* Make sure to edit an ad and verify that you are able to change the title, text, and tags.

* Search for some text that is only in a title and verify that the correct ads come back.

* Search for some text that is only in a body and verify that the correct ads come back.

* Search for some text that is only in a tag and verify that the correct ads come back.

* Note the "?search=" in the location bar in your browser while you are doing searched

* Clear the search and see all of the results and verify there is no "?search=" get parameter


