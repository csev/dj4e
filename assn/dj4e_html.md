
Serving HTML Content Using Django
=================================

In this assignment we will be adding some HTML content to your Django instance.
Most of the content that comes from a site is usually served through a template
and a view, but sometimes you just want to have a few static HTML pages on 
your site.

Serving Static Content
----------------------

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
reload your application and then test your application.

Testing Your Application
------------------------

Navigate to your top level page
page <a href="dj4e_html/noroute.htm" target="_blank">drchuck.pythonanywhere.com</a>
(replace drchuck with your account) - 
with no path and you should see an error page
like <a href="dj4e_html/noroute.htm" target="_blank">this</a>.
This is Django's way of letting you know that you have requested a url
that has no route and so it is returning a 
<a href="https://en.wikipedia.org/wiki/HTTP_404" target="_blank">404 Not found</a> error.
But since you have `DEBUG = True` in your `settings.py` it is giving you some additional
detail which will prove very helpful to you as a developer trying to figour out why
your site is not working as you expect.

You will see the same error if you go to some random URL that does not exist like
like <a href="dj4e_html/xyzzy.htm" target="_blank">drchuck.pythonanywhere.com/xyzzy</a>

Later we will route the main path (i.e. no path) to a view so users can visit your
site at the top level.

Next test the ability to serve the `site` content.

Go to <a href="dj4e_html/site.htm" target="_blank">drchuck.pythonanywhere.com/site</a> - you should
see a list of files including your `hello.txt`.  Click on `hello.txt` and you should see
"Hello world". 

Go to <a href="dj4e_html/hello.htm" target="_blank">drchuck.pythonanywhere.com/site/subfolder/hello.htm</a> - you should
see "Hello World" styled using a HTML header tag.



