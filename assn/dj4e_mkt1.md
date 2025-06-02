
Building a Marketplace Web Site - Owned Rows - Milestone 1
==========================================================

In this assignment, you will build a web site that is roughly equivalent to

https://market.dj4e.com/m1

This web site is a classified ad web site.   People can view ads without logging in
and if they log in, they can create their own ads. You can log into this site using 
an account: <b>facebook</b> and a password of <b>Marketnn!</b> where "nn" is the 
two-digit number of Dr. Chuck's race car or the numeric value for asterisk in the ASCII character set.

You will build this application by borrowing parts and pieces from the code that runs

https://samples.dj4e.com/

and combining them into a single application.

The autograder expects that you will copy and adapt code from the provided examples.  If
you just build a "roughly equivalent application" (perhaps using AI) that seems to work
OK - the autograder might be looking for a different pattern in the HTML of your
application and reject your application.

Initial Setup
-------------

We provide an initial github repository for you to checkout and install on PythonAnywhere
with most of the base code already built so you can add your application.

If you have not already done so, follow the instructions at:

https://github.com/csev/dj4e-market

If you are taking this course on www.dj4e.com, you should have this running and submitted 
to the autograder for credit before continuing below.

Building the Mkt Application
----------------------------

In this section, you will pull bits and pieces of the sample applications repository and pull them
into your `mkt` application.

__Important Note:__ If you find you have a problem saving files in the PythonAnywhere
system using their browser-based editor, you might need to turn off your ad blocker for
this site - weird but true.

(1) Create a new `mkt` application within your `market` project:

    cd ~/django_projects/market
    python manage.py startapp mkt

(2) Then add the application to your `market/config/settings.py`

INSTALLED_APPS = [

    ... Keep all the existing entries ...

    'taggit',
    'home.apps.HomeConfig',
    'mkt.apps.MktConfig',
]

(3) Then edit your `market/config/urls.py` to add a route to the new application:

(4) Use this in your `mkt/models.py`:

    from django.db import models
    from django.core.validators import MinLengthValidator
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

(5) Copy the `owner.py` file from `dj4e-samples/myarts` to your `mkt` folder.  This is the one file you <b>do not</b>
have to change at all (thanks to object orientation 😊).

(6) The files `admin.py`, `views.py`, `urls.py`, and the `templates` in the `myarts` folder will require significant
adaptation to be suitable for a classified
ad application and the above model.   A big part of this assignment is to use the
view classes that are in `owner.py` and used in `views.py`.  The new `owner` field should
not be shown to the user on the create and update forms, it should be automatically set
by the classes like `OwnerCreateView` in `owner.py`.  If you see an "owner" drop down
in your create screen the program is not implemented correctly and will fail the autograder.

(7) Adapt the templates in `myarts/templates/myarts` as a starting point to create the needed
templates in `mkt/templates/mkt`.

(8) When you are implementing the update and delete views, make sure to follow the url patterns
for the update and delete operations.  They should be of the form `/ad/<int:pk>/update`
and `/ad/<int:pk>/delete`.  Something like the following should work in your `urls.py`:

    from django.urls import path, reverse_lazy
    from . import views

    app_name='mkt'
    urlpatterns = [
        path('', views.AdListView.as_view(), name='all'),
        path('ad/<int:pk>', views.AdDetailView.as_view(), name='detail'),
        path('ad/create',
            views.AdCreateView.as_view(success_url=reverse_lazy('mkt:all')), name='create'),
        path('ad/<int:pk>/update',
            views.AdUpdateView.as_view(success_url=reverse_lazy('mkt:all')), name='update'),
        path('ad/<int:pk>/delete',
            views.AdDeleteView.as_view(success_url=reverse_lazy('mkt:all')), name='delete'),
    ]

(9) As you build the application, use `check` periodically as you complete some of the code.

    python manage.py check

(10) Once your application is mostly complete and can pass the `check`
without error, add the new models to your migrations and database tables:

    python manage.py makemigrations
    python manage.py migrate

Adding the Bootstrap menu to the top of the page
------------------------------------------------

Next we will add the bootstrap navigation bar to the top of your application as shown in:

https://chuckplace.dj4e.com/

