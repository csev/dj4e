# Switching Applications

If you are taking the Django for Everybody course, you may have several Django projects on PythonAnywhere: the **Django Girls** tutorial (this one), the main **mysite** project (polls, etc.), and perhaps the **market** classified ads application. PythonAnywhere gives you one web application, so you switch between them by changing the Web tab configuration.

## The three configurations

Go to the **Web** tab on PythonAnywhere and update these these settings plus the WSGI file for the project you want to run. Make sure to use your PythonAnywhere account name in the file paths.

---

### 1. Django Girls (this tutorial)

**Source code:** `/home/drchuck/djangogirls`  
**Working directory:** `/home/drchuck/djangogirls`  
**Virtualenv:** `/home/drchuck/.ve52`

**WSGI configuration file** – replace the entire content with:

    import os
    import sys

    path = os.path.expanduser('~/djangogirls')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'mysite.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

---

### 2. django_projects/mysite (polls, books, etc.)

**Source code:** `/home/drchuck/django_projects/mysite`  
**Working directory:** `/home/drchuck/django_projects/mysite`  
**Virtualenv:** `/home/drchuck/.ve52`

**WSGI configuration file** – replace the entire content with:

    import os
    import sys

    path = os.path.expanduser('~/django_projects/mysite')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'mysite.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

---

### 3. market (classified ads)

**Source code:** `/home/drchuck/django_projects/market`  
**Working directory:** `/home/drchuck/django_projects/market`  
**Virtualenv:** `/home/drchuck/.ve52`

**WSGI configuration file** – replace the entire content with:

    import os
    import sys

    path = os.path.expanduser('~/django_projects/market')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'config.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())

---

## After changing configuration

Click **Reload** at the top of the Web tab for your changes to take effect. Your site URL stays the same (e.g. `https://youraccount.pythonanywhere.com/`); only which Django project serves it changes.
