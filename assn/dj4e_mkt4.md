
Marketplace - Favorites - Milestone 4
=====================================

In this assignment, you will expand your classified ads web site to add functionality
equivalent to:

https://market.dj4e.com/m4

You can log into this site using
an account: <b>facebook</b> and a password of <b>Marketnn!</b> where "nn" is the
two-digit number of Dr. Chuck's race car or the numeric value for asterisk in the ASCII character set.

We will add a favoriting capability to your previous milestone by borrowing more parts and pieces from the code that runs:

https://samples.dj4e.com/

Do All of the Challenges
------------------------

At this point all of the challenges should be working - not all will be tested
by the autograder - but we will separately check them.

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps. The gravatar can be
anything you like - it does not have to be a picture of you.  The gravatar is associated an email address
so make sure to give an email address to the user you create with `createsuperuser`.

(2) Change your `home/static/favicon.ico` to a favicon of your own making.   I made my favicon
at https://favicon.io/favicon-generator/ - it might not change instantly after you update the favicon
because they are cached extensively.   Probably the best way to test is to go right to the favicon url
after you update the file and press 'Refresh' and/or switch browsers.  Sometimes the browser caching
is "too effective" on a favicon so to force a real reload to check if the new favicon is really being served
you can add a GET parameter tho the URL to force it to be re-retrieved:

    https://market.dj4e.com/favicon.ico?x=42

Change the `x` value to something else if you want to test over and over.

(3) Make social login work.  Take a look at
<a href="https://github.com/csev/dj4e-samples/blob/main/dj4e-samples/github_settings-dist.py" target="_blank">
github_settings-dist.py</a>, copy it into
`market/config/github_settings.py` and go through the process on github to get your client ID and
secret.   The documentation is in comments of `market/config/github_settings.py`.

To get your key and secret from github, go to:
<a href="https://github.com/settings/developers" target="_blank">https://github.com/settings/developers</a>
and add a new OAuth2 application.  Here are some sample settings:

    Application name: ChuckList PythonAnywhere
    Homepage Url: https://drchuck.pythonanywhere.com
    Application Description: Some pithy words...
    Authorization callback URL: https://drchuck.pythonanywhere.com/oauth/complete/github/
   
You can register two applications with github - one on localhost and one on PythonAnywhere.  If you are
using github login on localhost - make sure that you register `http://127.0.0.1:8000/` instead
of `http://localhost:8000/` and use that in your browser to test your site.  If you
use localhost, you probably will get an error message when you login like:
`The redirect_uri MUST match the registered callback URL for this application.`


Adding Favorites to the Mkt Application
-----------------------------------------

In this section, you will pull bits and pieces of the `favwc` sample application
into your `mkt` application to add support for logged in users to "favorite" and "un-favorite"
ads. We will also create a custom web component to implement this favorites feature.

(1) Add this to your `mkt/models.py`, taking inspiration from `dj4e-samples/favwc/models.py`

    class Ad(models.Model) :

        ...

        # Favorites
        favorites = models.ManyToManyField(settings.AUTH_USER_MODEL,
            through='Fav', related_name='favorite_ads')
        ...

    class Fav(models.Model) :
        ad = models.ForeignKey(Ad, on_delete=models.CASCADE)
        user = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)

        # https://docs.djangoproject.com/en/4.2/ref/models/options/#unique-together
        class Meta:
            unique_together = ('ad', 'user')

        def __str__(self) :
            return '%s likes %s'%(self.user.username, self.ad.title[:10])

Of course do the migrations once you have modified the model.

(2) Add the following route to your `urls.py` for the favorites feature:

    ...
    path('ad/<int:pk>/toggle', views.ToggleFavoriteView.as_view(), name='ad_toggle'),
    ...

(3) Look at how `ThingListView` from `dj4e-samples/favwc/views.py`
retrieves the list of favorites for the current user and add code
to your `AdListView` to retrieve the favorites for the current logged in user.

