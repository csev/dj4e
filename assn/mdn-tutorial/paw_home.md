Django Home Page
================

Ironically, we are several steps into this tutorial and we *finally* get
to the point where we are building the elements of *our* user interface into
our application.  Most everything up to this point is book keeping.

Our next step is to add some url routes and views to
our LocalLibrary application so we can build some user interface bits.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Home_page

If you are submitting this assignment to the DJ4E autograder 
for this assignment,
it would be a good idea to check the autograder for specific instructions that
the autograder requires for this assignment.

Complete the following sections of the Views tutorial:

* Go into the `catalog` application

        cd ~/django_projects/locallibrary/catalog

* Edit `catalog/urls.py` so it looks like follows:

        from django.urls import path
        from . import views
        urlpatterns = [
            path('', views.index, name='index'),    # New line as per tutorial
        ]

        # New lines below to serve static files in debug mode
        import os
        from django.urls import re_path
        from django.views.static import serve
        from django.conf import settings

        BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

        urlpatterns += [
            re_path(r'^static/(?P<path>.*)$', serve, {
                'document_root': os.path.join(BASE_DIR, 'catalog/static'),
            }),
        ]

    We need the extra bits to serve the static files locally in debug mode.  We will come back
    to talk about how to serve static files when we move our application to production.

* Edit `views.py` for the views.index as suggested in the tutorial

* You will have to make the `templates` and `static` directories

        cd ~/django_projects/locallibrary/catalog
        mkdir templates
        mkdir static
        mkdir static/css

* Create the file `templates/base_generic.html`as suggested.  The tutorial shows a simple version and then a more complex version with some CSS files - create the more complex version.  The autograder may require the addition of a `<meta>` tag in the `<head>` area of the base template.

* Create the file `static/css/styles.css` as suggested

* Create the file `templates/index.html`as suggested but replace the string "Mozilla Developer Network!" with something else.  You do not need to use your name if you don't want to - it just must be something other than the text in the tutorial.


* Reload your application under the `Web` tab in
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>

* Visit the catalog site
<a href="http://mdntutorial.pythonanywhere.com/catalog" target="_blank">http://mdntutorial.pythonanywhere.com/catalog</a>

When you make changes to configuration files like `urls.py` or `views.py` it is always a good idea to reload
your web application on
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>
under the `Web` tab and `Reload` the web server to re-read your updated configuration files.

Generally the server automatically detects changes to templates or static files
without requiring your application to be reloaded.  There is not harm in reloading your 
web application too often.  If you made a change and dont think you are seeing the change,
reload the web application.

Common Problems and How to Fix Them
-----------------------------------

If you reload your web application and get the "Something went wrong :("
page when you access your web application, check on the "error.log" link 
and scroll to the very bottom to see why your application will not start.
If you see and error message like:

    No module named 'django_extensions'

It probably means that you have not set up your virtual environment under 
the `Web` tab.  

If you did the installation properly and created a `django3`
virtual environment, the virtual environment under the `Web` tab should be set to:

    /home/drchuck/.virtualenvs/django3

Replacing "drchuck" with your PythonAnywhere account name.


References
----------

https://docs.djangoproject.com/en/3.2/ref/views/

https://stackoverflow.com/questions/30430131/get-the-file-path-for-a-static-file-in-django-code

