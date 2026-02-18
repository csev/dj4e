# Starting a new Django Project!

> Part of this chapter is based on tutorials by Geek Girls Carrots (https://github.com/ggcarrots/django-carrots).

> Parts of this chapter are based on the [django-marcador
tutorial](http://django-marcador.keimlink.de/) licensed under the Creative Commons
Attribution-ShareAlike 4.0 International License. The django-marcador tutorial
is copyrighted by Markus Zapke-Gründemann et al.

We're going to create a small blog!

The first step is to start a new Django project. Basically, this means that we'll run some scripts provided by Django that will create the skeleton of a Django project for us. This is just a bunch of directories and files that we will use later.

The names of some files and directories are very important for Django. You should not rename the files that we are about to create. Moving them to a different place is also not a good idea. Django needs to maintain a certain structure to be able to find important things.

> Remember to run everything in the virtualenv. If you don't see a prefix `(.ve52)` in your console, you need to activate your virtualenv. We explained how to do that in the __Django installation__.

In the shell, you should run the following command. **Don't forget to add the period (or dot) `.` at the end!**

{% filename %}command-line{% endfilename %}
```
(.ve52) ~/djangogirls$ django-admin startproject mysite .
```

> The period `.` is crucial because it tells the script to install Django in your current directory (for which the period `.` is a short-hand reference).

> **Note** When typing the command above, remember that you only type the part which starts by `django-admin`.
The `(.ve52) ~/djangogirls$` part shown here is just example of the prompt that will be inviting your input on your command line.

You only need to do `startproject` once.  If things get really messed up, you can start over by going into your
home folder under `Files` and deleting the `djangogirls` folder and then re-unning the `startproject` command above.

`django-admin.py` is a script that will create the directories and files for you. You should now have a directory structure which looks like this:

```
djangogirls
├── manage.py
├── mysite
│   ├── asgi.py
│   ├── __init__.py
│   ├── settings.py
│   ├── urls.py
│   └── wsgi.py
└── requirements.txt
```

`manage.py` is a script that helps with management of the site. With it we will be able (among other things) to start a web server on our computer without installing anything else.

The `settings.py` file contains the configuration of your website.

Remember when we talked about a mail carrier checking where to deliver a letter? `urls.py` file contains a list of patterns used by `urlresolver`.

Let's ignore the other files for now as we won't change them. The only thing to remember is not to delete them by accident!


## Changing settings

Let's make some changes in `mysite/settings.py`. Open the file using the file editor.

#### Changing the Timezone

It would be nice to have the correct time on our website. Go to [Wikipedia's list of time zones](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones) and copy your relevant time zone (TZ) (e.g. `Europe/Berlin`).

In `settings.py`, find the line that contains `TIME_ZONE` and modify it to choose your own timezone.  For example:

{% filename %}mysite/settings.py{% endfilename %}
```python
TIME_ZONE = 'Europe/Berlin'
```

> **Note**: Timezones should be in the Region/City format, so eg "EDT" is not valid, but "America/Detroit" is.


#### Changing the Language

A language code consists of the language, e.g. `en` for English or `de` for German, and the country code, e.g. `de` for Germany or `ch` for Switzerland. If English is not your native language, you can add this to change the default buttons and notifications from Django to be in your language. So you would have "Cancel" button translated into the language you defined here. [Django comes with a lot of prepared translations](https://docs.djangoproject.com/en/5.2/ref/settings/#language-code).

If you want a different language, change the language code by changing the following line:

{% filename %}mysite/settings.py{% endfilename %}
```python
LANGUAGE_CODE = 'de-ch'
```


#### Other settings

We'll also need to add a path for static files.
(We'll find out all about static files and CSS later in the tutorial.)
Go down to the *end* of the file,
and just underneath the `STATIC_URL` entry, add a new one called `STATIC_ROOT`:

{% filename %}mysite/settings.py{% endfilename %}
```python
STATIC_URL = 'static/'
STATIC_ROOT = BASE_DIR / 'static'
```

When `DEBUG` is `True` and `ALLOWED_HOSTS` is empty, the host is validated against `['localhost', '127.0.0.1', '[::1]']`.
This won't match our hostname on PythonAnywhere once we deploy our application so we will change the following setting:

{% filename %}mysite/settings.py{% endfilename %}
```python
ALLOWED_HOSTS = ['*']
```

## Set up a database

There's a lot of different database software that can store data for your site. We'll use the default one, `sqlite3`.

This is already set up in this part of your `mysite/settings.py` file:

{% filename %}mysite/settings.py{% endfilename %}
```python
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.sqlite3',
        'NAME': BASE_DIR / 'db.sqlite3',
    }
}
```

To create a database for our blog, let's run the following in the console: `python manage.py migrate` (we need to be in the `djangogirls` directory that contains the `manage.py` file). If that goes well, you should see something like this:

{% filename %}command-line{% endfilename %}
```
(.ve52) ~/djangogirls$ python manage.py migrate
Operations to perform:
  Apply all migrations: admin, auth, contenttypes, sessions
Running migrations:
  Applying contenttypes.0001_initial... OK
  Applying auth.0001_initial... OK
  Applying admin.0001_initial... OK
  Applying admin.0002_logentry_remove_auto_add... OK
  Applying admin.0003_logentry_add_action_flag_choices... OK
  Applying contenttypes.0002_remove_content_type_name... OK
  Applying auth.0002_alter_permission_name_max_length... OK
  Applying auth.0003_alter_user_email_max_length... OK
  Applying auth.0004_alter_user_username_opts... OK
  Applying auth.0005_alter_user_last_login_null... OK
  Applying auth.0006_require_contenttypes_0002... OK
  Applying auth.0007_alter_validators_add_error_messages... OK
  Applying auth.0008_alter_user_username_max_length... OK
  Applying auth.0009_alter_user_last_name_max_length... OK
  Applying auth.0010_alter_group_name_max_length... OK
  Applying auth.0011_update_proxy_permissions... OK
  Applying auth.0012_alter_user_first_name_max_length... OK
  Applying sessions.0001_initial... OK
```

And we're done! Time to start the web server and see if our website is working!

## Doing a final check of the changes you made to your application

You need to be in the directory that contains the `manage.py` file (the `~/djangogirls` directory). In the console, we can verify that our web server will start by running `python manage.py check`:

{% filename %}command-line{% endfilename %}
```
(.ve52) ~/djangogirls$ python manage.py check
```

## Routing your PythonAnywhere domain name to your new Django project

You will need to route your web domain name to the folder containing your  new Django project.  Go to the `Web` tab
on PythonAnywhere, and scroll down and make the following changes:

    Source code: /home/drchuck/djangogirls
    Working directory: /home/drchuck/djangogirls
    
The virtual environment should already be set to:

    Virtualenv: /home/drchuck/.ve52

Then edit the *WSGI Configuration File* and put the following code into it.

__Make sure to delete the existing content__ of the *WSGI Configuration File*
and completely replace it with the text below.
This is slightly different from the sample in the PythonAnywhere tutorial.

    import os
    import sys

    path = os.path.expanduser('~/djangogirls')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'mysite.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

Once the above configuration is complete, go back to the top of the PythonAnywhere
Web tab, `Reload` your web application, wait a few seconds and check
that it is up and visiting the URL for your application shown in the Web
tab on PythonAnywhere like:

{% filename %}browser{% endfilename %}
```
http://127.0.0.1:8000/
```

You can open this in another browser window and you should see the Django install worked page.

Congratulations! You've just created your first website and run it using a web server! Isn't that awesome?

![Install worked!](images/install_worked.png)

Ready for the next step? It's time to create some content!
