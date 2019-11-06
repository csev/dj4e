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

If it already is on PythonAnywhere:

    cd ~/dj4e-samples
    git pull

Note: Do **not** build this application by forking the sample application repository
and hacking it.  There is just too much cruft in that repository that will make it hard to develop
your application.   You will end up with a lot cleaner code by taking pieces from
the sample applications and copying them into a new application.   At some point,
we will ask you to hand
in the source of your application and we will check to make sure you are not using a fork
of the sample application.

Borrowing from the Samples Repository
-------------------------------------

(1) Make a new project under your `django_projects` called `adlist`.

(2) Copy `dj4e-samples/requirements.txt` to `django_projects/adlist/requirements.txt` and launch a shell.  If you are using
virtual environments you must run the `pip` command in your virtual environment.   In PythonAnywhere
under Linux you would say:

    workon django2

The run:

    pip install -r requirements.txt   # or pip3

This will pull in important extra libraries that your application will need.  On PythonAnywhere
make sure to double check under the `Web` tab under the `Virtualenv` section that you have
it set to something like:

    /home/--your-account---/.virtualenvs/django2

So that your python application is run within the virtual environment.

(3) Adapt `django_projects/adlist/adlist/settings.py` to pull in most of `dj4e-samples/dj4e-samples/settings.py`.

You might even want to copy `dj4e-samples/dj4e-samples/settings.py` to
`dango_projects/adlist/adlist/settings.py` and then delete
all the `INSTALLED_APPLICATIONS` after `home` and add `ads`.  You also have to search
and replace `dj4e-samples` with `adlist` in a few places.

Alternatively, you can look through the `dj4e-samples/dj4e-samples/settings.py` and copy pertinent lines
into `django_project/adlist/adlist/settings.py` - some lines have an "Add" comment to help draw your attention
to things to copy across.

In addition to all the other settings fixes, make sure to add a line
to `django_project/adlist/adlist/settings.py` like this:

    # Used for a default title
    APP_NAME = 'ChucksList'

This shows up in default page titles and default page navigation.

