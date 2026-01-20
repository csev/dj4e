
Django Project Tutorial 2 - On PythonAnywhere
=============================================

Learning Objectives:

* Understand Django models and models.py
* Use the Django shell
* Understand the django admin feature
* Understand databases and Django migration


We are going to do the second tutorial from the Django project web site.  That tutorial
is generic and can be used in many situations including developing locally on your own
computer - but we need to do the tutorial on PythonAnywhere.

**Important:** Do not go back to tutorial1 on the Django web site and create the folder `djangotutorial`.
You already have this folder from the first assignment in this class called `~/django_projects/mysite`.

So we need a mapping between what the tutorial says and what you do on PythonAnywhere -
A Rosetta stone as it were.  This table maps from what the tutorial says to how you
do it in PythonAnywhere.  In general, you are doing the the same thing in a different
place or in a different way.

| Django Project Tutorial | On PythonAnywhere | 
| -------------- | ------------ |
| Open up `mysite/settings.py` | Open `django_projects/mysite/mysite/settings.py` in the Files editor |
| `cd  djangotutorial` | `cd ~/django_projects/mysite` |
| `python manage.py migrate` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py migrate` |
| Edit the `polls/models.py` | Open `django_projects/mysite/polls/models.py` in the Files editor |
| `python manage.py makemigrations polls` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py makemigrations polls` | 
| `python manage.py sqlmigrate polls 0001` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py sqlmigrate polls 0001` | 
| `python manage.py shell` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py shell` |
| `python manage.py createsuperuser` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py createsuperuser` |
| `python manage.py runserver` | In the console / shell: <br/> `cd ~/django_projects/mysite`<br/> `python manage.py check` <br/> If there are no errors from `check`, reload your web application in the Web Tab  or in a text editor |
| Open `http://127.0.0.1:8000/admin/` in your browser | Open `https://(your-account).pythonanywhere.com/admin/` (with your account) in your browser |

After a few assignments, you won't need this "Rosetta Stone" / "Cheat Sheet" mapping between generic
DJango instructions and PythonAnywhere. You will be able to look at generic 
Django instructions and map thme on to "how we do it on PythonAnywhere".

Django Project Tutorial 2
-------------------------

Armed with the above mapping, follow the instructions in this tutorial:

https://docs.djangoproject.com/en/5.2/intro/tutorial02/

One suggestion, when the tutorial tells you to put the following in `~/django_projects/mysite/polls/admin.py`:

    from django.contrib import admin

    from .models import Question

    admin.site.register(Question)

Insert the following instead:

    from django.contrib import admin

    from .models import Question
    from .models import Choice

    admin.site.register(Question)
    admin.site.register(Choice)

It makes it easier to edit questions and choices in the Admin UI.

Also when you are shown code samples like:


    class Question(models.Model):
        # ...
        def __str__(self):
            return self.question_text

The `...` means that you are supposed to keep the information in the file that is already there and
add two new lines.

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

