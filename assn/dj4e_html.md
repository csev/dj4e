
Adding HTML Content to Django
=============================

In this assignment we will be adding some HTML content to your Django instance.
Most of the content that comes from a site is usually served through a template
and a view, but sometimes you just want to have a few static HTML pages on 
your site.

Serving HTML Content
--------------------

Make a two folders

    mkdir ~/django_projects/mysite/site
    mkdir ~/django_projects/mysite/site/subfolder

Create a file at `~/django_projects/mysite/site/hello.txt` with the text "Hello World".

Create a file at `~/django_projects/mysite/site/subfolder/hello.html` with this text:

    <h1>Hello World</h1>

Change your `mysite/urls.py` to be:

    import os
    from django.contrib import admin
    from django.urls import path
    from django.conf.urls import url
    from django.views.static import serve

    # Up two folders to serve "site" content
    BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    SITE_ROOT = os.path.join(BASE_DIR, 'site')

    urlpatterns = [
        path('admin/', admin.site.urls),
    
        url(r'^site/(?P<path>.*)$', serve,
            {'document_root': SITE_ROOT, 'show_indexes': True},
            name='site_path'
        ),
    ]

Going forward we will be adding entries to this `urlpatterns` variable 
as we add new features.  Do not remove these entries from your `urls.py`.
Just add the new entries as required by the upcoming assignments.

Once you have made the changes, you should check for errors using:

    cd ~/django_projects/mysite
    python3 manage.py check

If the `check` fails, stop and fix any and all errors before continuing.

Once `check` succeeds, you can go to the `Web` tab on PythonAnywhere,
reload your application and then test your application by navigating to:

    (your-account).pythonanywhere.com

If your application fails to 
load or reload, you might get an error message that looks
like <a href="dj4e_html/pyaw_error.htm" target="_blank">this</a>.

If you get an error, you will need to look through the error logs
under the `Web` tab on PythonAnywhere:

<center><img src="dj4e_html/error_logs.png" style="border: 1px black solid;"></center>

First check the `error` log and then check the `server` log.
Make sure to scroll through the logs to the end to find the latest error.


Testing Your Application
------------------------

Navigate to your top level page
page __(your-account).pythonanywhere.com__
with no path and you should see an error page
like <a href="dj4e_html/noroute.htm" target="_blank">this</a>.
This is Django's way of letting you know that you have requested a url
that has no route and so it is returning a 
<a href="https://en.wikipedia.org/wiki/HTTP_404" target="_blank">404 Not found</a> error.
But since you have `DEBUG = True` in your `settings.py` it is giving you some additional
detail which will prove very helpful to you as a developer trying to figure out why
your site is not working as you expect.

You will see the same error if you go to some random URL that does not exist like
like __(your-account).pythonanywhere.com/xyzzy__ and it should look
like <a href="dj4e_html/xyzzy.htm" target="_blank">this</a>

In a later assignment, we will add a route for the main path (i.e. no path) so users can visit your
site at the top level.

Next test the ability to serve the `site` content.

Go to __(your-account).pythonanywhere.com/site__ - you should see
see a list of files including your `hello.txt`
(like <a href="dj4e_html/site.htm" target="_blank">this</a>).
Click on `hello.txt` on your site and you should see "Hello world". 

Go to __(your-account).pythonanywhere.com/site/subfolder/hello.htm__ - you should
see "Hello World" styled using a HTML header tag 
( like <a href="dj4e_html/hello.htm" target="_blank">this</a>)



