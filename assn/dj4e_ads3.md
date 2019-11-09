Classified Ad Web Site - Milestone 3
====================================

In this assignment, you will expand your classified ads web site to add functionality
equivalent to:

https://chucklist.dj4e.com/m3

We will add a favoriting capability to your previous milestone by borrowing more parts and pieces from the code that runs:

https://samples.dj4e.com/

Do All of the Challenges
------------------------

At this point all of the challenges should be working - not all will be tested
by the autograder - but we will separately check them.

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps.  The gravatar can be
any thing you like - it does not have to be a picture of you.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after up update the file and press 'Refresh' and.or switch browsers.

(3) Make social login work.  Take a look at `dj4e-samples/github_settings-dist.py`, copy it into
`adlist/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments in the `github_setting.py` file.
You can register two applications - one on localhost and one on PythonAnywhere.  If you are
using github on localhost - make sure that you
register `http://127.0.0.1:8000/` instead of `http://localhost:8000/` and use that in your browser
to test your site.  If you use localhost, you probably will get the `The redirect_uri MUST
match the registered callback URL for this application.` error message when you use social login.


Adding Favorites to the Ads Application
-----------------------------------------

In this section, you will pull bits and pieces of the `favs` sample application
into your `ads` application to add support for logged in users to "favorite" and "un-favorite"
ads.

(1) Add this to your `ads/model.py`, talking inspiration from `dj4e-samples/favs/models.py`

    class Ad(models.Model) :

        ...

        # Favorites
        favorites = models.ManyToManyField(settings.AUTH_USER_MODEL,
            through='Fav', related_name='favorite_ads')
        ...

    class Fav(models.Model) :
        ad = models.ForeignKey(Ad, on_delete=models.CASCADE)
        user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)

        # https://docs.djangoproject.com/en/2.1/ref/models/options/#unique-together
        class Meta:
            unique_together = ('ad', 'user')

        def __str__(self) :
            return '%s likes %s'%(self.user.username, self.ad.title[:10])

Of course do the migrations once you have modified the model.

(2) Add two routes to your `urls.py` for the favorite features

    ...
    path('ad/<int:pk>/favorite',
        views.AddFavoriteView.as_view(), name='ad_favorite'),
    path('ad/<int:pk>/unfavorite',
        views.DeleteFavoriteView.as_view(), name='ad_unfavorite'),
    ...

(3) Pull in and adapt `ThingListView`, `AddFavoriteView`, and `DeleteFavoriteView`
from `dj4e-samples/favs/views.py` into your `views.py`.

(4) Alter your `ad_list.html` by looking through `favs/templates/favs/list.html`.  Make sure to add the
parts that show the stars based on the list of favorites for this user and the `favPost()` JavaScript
code at the end.


