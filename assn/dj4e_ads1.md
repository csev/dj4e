Building a Classified Ad Web Site
=================================

In this assignment, you will build a web site that is roughly equivalent to

https://chucklist.dj4e.com/m1

This web site is a classified ad web site.   People can view ads without logging in
and if they log in, they can create their own ads.   It uses a social login
that allows loging using github accounts.

You will build this application by borrowing parts and pieces from the code that runs

https://samples.dj4e.com/

and combining them into a single application.

Make sure to get the latest version of dj4e-samples.  If you have never checked it out
on PythonAnywhere:

    cd ~
    git clone https://github.com/csev/dj4e-samples

If you have already checked `dj4e-samples`  on PythonAnywhere do:

    workon django3
    cd ~/dj4e-samples
    git pull
    pip install -r requirements.txt


Pulling In Code From Samples
----------------------------

In this section, we will break and then fix your `settings.py` and `urls.py`.
When this is done, the autos, cats, dogs, etc will stop working unless you 
add them back to these two files.  It is OK for these applications to be working.
The autograder will just look at /ads.

(1) Copy the `settings.py` and `urls.py` files and the entire
`home` folder from the `dj4e-samples` project:

    cp ~/dj4e-samples/dj4e-samples/settings.py ~/django_projects/mysite/mysite
    cp ~/dj4e-samples/dj4e-samples/urls.py ~/django_projects/mysite/mysite
    cp -r ~/dj4e-samples/home/* ~/django_projects/mysite/home

(2) Edit `~/dango_projects/mysite/mysite/settings.py` and then delete
all the `INSTALLED_APPLICATIONS` after `home`.  You also have to search
and replace `dj4e-samples` with `mysite` in a few places.  Also set
the name of your application in the `settings.py` file:

    # Used for a default title
    APP_NAME = 'ChucksList'

This shows up in default page titles and default page navigation.

(5) Edit your `django_projects/mysite/mysite/urls.py` and
remove all of the `path()` calls to the sample applications. Make
sure to keep the `path()` to include the `home.urls`.  Also keep
the `site` and `favicon` rules in your `urls.py`.

(6) Edit the file `django_projects/mysite/home/templates/home/main.html` and put
this HTML in the file:

    <html>
    <head>
        <title>{{ settings.APP_NAME }}</title>
    </head>
    <body>
        <h1>Welcome to {{ settings.APP_NAME }}</h1>
        <p>
        Hello world.
        </p>
    </body>
    </html>

(7) At this point, you should be able to run:

    python3 manage.py check

Keep running `check` until it does not find any errors.

If you restart your web application, there won't be many working urls.
Try these two to see if you have the home code working properly:

    https://your-account.pythonanywhere.com/
    https://your-account.pythonanywhere.com/favicon.ico
    https://your-account.pythonanywhere.com/accounts/login

Look at how pretty the login form looks :).
Don't worry about social login yet.  We will get to that later.
Favicons are shown in the tabs in the browser.  We will get to favicons later too :)

If you get an error like `Could not import github_settings.py for social_django`
when running `manage.py` or restarting your PythonAnywhere webapp,
don't worry - you will see this warning until you set up social login.

Building the Ads Application
----------------------------

In this section, you will pull bits and pieces of the sample applications repository and pull them
into your `ads` application.  

__Important Note:__ If you find you have a problem saving files in the PythonAnywhere
system using their browser-based editor, you might need to turn off your ad blocker for
this site - weird bt true.

(1) Create a new `ads` application within your `mysite` project:

    cd django_projects/mysite
    python3 manage.py startapp ads

The add the application to your `mysite/mysite/settings.py` and `mysite/mysite/urls.py'.

(2) Use this in your `ads/model.py`:

    from django.db import models
    from django.core.validators import MinLengthValidator
    from django.contrib.auth.models import User
    from django.conf import settings

    class Ad(models.Model) :
        title = models.CharField(
                max_length=200,
                validators=[MinLengthValidator(2, "Title must be greater than 2 characters")]
        )
        price = models.DecimalField(max_digits=7, decimal_places=2, null=True)
        text = models.TextField()
        owner = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)
        created_at = models.DateTimeField(auto_now_add=True)
        updated_at = models.DateTimeField(auto_now=True)

        # Shows up in the admin list
        def __str__(self):
            return self.title

(3) Copy the `owner.py` from `myarts` to your ads application.  This is the one file you <b>do not</b>
have to change at all (thanks to object orientation :) ).

(4) The files `admin.py`, `views.py`, `urls.py`, and the `templates` folder will require significant
adaptation to be suitable for a classified
ad application and the above model.   A big part of this assignment is to use the
view classes that are in `owner.py` and used in `views.py`.  The new `owner` field should
not be shown to the user on the create and update forms, it should be automatically set
by the classes like `OwnerCreateView` in `owner.py`.  If you see an "owner" drop down
in your create screen the program is not implemented correctly and will fail the autograder.

(5) When you are implementing the update and delete views, make sure to follow the url patterns
for the update and delete operations.  They should be of the form `/ad/14/update`
and `/ad/14/delete`.  Something like the following should work in your `urls.py`:

    from django.urls import path, reverse_lazy
    from . import views

    app_name='ads'
    urlpatterns = [
        path('', views.AdListView.as_view()),
        path('ads', views.AdListView.as_view(), name='all'),
        path('ad/<int:pk>', views.AdDetailView.as_view(), name='ad_detail'),
        path('ad/create',
            views.AdCreateView.as_view(success_url=reverse_lazy('ads:all')), name='ad_create'),
        path('ad/<int:pk>/update',
            views.AdUpdateView.as_view(success_url=reverse_lazy('ads:all')), name='ad_update'),
        path('ad/<int:pk>/delete',
            views.AdDeleteView.as_view(success_url=reverse_lazy('ads:all')), name='ad_delete'),
    ]

(6) Ad you build the application, use `check` periodically as you complete some of the code.

    python3 manage.py check

(7) Once your application is mostly complete and can pass the `check`
without error, add the new models to your migrations and database tables:

    python3 manage.py makemigrations
    python3 manage.py migrate

Debugging: Searching through all your files in the bash shell
-------------------------------------------------------------

If you have errors, you might find the `grep` tool very helpful in figuring out where you might find your errors.
For example, lets say after you did all the editing, and went to the ads url and got this error:

    NoReverseMatch at /ads
    'myarts' is not a registered namespace

You *thought* you fixed all the instances where the string "myarts" was in your code, but you must have missed one.
You can manually look at every file individually or use the following command to let the computer do the searching:

    cd ~/django_projects/mysite
    grep -ri myarts *

You might see output like this:

    ads/templates/ads/ad_list.html:<a href="{% url 'login' %}?next={% url 'myarts:all' %}">Login</a>

The `grep` program is searching for all the files in the current folder and in subfolders for any lines
in any file that have the string "myarts" in them and shows you the file name and the line within the file.

The `grep` command is the <a href="https://en.wikipedia.org/wiki/Grep" target="_blank">"Generalized Regular
Expression Parser"</a> and is one of the most useful Linux commands to know.
The 'r' means 'recursive' and the 'i' means 'ignore case.   The `grep` program will save you so much time :).

Adding the Bootstrap menu to the top of the page
------------------------------------------------

Next we will add the bootstrap navigation bar to the top of your application as shown in:

https://chucklist.dj4e.com/

This top bar includes a 'Create Ad' navigation item and the login/logout navigation as well as
the gravatar when the user logs in.

(2) Edit all four of the `ads_` files in `ads/templates/ads` to change them so
they extend `ads/base_menu.html`.  Change the first line of each file from:

    {% extends "base_bootstrap.html" %}

to be:

    {% extends "base_menu.html" %}

(3) Then create `home/templates/base_menu.html` with the following content:

    {% extends "base_bootstrap.html" %}
    {% block navbar %}
    {% load app_tags %}
    <nav class="navbar navbar-default navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">{{ settings.APP_NAME }}</a>
        </div>
        <!-- https://stackoverflow.com/questions/22047251/django-dynamically-get-view-url-and-check-if-its-the-current-page -->
        <ul class="nav navbar-nav">
          {% url 'ads' as ads %}
          <li {% if request.get_full_path == ads %}class="active"{% endif %}>
              <a href="{% url 'ads:all' %}">Ads</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            {% if user.is_authenticated %}
            <li>
            <a href="{% url 'ads:ad_create' %}">Create Ad</a>
            </li>
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                    <img style="width: 25px;" src="{{ user|gravatar:60 }}"/><b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{% url 'logout' %}?next={% url 'ads:all' %}">Logout</a></li>
                </ul>
            </li>
            {% else %}
            <li>
            <a href="{% url 'login' %}?next={% url 'ads:all' %}">Login</a>
            </li>
            {% endif %}
        </ul>
      </div>
    </nav>
    {% endblock %}

(4) Find the line in your `base_bootstrap.html` that looks like this:

        <meta name="dj4e-code" content="99999999">

   and change the `9999999`  to be "<span id="dj4e-code">missing</span>"

Make sure to check the autograder for additional markup requirements.

When you are done, you should see an 'Ads' menu on the left and a 'Create Ad' link on the right just like the
sample implementation.

Fun Challenges
--------------

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps. The gravatar can be
anything you like - it does not have to be a picture of you.  The gravatar is associated an email address
so make sure to give an email address to the user you create with `createsuperuser`.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after you update the file and press 'Refresh' and/or switch browsers.  Sometimes the browswer caching
is "too effective" on a favicon so to force a real reload to check if the new favicon is really being served
you can add a GET parameter tho the URL to forc it to be re-retrieved:

    https://chucklist.dj4e.com/favicon.ico?x=42

Change the `x` value to something else if you want to test over and over.

(3) Make social login work.  Take a look at
<a href="https://github.com/csev/dj4e-samples/blob/master/dj4e-samples/github_settings-dist.py" target="_blank">
github_settings-dist.py</a>, copy it into
`mysite/mysite/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments of the file.  Also take a look at
<a href="https://github.com/csev/dj4e-samples/blob/master/dj4e-samples/urls.py" target="_blank">
dj4e-samples/urls.py</a> and make sure that the "Switch to social login" code is correct
and at the end of your `mysite/mysite/github_settings.py`.

You can register two applications with github - one on localhost and one on PythonAnywhere.  If you are
using github login on localhost - make sure that you register `http://127.0.0.1:8000/` instead
of `http://localhost:8000/` and use that in your browser to test your site.  If you
use localhost, you probably will get the `The redirect_uri MUST match the registered callback
URL for this application.` error message when you use social login.

Working with Ambiguity
----------------------

This assignment is more vague than previous assignments - on purpose.  The goal is to get
closer to the development model for actual applications.  You know what you want to build
and start with a mostly blank slate.  You look at sample code, reuse some code form stuff
you build earlier, do some online
searching and glue pieces of what you find together to make your application.  Of course as
you are gluing bits from various places together, they always break and you have to adjust things
so they fit in your application.

So this is kind of like the real world - when you have to build your own first application
for someone else.

Let us know if you really get stuck.  We want you to succeed in this assignment.


<script>
var d= new Date();
var code = "42"+((Math.floor(d.getTime()/1234567)*123456)+42)
document.getElementById("dj4e-code").innerHTML = code;
</script>

