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

Note: Do **not** build this application by forking the sample application repository
and hacking it.  There is just too much cruft in that repository that will make it hard to develop
your application.   You will end up with a lot cleaner code by taking pieces from
the sample applications and copying them into a new application.   At some point,
we will ask you to hand
in the source of your application and we will check to make sure you are not using a fork
of the sample application.

Borrowing from the Samples Repository
-------------------------------------

(1) Make a new project under your `django_projects` called `adlist` and within that
project make an application called `ads`.

(2) Copy `samples/requirements.txt` to `adlist/requirements.txt` and launch a shell in your virtual environment and run:

    pip install -r requirements.txt   # or pip3

This will pull in important extra libraries that your application will need.  On PythonAnywhere
under the Web tab there is a link to launch a shell into your virtual environment.  It
is important to be in the virtual environment so the installed libraries end
up in the right place.

(3) Adapt `adlist/settings.py` to pull in most of `samples/settings.py`.

You might even want to copy `samples/settings.py` to `adlist/settings.py` and then delete
all the `INSTALLED_APPLICATIONS` after `home` and add `ads`.  You also have to search
and replace `samples` with `adlist` in a few places.

Alternatively, you can look through the `samples/settings.py` and copy pertinent lines
into `adlist/settings.py` - some lines have an "Add" comment to help draw your attention
to things to copy across.

In addition to all the other settings fixes, make sure to add a line
to `adlist/settings.py` like this:

    # Used for a default title
    APP_NAME = 'ChucksList'

This shows up in default page titles and default page navigation.

(4) Copy the entire `home` application folder from into your adlist project.  This should not
need much changing - it has things like base templates, and login templates and is designed
to quickly get up to speed getting started in a new project.  If you are using PythonAnywhere

Make sure to get the latest version of dj4e-samples.  If you have never checked it out
on PythonAnywhere:

    cd ~
    git clone https://github.com/csev/dj4e-samples

If it already is on PythonAnywhere:

    cd ~/dj4e-samples
    git pull

Once you have `~/dj4e-samples` and `~/django_projects/adlist` you
can copy all of home with the following commands:

    mkdir ~/django_projects/adlist/home
    cp -r ~/dj4e-samples/samples/home/* ~/django_projects/adlist/home

(5) Edit your `adlist/urls.py` and pull in some of the paths from `samples/urls.py`.   Look
for lines that say "Keep" to help make sure you configure all of the optional features.

At this point, you should be able to run:

    python3 manage.py makemigrations
    python3 manage.py migrate
    python3 manage.py createsuperuser

There won't be many working urls.  Try these two to see if you have the home code
working properly:

    https://chucklist.dj4e.com/accounts/login
    https://chucklist.dj4e.com/favicon.ico

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

(3) Pull in pieces of `owner` application other than `models.py`.  Then adapt the
`admin.py`, `views.py`, `urls.py`, and templates to be suitable for a classified
ad application and the above model. Make sure to follow the url patterns
the update and delete operations.  They chould be of the form `/ad/14/update`
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

(4) Pull `base_menu.html` template from `samples/menu` application and into `ads`
and then edit the ad templates to extend `base_menu.html` using `main_menu.html`
as an example.  Then adjust `adlist/templates/base_menu.html` to make the navigation
look like the adlist application.

Fun Challenges
--------------

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps. The gravatar can be 
any thing you like - it does not have to be a picture of you.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after up update the file and press 'Refresh' and.or switch browsers.

(3) Make social login work.  Take a look at `samples/github_settings-dist.py`, copy it into
`adlist/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments in the `github_setting.py` file.
You can register two applications - one on localhost and one on PythonAnywhere.  If you are
using github on localhost - make sure that you 
register `http://127.0.0.1:8000/` instead of `http://localhost:8000/` and use that in your browser
to test your site.  If you use localhost, you probably will get the `The redirect_uri MUST 
match the registered callback URL for this application.` error message when you use social login.

Working with Ambiguity
----------------------

This assignment is far more vague than previous assignments - on purpose.  The goal is to get
closer to the development model for actual applications.  You know what you want to build
and start with a mostly blank slate.  You look at sample code, reuse some code form stuff
you build earlier, do some online
searching and glue pieces of what you find together to make your application.  Of course as
you are gluing bits from various places together, they lways break and you have to adjust things
so they fit in your application.

So this is kind of like the real world - when you have to build your own first application
for someone else.

Let us know if you really get stuck.  We want you to succeed in this assignment.




