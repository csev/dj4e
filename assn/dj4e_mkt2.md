
Classified Ads + Pictures (Milestone 2)
=======================================

In this assignment, you will expand your classified ads web site to add functionality
equivalent to:

https://market.dj4e.com/m2

The primary additions from the previous milestone are to add an image to each ad and
keep all the features from the previous version of the application.
You can log into this site using
an account: <b>facebook</b> and a password of <b>Marketnn!</b> where "nn" is the
two-digit number of Dr. Chuck's race car or the numeric value for asterisk in the ASCII character set.

You will build this application by borrowing parts and pieces from the code that runs

https://samples.dj4e.com/

and adding code to your previous version of this application.  You need to complete 
the previous assignment and pass the autograder, and get a grade before attempting
this assignment.

The autograder will re-test all the features of the previous assignment to make sure they
continue to work as you add new features.

__Important Note:__ If you find you have a problem saving files in the PythonAnywhere
system using their browser-based editor, you might need to turn off your ad blocker for
this site - weird but true.

Adding Pictures to the Ads Application
--------------------------------------

In this section, you will pull bits and pieces of the `pics` sample application
into your `ads` application to add support for an optional single picture per ad.

(1) Add this to your `mkt/model.py`, taking inspiration from `dj4e-samples/pics/models.py`

    class Ad(models.Model) :

        ...
        # Picture
        picture = models.BinaryField(null=True, blank=True, editable=True)
        content_type = models.CharField(max_length=256, null=True, blank=True,
            help_text='The MIMEType of the file')
        ...

Do not include the entire `Pic` model.  Of course do the migrations once you have modified the model.

(2) Copy in the `pics/forms.py` as well as `pics/humanize.py`.  Edit the `pics/forms.py` and change
*only* the following four lines:

    ...
    from pics.models import Pic
    ...
    from pics.humanize import naturalsize
    ...
            model = Pic
            fields = ['title', 'text', 'picture']

to:

    ...
    from ads.models import Ad
    ...
    from ads.humanize import naturalsize
    ...
            model = Ad
            fields = ['title', 'text', 'picture', 'price']

Leave the other bits of `mkt/forms.py` alone.  Only change the name of the model in the
above two lines.

(3) Take a look at `pics/views.py` and adapt the patterns in `PicCreateView` and
`PicUpdateView` and replace the code for `AdCreateView` and `AdUpdateView` in `mkt/views.py`.
These new views don't inherit from owner.py because they manage the `owner` column in the `get()`
and `post()` methods.

(4) Alter your `templates/mkt/ad_form.html` by looking through `pics/templates/pics/form.html`.  Make sure to add the
JavaScript bits at the end and add `enctype="multipart/form-data"` and the `id`
attribute to the `form` tag.

(5) Alter the `templates/mkt/ad_detail.html` template by looking through `pics/templates/pics/detail.html` and
to add code to include the image in the output if there is an image associated with the ad.
Make sure not to lose the `price` field in your UI.  If you don't see the `price` field
in your UI it is likely a mistake in your `forms.py`.

(6) Add an `ad_picture` route to your `urls.py` based on the `pics_picture` route from `pics/urls.py`:

    path('ad_picture/<int:pk>', views.stream_file, name='ad_picture'),

(5) Add the `stream_file()` view from `pics/views.py` and adapt appropriately.

Test to make sure you can upload, view, and update pictures with your ads.

Manual Testing
--------------

It is always a good idea to manually test your application before submitting it for grading.  Here
are a set of manual test steps:

* Make two accounts - If you have not already done so
* Log in to your application on the first account
* Create an ad with a picture
* In the all ads list make sure that the edit / delete button shows correctly
* View its details click on the picture to see that it fulls the screen
* Update the ad, check that the details are correct
* Delete the ad - just to make sure it works - the autograder gets grumpy if it cannot delete an ad
* Create two more ads


Do Some or All of the Challenges
---------------------------------

You will have to finish these eventually - so you might as well work on them now.
And they are fun.

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps. The gravatar can be
anything you like - it does not have to be a picture of you.  The gravatar is associated with an email address
so make sure to give an email address to the user you create with `createsuperuser`.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after you update the file and press 'Refresh' and/or switch browsers.  Sometimes the browser caching
is "too effective" on a favicon so to force a real reload to check if the new favicon is really being served
you can add a GET parameter to the URL to force it to be re-retrieved:

    https://market.dj4e.com/favicon.ico?x=42

Change the `x` value to something else if you want to test over and over.

(3) Make social login work.  Take a look at
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
