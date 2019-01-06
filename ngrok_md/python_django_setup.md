# Macintosh setup

## Prerequisites
* Homebrew
* Python 3 (installed via Homebrew)
* [MySQL Community Server](https://dev.mysql.com/downloads/mysql/)
* [MySQL workbench](https://dev.mysql.com/downloads/workbench/)
* [ngrok](https://ngrok.com/download)
* [Github](https://github.com/) account
* [PyPI](https://pypi.org/ account (Python Package Index)

## Optional
* [MySQL shell](https://dev.mysql.com/downloads/shell/)

## Installing Django

Django conventions: models, tests, urls, and views submodules.

#### Install virtualenv
[Installation](https://virtualenv.pypa.io/en/stable/installation/)
[User Guide](https://virtualenv.pypa.io/en/stable/userguide/)

```
$ pip3 install virtualenv
```

#### Create project directory

```
$ mkdir django_tutorial
```

#### Initialize git

```
$ git .init
```

#### Add .gitignore

#### Add README, LICENSE, NOTICE

### Create a virtual Environment
WARN: do this *before* installing Django.

```
$ cd path/to/django_tutorial                   <-- change directory to project home
$ which python3                                <-- check where python3 is installed
/usr/local/bin/python3
$ virtualenv -p /usr/local/bin/python3 venv    <-- install Python 3 virtual environment
$ source venv/bin/activate                     <-- start 'venv' virtual environment
```

Once activated the virtual environment 'venv' will be displayed before the prompt:

```
(venv) $
```

INFO: You can terminate the virtual environment by issuing the `deactivate` command:

```
(venv) $ deactivate
```

INFO: Python 3 also provides virtual environment support:

```
$ python3 -m venv venv
```

Gotcha: https://medium.com/@justiniso/don-t-rename-your-virtualenv-projects-1049e47e1261

### Install Django
WARN: activate the 'Django_tutorial' virtual environment before installing.

```
(venv) $ pip3 install Django
```

### Tutorial, part 1

#### Confirm Django installation
Invoke the Python shell and confirm that Django is installed:

```
(venv) $ python3
>>> import django
>>> print(django.get_version())
2.0.7
>>> exit()
```

You can also check whether or not Django is installed this way:

```
(venv) $ python3 -m django --version
2.0.7
```

#### Create mysite project
WARN: Don't forget to add the trailing dot ('.') to the command.  The dot creates the new project with a directory structure that simplifies deployment to a server.  If you forget to include the dot, delete the directories and files that were created (except 'venv') and run the command again along with the trailing doct ('.').

```
(venv) $ django-admin startproject mysite .
```

The resulting `mysite` layout:

```
django-tutorial/
    manage.py
    mysite/
        __init__.py
        settings.py
        urls.py
        wsgi.py
```

#### Verify project
Start up the development server by issuing the `runserver` command:

```
(venv) $ python3 manage.py runserver
```

Expected terminal output:

```
Performing system checks...

System check identified no issues (0 silenced).

You have 14 unapplied migration(s). Your project may not work properly until you apply the migrations for app(s): admin, auth, contenttypes, sessions.
Run 'python manage.py migrate' to apply them.

July 08, 2018 - 02:03:48
Django version 2.0.7, using settings 'mysite.settings'
Starting development server at http://127.0.0.1:8000/
Quit the server with CONTROL-C.
```

INFO: ignore the database migration warnings; we will address those momentarily.

Point your browser to http://127.0.0.1:8000/ and confirm that the default Django page is displayed.  Then switch back to the terminal and shut down the development server by clicking CONTROL-C on the keyboard.

#### Create polls app
A Django project is composed of one or more *apps*.  Add an app named 'polls'.

```
(venv) $ python3 manage.py startapp polls
```

The resulting polls app layout:

```
polls/
    __init__.py
    admin.py
    apps.py
    migrations/
        __init__.py
    models.py
    tests.py
    views.py
```

#### Perform database migration
Attempt to connect to the MySQL 'polls' database in order to perform the initial set of required Django migrations referenced when the polls app was created.

```
(venv) $ python3 manage.py migrate
```
