
Django Project Tutorial 2
=========================

Learning Objectives:

* Understand Django models and models.py
* Use the Django shell
* Understand the django admin feature
* Understand databases and Django migration

Follow the instructions in this tutorial:

https://docs.djangoproject.com/en/5.2/intro/tutorial02/

Skim all the notes below before starting the tutorial - it will save you a lot of time.

Notes for PythonAnywhere
------------------------

**Hint:** At the point where the tutorial tells you to modify the `polls/admin.py` you
should also import the `Choice` model following the pattern of importing the `Question` model.
It will make completing the assignment a lot easier.

**Important:** Do not go back to tutorial1 on the Django web site and create the folder `djangotutorial`
- you already have this folder from the first assignment called `django_projects`.
Make sure to do all your work in `~/django_projects/myite`
and not `djangotutorial`.

Note that when you are typing a password for the `createsuperuser` command in the tutorial,
it does not echo your characters so folks can't observe your password.
It looks like your typing is not working - but it is - simply
type the letters and then enter - you will enter your password twice to verify.

Also as a reminder, you never use `runserver` on PythonAnywhere:

    python manage.py runserver     # <-- Never run this on pythonanywhere

Instead after you change files, run

    python manage.py check

And then go to the the PythonAnywhere Web tab and press Reload.

Also when it tells you to navigate to a `localhost` or `127.0.0.1` like

    http://127.0.0.1:8000/admin
    http://localhost:8000/admin

Instead nagivate to the same path on your PythonAnywhere site:

    (your-account).pythonanywhere.com/admin

If You Create an admin User and Need to Change its Password
------------------------------------------------------------

Note that when Linux / Bash is promoting for a password, it does
not "echo" your characters so someone watching over your shoulder
does not see the password.  Just type the password and press
enter.  Trust that Linux is listening as you type even though
it does not show the characters as you type.

If you run `createsuperuser` and end up with an `admin` account and want to
change the password for an account, use:

    cd ~/django_projects/mysite
    python manage.py changepassword admin

You can change any Django user account using this approach.

If Your Model/Database Gets Messed Up Completely - Start over
-------------------------------------------------------------

Since this is your first time doing a model and migrations, things can
get messed up - if you want to clean up your database and start with a
fresh database using the following instructions:

    cd ~/django_projects/mysite
    rm */migrations/00*
    rm db.sqlite3
    python manage.py makemigrations
    python manage.py migrate
    python manage.py createsuperuser    # If needed

You need to recreate the superuser because it is stored in the database
and the `rm` command emptied out your database.  You can do
this process any time your database feels like it is messed up.  But
you have to re-enter all your data.