(4) Copy the entire `home` application folder from into your adlist project.  This should not
need much changing - it has things like base templates, and login templates and is designed
to quickly get up to speed getting started in a new project.  

    mkdir ~/django_projects/adlist/home
    cp -r ~/dj4e-samples/home/* ~/django_projects/adlist/home

(5) Edit your `adlist/urls.py` and pull in some of the paths from `dj4e-samples/dj4e-samples/urls.py`.   Look
for lines that say "Keep" to help make sure you configure all of the optional features.

At this point, you should be able to run:

    python3 manage.py makemigrations
    python3 manage.py migrate
    python3 manage.py createsuperuser

There won't be many working urls.  Try these two to see if you have the home code
working properly:

    https://your-account.pythonanywhere.com/accounts/login
    https://your-account.pythonanywhere.com/favicon.ico

Don't worry about social login yet.  We will get to that later.
Favicons are shown in the tabs in the browser.  We will get to favicons later too :)

If you get an error like `Could not import github_settings.py for social_django`
when running `manage.py` or restarting your PythonAnywhere webapp,
don't worry - you will see this warning until you set up social login.

Building the Ads Application
----------------------------

In this section, you will pull bits and pieces of the sample applications repository and pull them
into your `ads` application.

(1) Create a new `ads` application within your `adlist` project.

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

(3) Pull in pieces of `myarts` application other than `models.py`.  Then adapt the
`admin.py`, `views.py`, `urls.py`, and templates to be suitable for a classified
ad application and the above model.   A big part of this assignment is to use the
view classes that are in `owner.py` and used in `views.py`.  The new `owner` field should
not be shown to the user on the create and update forms, it should be automatically set
by the classes like `OwnerCreateView` in `owner.py`.  If you see an "owner" drop down
in your user interface the program is not implemented correctly and will fail the autograder.

(4) When you are implementing the update and delete views, make sure to follow the url patterns
the update and delete operations.  They should be of the form `/ad/14/update`
and `/ad/14/delete`.  Something like the following should work in your `urls.py`:

    urlpatterns = [
        path('', views.AdListView.as_view()),
        path('ads', views.AdListView.as_view(), name='ads'),
        path('ad/<int:pk>', views.AdDetailView.as_view(), name='ad_detail'),
        path('ad/create',
            views.AdCreateView.as_view(success_url=reverse_lazy('ads')), name='ad_create'),
        path('ad/<int:pk>/update',
            views.AdUpdateView.as_view(success_url=reverse_lazy('ads')), name='ad_update'),
        path('ad/<int:pk>/delete',
            views.AdDeleteView.as_view(success_url=reverse_lazy('ads')), name='ad_delete'),
    ]

(5) Change your `adlist/urls.py` to use the following url patterns so the main route ('')
goes to the `ads` application.

    urlpatterns = [
        path('', include('ads.urls')),
        path('admin/', admin.site.urls),
        path('accounts/', include('django.contrib.auth.urls')),
        url(r'^oauth/', include('social_django.urls', namespace='social')),
    ]


Adding the Bootstrap menu to the top of the page
------------------------------------------------

Next we will add the bootstrap navigation bar to the top of your application as shown in:

https://chucklist.dj4e.com/

This top bar includes a 'Create Ad' navigation item and the login/logout navigation as well as
the gravatar when the user logs in.

(1) Copy `base_menu.html` template from `dj4e-samples/menu` application and into `ads/templates/ads`.  This should
extend `base_bootstrap.html` (in your `home` application).  You will need to adjust the navigation and url
lookups in this file to match the naviation in the sample implementation.

(2) Then edit all four of the `ads_` files in `ads/templates/ads` to change them so
they extend `ads/base_menu.html`.  Change the first line of each file from:

    {% extends "base_bootstrap.html" %}

to be.

    {% extends "ads/base_menu.html" %}

(3) Then edit `ads/templates/base_menu.html` replace the main lists of navigation items as follows:

    <nav class="navbar navbar-default navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">{{ settings.APP_NAME }}</a>
        </div>
        <!-- https://stackoverflow.com/questions/22047251/django-dynamically-get-view-url-and-check-if-its-the-current-page -->
        <ul class="nav navbar-nav">
          {% url 'ads' as ads %}
          <li {% if request.get_full_path == ads %}class="active"{% endif %}>
              <a href="{% url 'app_name_here:ads' %}">Ads</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            {% if user.is_authenticated %}
            <li>
            <a href="{% url 'app_name_here:ad_create' %}">Create Ad</a>
            </li>
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                    <img style="width: 25px;" src="{{ user|gravatar:60 }}"/><b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{% url 'logout' %}?next={% url 'app_name_here:ads' %}">Logout</a></li>
                </ul>
            </li>
            {% else %}
            <li>
            <a href="{% url 'login' %}?next={% url 'app_name_here:ads' %}">Login</a>
            </li>
            {% endif %}
        </ul>
      </div>
    </nav>

(4) Find the line in your `base_bootstrap.html` that looks like this:

        <meta name="wa4e-code" content="99999999">

   and change the `9999999`  to be "<span id="wa4e-code">missing</span>"

Make sure to check the autograder for additional markup requirements.

When you are done, you should see an 'Ads' menu on the left and a 'Create Ad' link on the right just like the
sample implementation.

Fun Challenges
--------------

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps. The gravatar can be
any thing you like - it does not have to be a picture of you.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after up update the file and press 'Refresh' and.or switch browsers.

(3) Make social login work.  Take a look at `dj4e-samples/dj4e-samples/github_settings-dist.py`, copy it into
`adlist/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments in the `github_setting.py` file.
You can register two applications - one on localhost and one on PythonAnywhere.  If you are
using github on localhost - make sure that you
register `http://127.0.0.1:8000/` instead of `http://localhost:8000/` and use that in your browser
to test your site.  If you use localhost, you probably will get the `The redirect_uri MUST
match the registered callback URL for this application.` error message when you use social login.

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
document.getElementById("wa4e-code").innerHTML = code;
</script>