This top bar includes a 'Create Ad' navigation item and the login/logout navigation with
gravatar when the user logs in.

(1) Edit all four of the `mkt` files in `mkt/templates/mkt` to change them so
they extend `mkt/base_menu.html`.  Change the first line of each file from:

    {% extends "base_bootstrap.html" %}

to be:

    {% extends "base_menu.html" %}

(2) Create `home/templates/base_menu.html` with this code:

    {% extends 'base_bootstrap.html' %}
    {% load app_tags %} <!-- see home/templatetags/app_tags.py and dj4e-samples/settings.py -->
    {% block navbar %}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-radius:10px !important">
      <div class="container-fluid">
        <a class="navbar-brand" href="{% url 'mkt:all' %}">{{ settings.APP_NAME }}</a>
        <ul class="navbar-nav">
          {% url 'mkt:all' as x %}
          <li {% if request.get_full_path == x %}class="active"{% endif %}>
              <a class="nav-link" href="{% url 'mkt:all' %}" role="button">Ads</a></li>
        </ul>
        <ul class="navbar-nav">
          {% if user.is_authenticated %}
          <li>
             <a class="nav-link" href="{% url 'mkt:create' %}">Create Ad</a>
          </li>
          <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle" href="#" id="rightnavDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img style="width: 25px;" src="{{ user|gravatar:60 }}"/><b class="caret"></b>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="rightnavDropdown">
                <li><a class="dropdown-item" href="{% url 'logout' %}?next={% url 'mkt:all' %}">Logout</a></li>
            </ul>
           </li>
           {% else %}
           <li class="nav-item"><a class="nav-link" href="{% url 'login' %}?next={% url 'mkt:all' %}">Login</a></li>
           {% endif %}
        </ul>
      </div>
    </nav>
    {% endblock %}

(3) Find the line in your `base_bootstrap.html` that looks like this:

        <meta name="dj4e-code" content="99999999">

   and change the `9999999`  to be "<span id="dj4e-code">missing</span>"
   Note that there will be two meta tags, one for dj4e-code and one for
   dj4e - keep both in this file.

Make sure to check the autograder for additional markup requirements.

When you are done, you should see an 'Ads' menu on the left and a 'Create Ad' link on the right just like the
sample implementation.

Manual Testing
--------------

It is always a good idea to manually test your application before submitting it for grading.  Here
are a set of manual test steps:

* Make two accounts if you have not already done so
* Log in to your application on the first account
* Make sure the menu bar shows at the top of all of the screens - the autograder gets grumpy about a missing menu on a page
* Create an ad
* Try to submit an add with no title - make sure that it complains
* Create an ad
* In the all ad list make sure that the edit / delete button shows correctly
* View its details - make sure the edit / delete button shows up correctly
* Update the ad, check that the details are correct after the update
* Delete the ad - just to make sure it works
* Create two more ads
* Log in on the second account - make sure you **do not** see edit / delete buttons on the existing ad
* Go into one the detail for the ad created by the other user -  make sure you **do not** see edit / delete buttons
* Create a new ad on the second account
* Make sure that in the "all ad list" the edit / delete buttons are only present on the ad the second user "owns"
* Delete the ad


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
is "too effective" on a favicon so to force a real reload (command/ctrl + shift + r) to check if the new
favicon is really being served you can add a GET parameter to the URL to force it to be re-retrieved:

    https://market.dj4e.com/favicon.ico?x=42

Change the `x` value to something else if you want to test over and over.

(3) To make the social login work.  Take a look at
<a href="https://github.com/csev/dj4e-samples/blob/main/dj4e-samples/github_settings-dist.py" target="_blank">
github_settings-dist.py</a>, copy it into
`market/config/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments of the file.  Also take a look at
<a href="https://github.com/csev/dj4e-samples/blob/main/dj4e-samples/urls.py" target="_blank">
dj4e-samples/urls.py</a> and make sure that the "Switch to social login" code is correct
and at the end of your `market/config/github_settings.py`.

You can register two applications with github - one on localhost and one on PythonAnywhere.  If you are
using github login on localhost - make sure that you register `http://127.0.0.1:8000/` instead
of `http://localhost:8000/` and use that in your browser to test your site.  If you
use localhost, you probably will get the `The redirect_uri MUST match the registered callback
URL for this application.` error message when you use social login.