(4) Create the file `mkt/static/dj4e-favstar.js` and add the following Javascript code for our custom web component:

    import { html, LitElement } from "https://cdn.jsdelivr.net/npm/lit@3.2.1/+esm";

    export class DJ4EFavoriteStar extends LitElement {

        static properties = {
            fav: { type: Boolean },
        };

        // Don't use Shadow-DOM 
        createRenderRoot() { return this; }

        render() {

            return html`
            <span class="fa-stack" style="vertical-align: middle;">
                <i class="fa fa-star fa-stack-1x" 
                   style="${this.fav ? "" : "display: none;"} color: orange;">
                </i>
                <i class="fa fa-star-o fa-stack-1x"></i>
            </span>
            `
        }
    }

    customElements.define('dj4e-favstar', DJ4EFavoriteStar);


(5) Alter your `list.html` by looking through `favwc/templates/favwc/list.html`.  Make sure to add the
`dj4e-favstar` web component to show the stars based on the list of favorites for this user and the `favToggle()` JavaScript
code at the end. You will have to adapt the `dj4e-favstar` web component code in your template to work in your app.  
We will also need to load the `dj4e-favstar.js` file we created earlier into our template to use the web component. To do this, add the `load static` template tag near the top of your `list.html` template:

    ...
    {% extends "base_menu.html" %}
    {% load static %} <!-- add this -->
    {% block content %}
    ...

Then add the script tag for `dj4e-favstar.js` at the bottom of the `list.html` template:

    ...
    <script type="module" src="{% static 'dj4e-favstar.js' %}"></script> <!-- add this -->
    {% endblock %}
    ...

Again, be sure the file `dj4e-favstar.js` is in the `mkt/static/` directory so the {% static … %} helper can find it.


(6) Pull in and adapt `ToggleFavoriteView`
from `dj4e-samples/favwc/views.py` into your `views.py`.

Manual Testing
--------------

It is always a good idea to manually test your application before submitting it for grading.  Here
are a set of manual test steps:

* Make two accounts if you have not already done so
* Log in to your application on the first account
* Create an ad, view its details, update, the ad, and delete the ad (test for regression)
* Create more than one ad
* In the list view mark one ad as a favorite and then press 'refresh' and see if the star is the same
after refresh as it was when you clicked on the star
* In the list view unfavorite a favorited ad and then press 'refresh' and see if the star is the same
after refresh as it was when you clicked on the star
* Log in on the second account - make sure the favorites are not the same as the first account
* Do several favorite and unfavorite operations pressing 'refresh' after each change and make sure
the star "sticks" (i.e. has the same value as when you clicked it)

The most common problem is that when you click on the star it looks good on the screen but the
fact that this is not a favorites (or not) did not get recorded in the server.
Often you will need to check the developer network console in your browser to find errors
in the AJAX code.


Finding and Fixing Errors in the Developer Console
--------------------------------------------------

This is the first time you are using AJAX so some of the errors will only be seen in the developer
console.  If your favoriting code breaks - you won't see the errors on the main screen.  Go into the
developer console, under the network tab and watch for the AJAX (also known as XHR) calls.  Some will
fail with errors like 404 or 500 and - if you select the request that is in error and look at
the `Response` tab you will usually see what is going wrong in the server.

Sometimes the AJAX errors are a little difficult to see when 
using the <a href="dj4e_ads3/error_in_chrome.png" target="_blank">Chrome browser</a>.
The developer console in <a href="dj4e_ads3/error_in_firefox.png" target="_blank">FireFox</a>
renders the actual HTML to make it easier to read.


Things that might go wrong
--------------------------

Make sure to turn off your ad blocker.  Take a look at your web developer console if the AJAX part 
of favorites seem to fail.  You might see a message like:

    POST .. net::ERR_BLOCKED_BY_CLIENT

Or a similar message - this means your JavaScript tried to do an AJAX
request and was stopped by the browser.
