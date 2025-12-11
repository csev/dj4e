
Marketplace - Search - Milestone 5
==================================

In this assignment, you will expand your classified ads web site to add search and tags
functionality equivalent to:

https://market.dj4e.com/m5

You can log into this site using
an account: <b>facebook</b> and a password of <b>Marketnn!</b> where "nn" is the
two-digit number of Dr. Chuck's race car or the numeric value for asterisk in the ASCII character set.

*Important*: The number of lines you need to add to your code is *relatively* small.  Take your time
read the sample code carefully - only make changes that you understand.  Wholesale cutting and
pasting sample code will make it almost impossible to complete this assignment.

This is somewhat like the kind of real work you do when you have a working application and want to add a feature
the the application.  First - don't break what you have working.

Taking a Snapshot of Your Previous Assignment using git
-------------------------------------------------------

We want to take a snapshot of your working Milestone 4 code using the `git` version management
tool before we start editing Milestone 5.  Only do this *once and only once* after you have fully completed the previous
milestone and before you start editing your files for this assignment.

If you have not already done so, first set up your github identity using the commands below:

    cd ~/django_projects/market
    git config --global user.email "youremail@example.com"
    git config --global user.name "Your name"

Then run these commands:

    cd ~/django_projects/market
    git tag             # Make sure you don't already have a mkt4 tag (only do this once!)

    git add .
    git commit -a -m mkt4
    git tag -a mkt4 -m mkt4
    git tag             # Make sure you do have a mkt4 tag

The whole `~/django_projects/market` folder is a `git` repository so you can use `git` for many cool
things.  But for now we are just making sure you have a "re-spawn" point if AI breaks your code badly.

The instructions to "revert" to the saved tag are at the bottom of this
document.  Hopefully you won't need to use them.

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

There is a bit of an extra wrinkle when adapting the approach in `tagme` because we are
using a ModelForm in order to process uploaded pictures.  The key is that you have to
save the tags after the form has been copied to the model and the model has been saved because
the tags are stored using a many-to-many data model.

https://django-taggit.readthedocs.io/en/latest/forms.html#commit-false

In your `forms.py` code you will need to (a) add 'tags' to the field list and (b) update the
code in the commit to look like:

        if commit:
            instance.save()
            self.save_m2m()    # Add this

In your `views.py`, we have our own code to pull data from the form to the model and then
save the model.  This code is in both the insert and edit views:

        # Adjust the model owner before saving
        inst = form.save(commit=False)
        inst.owner = self.request.user
        inst.save()

        # https://django-taggit.readthedocs.io/en/latest/forms.html#commit-false
        form.save_m2m()    # Add this

You need to add the `save_m2m()` call *after* the instance was saved.

Finally, the detail template for the `tagme` application contains code that can be adapted to
display the tags in your application.

Note About using AI with this Assignment
----------------------------------------

By this point in the course, you should already have the earlier assignments working 
correctly. While it’s fine to use AI to help diagnose specific errors, one of the worst
approaches is to take a file you’ve been developing for weeks, hand it to an AI, 
and paste back a completely rewritten version without reviewing it carefully.

AI-generated solutions often “work” in the sense that they run without crashing, but 
they may remove or alter details the autograder expects. This can cause confusing 
autograder failures that are very hard to untangle.

There are many ways to solve these assignments that are roughly equivalent, but the 
autograder is not checking for just any equivalent solution. It's verifying that you’ve 
adapted the provided sample code in the expected ways.

If AI produces a solution that is only “approximately” like the samples, it probably 
won’t pass. And if AI overwrites working code you already built and have working perfectly, 
you may find yourself needing to start over from the beginning. <!-- And yes — the irony of asking AI to help write a warning about overusing AI is not lost on us. -->


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

Resetting Your Database
------------------------

If `python manage.py check` is working and `python manage.py makemigrations` is working, 
you may have made a series of changes to `models.py` and mis-match between your migration files
and database have become confused causing `makemigrations` to fail.

We have provided a Python script that completely resets your Django project's database and
removes all migration files, allowing you to start fresh with a clean database
schema. This is particularly useful when migration files have become corrupted
or when you need to restructure your models significantly.

First we update the samples code so you have the latest helper scripts.

    cd ~/dj4e-samples/
    git checkout django52
    git pull origin django52

Then follow the instructions at
[README_DB.md](https://github.com/csev/dj4e-samples/blob/django52/tools/DB_RESET.md)

The reset script will:
- Drop all tables in your database
- Delete all migration files (except `__init__.py`)
- Allow you to start fresh with `makemigrations` and `migrate`

Discarding Code Changes and Going back to the Mkt4 Tag
------------------------------------------------------

If if you make a mistake (or if AI makes a mistake) and you paste it into your code and break everything (i.e. not
migrations and `models.py`)
you can decide to reset your code base to the tag your created above (if you created a tag).
Follow these instructions - move slowly and if things blow up - get help.

If `python manage.py check` is working and `python manage.py makemigrations` is not working, you may not need
to throw your code away and might want to try a database reset first.

First we update the samples code so you have the latest helper scripts.

    cd ~/dj4e-samples/
    git checkout django52
    git pull origin django52

Then follow the instructions at
[README_DB.md](github.com/csev/dj4e-samples/blob/django52/tools/README_GIT.md)

If you go back, and have discarded your code changes - you probably need to reset your database as well
as shown in the previous section.

