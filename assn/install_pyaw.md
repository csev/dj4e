Installing DJango on PythonAnywhere
===================================

https://help.pythonanywhere.com/pages/FollowingTheDjangoTutorial/

    mkvirtualenv django2 --python=/usr/bin/python3.6
    pip install django ## this may take a couple of minutes


Back in MDN

    mkdir dj4e
    cd dj4e
    django-admin startproject mytestsite


In PYAW

WebApp Tab

Add New WebAPp
Manual Configuration
Python 3.6

virtualenv 

/home/mdntutorial/.virtualenvs/django2

Source code
/home/mdntutorial/dj4e/mytestsite


    import os
    import sys

    path = os.path.expanduser('~/dj4e/mytestsite')
    if path not in sys.path:
        sys.path.insert(0, path)
    os.environ['DJANGO_SETTINGS_MODULE'] = 'mytestsite.settings'
    from django.core.wsgi import get_wsgi_application
    from django.contrib.staticfiles.handlers import StaticFilesHandler
    application = StaticFilesHandler(get_wsgi_application())


Set allowed hosts 

     ALLOWED_HOSTS = [ '*' ]                                                                                                        

Restart web app and go to:

    http://mdntutorial.pythonanywhere.com/

