Building a Classified Ad Web Site
=================================

In this assignment, you will build a web site that is roughly equivalent to 

https://chucklist.dj4e.com/

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

(2) Copy `samples/requirements.txt` to `adlist/requirements.txt` and run

    pip install -r requirements.txt   # or pip3

This will pull in important extra libraries that your application will need.

(3) Adapt `adlist/settings.py` to pull in most of `samples/settings.py`.

You might even want to copy `samples/settings.py` to `adlist/settings.py` and then delete
all the `INSTALLED_APPLICATIONS` after `home` and add `ads`.  You also have to search
and replace `samples` with `adlist` in a few places.

Alternatively, you can look through the `samples/settings.py` and copy pertinent lines
into `adlist/settings.py` - some lines have an "Add" comment to help draw your attention
to things to copy across.

Make sure to add a line like this:

    # Used for a default title
    APP_NAME = 'ChucksList'

This shows up in default page titles and default page navigation.

(4) Copy the entire `home` application folder into your adlist project.  This should not
need much changing - it has things like base templates, and login templates and is designed
to quickly get up to speed getting started in a new project.

(5) Edit your `adlist/urls.py` and pull in some of the paths from `samples/urls.py`.   Look 
for lines that say "Keep" to help make sure you configure all of the optional features.

(6) Make a simple view in your `ads` application, and connect it into your `adlist/urls.py`

At this point, you should be able to run:

    python3 manage.py makemigrations
    python3 manage.py migrate
    python3 manage.py createsuperuser
    python3 manage.py runserver

There won't be many working urls other than '/ads' - you can also test the login with something like:

    https://chucklist.dj4e.com/accounts/login

Don't worry about social login yet.  We will get to that later.

Another cool url to test is:

    https://chucklist.dj4e.com/favicon.ico

Favicons are shown in the tabs in the browser.  We will get to favicons later too :)

Building the Ads Application
----------------------------

In this section, you will pull bits and pieces of the sample applications repository and pull them
into your `ads` application.

(1) The best place to start is to figure out the `menu` application and copy most of it into `ads`
and tweak it so that it looks more like the navigation in sample implementation.

(2) Next, look at the `owner` application - it shows how rows in a model can be "owned" by a user.
Use this in your `ads/model.py`:

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

Then adapt the `admin.py`, `views.py`, `urls.py`, and templates to be suitable for a classified
ad application and the above model.

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