Working with Ambiguity
----------------------

This assignment is more vague than previous assignments - on purpose.  The goal is to get
closer to the development model of actual applications. You know what you want to build
and start with a mostly blank slate.  You look at sample code, reuse some code from stuff
you built earlier, do some online
searching and glue pieces of what you find together to make your application.  Of course as
you are gluing bits from various places together, they always break and you have to adjust things
so they fit in your application.

So this is kind of like the real world - when you have to build your own first application
for someone else.

It is not tricky on purpose.  We want you to succeed in this assignment.  But we do want you
to do less cutting-and-pasting and more writing Django applications.

Debugging: Searching through all your files in the bash shell
-------------------------------------------------------------

If you have errors, you might find the `grep` tool very helpful in figuring out where you might find your errors.
For example, lets say after you did all the editing, and went to the `mkt` url and got this error:

    NoReverseMatch at /mkt
    'myarts' is not a registered namespace

You *thought* you fixed all the instances where the string "myarts" was in your code, but you must have missed one.
You can manually look at every file individually or use the following command to let the computer do the searching:

    cd ~/django_projects/mysite
    grep -ri myarts *

You might see output like this:

    mkt/templates/mkt/list.html:<a href="{% url 'login' %}?next={% url 'myarts:all' %}">Login</a>

The `grep` program searches files in the current  folder and subfolders for any lines
in any file that have the string "myarts" in them and shows you the file name and the line it is mentioned.

The `grep` command is the <a href="https://en.wikipedia.org/wiki/Grep" target="_blank">"Generalized Regular
Expression Parser"</a> and is one of the most useful Linux commands to know.
The 'r' means 'recursive' and the 'i' means 'ignore case.   The `grep` program will save you so much time 😊.

Some Common Errors in This Assignment
-------------------------------------

Since you are in effect starting with a brand new `config/settings.py` and `config/urls.py`, you might
find a few problems when you are running `python manage.py check` - I will keep a list of the common
problems and their solutions here:

(1) If you have a problem running `migrate` or `makemigrations` in step 10 above, you might want
to start with a fresh MySQL database.  Since we are using a MYSQL server, we can't
just delete the SQLite file and start over - but it is not much more difficult.

First go into `Consoles` and start a `MySQL` console.  You should go into a shell and see a prompt
like this - type the command `SHOW DATABASES;` to find your database:

    Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
    mysql> SHOW DATABASES;
    +--------------------+
    | Database           |
    +--------------------+
    | information_schema |
    | dj4e$market        |
    | dj4e$default       |
    +--------------------+
    3 rows in set (4.05 sec)
    mysql>

Note - <b>never touch</b> the `information_schema` database - if you mess with this you break your entire MySQL
installation and may need to create a completely new PythonAnywhere account.   Leave `information_schema`
alone.

Pick the database you are using (in your `settings.py`) and issuer the `USE` command to select
the database and run the `SHOW TABLES;` command:

    mysql> use dj4e$market;
    Database changed
    mysql> SHOW TABLES;
    +----------------------------+
    | Tables_in_dj4e$market      |
    +----------------------------+
    | mkt_ad                     |
    | django_admin_log           |
    | django_content_type        |
    | django_migrations          |
    | django_session             |
    | social_auth_association    |
    | social_auth_code           |
    | social_auth_nonce          |
    | social_auth_partial        |
    | social_auth_usersocialauth |
    +----------------------------+
    10 rows in set (0.00 sec)
    mysql>

Then we will get rid of the `mkt_ad` table and its associated migration records:

    mysql> DROP TABLE mkt_ad;
    mysql> DELETE FROM django_migrations WHERE app='mkt';

Then, in the bash shell, you can remove and re-make the migrations

    cd ~/django_projects/market
    rm mkt/migrations/00*

Then go back to step 9 and pick up with the `makemigrations` and `migrate` steps as well as
`createuser` is needed.

<script>
var d= new Date();
var code = "42"+((Math.floor(d.getTime()/1234567)*123456)+42)
document.getElementById("dj4e-code").innerHTML = code;
</script>
