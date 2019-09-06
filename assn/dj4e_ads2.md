Classified Ad Web Site - Milestone 2
====================================

In this assignment, you will expand your classified ads web site to add functionality
equivalent to:

https://chucklist.dj4e.com/m2

The primary additions from the previous milestone are to add an image to each ad
and add comments for each ad.

You will build this application by borrowing parts and pieces from the code that runs

https://samples.dj4e.com/

and combining them into a single application.

Updating Requirements
---------------------

(1) Do a `git pull` in your `dj4e-samples` repo and make sure all the pre-requisites are
installed.  Copy `dj4e-samples/requirements.txt` to `adlist/requirements.txt` and launch a
shell in your virtual environment and run:

    pip install -r requirements.txt   # or pip3

On PythonAnywhere under the Web tab there is a link to launch a shell into your
virtual environment.  It is important to be in the virtual environment so the
installed libraries end up in the right place.

Do Some or All of the Challenges
---------------------------------

You will have to finish these by the next assignment - so you might as well work on them now.
And they are fun.

(1) Make yourself a gravatar at https://en.gravatar.com/ - it is super easy and you will see your
avatar when you log in in your application and elsewhere with gravatar enabled apps.  The gravatar can be
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


Adding Pictures to the Ads Application
--------------------------------------

In this section, you will pull bits and pieces of the `pics` sample application
into your `ads` application to add support for an optional single picture per ad.

(1) Add this to your `ads/model.py`, talking inspiration from `samples/pics/models.py`

    class Ad(models.Model) :

        ...
        # Picture
        picture = models.BinaryField(null=True, editable=True)
        content_type = models.CharField(max_length=256, null=True, help_text='The MIMEType of the file')
        ...

Do not include the entire `Pic` model.  Of course do the migrations once you have modified the model.

(2) Copy in the `pics/forms.py` as well as `pics/humanize.py`.

(3) Take a look at `pics/views.py` and adapt the patterns in `PicCreateView` and
`PicUpdateView` and replace `AdCreateView` and `AdUpdateView` in `ads/views.py`.
These new classes completely replace the classes that you had from the previous assignment.
These new views don't inherit from owner.py.

(4) Alter your `templates/ads/ad_form.html` by looking through `pics/templates/pics/form.html`.  Make sure to add the
JavaScript bits at the end and add `enctype="multipart/form-data"` and the `id`
attribute to the `form` tag.

(5) Alter the `templates/ads/ad_detail.html` template by looking through `pics/templates/pics/detail.html` and
to add code to include the image in the output if there is an image associated with the ad.
Make sure not to lose the `price` field in your UI.  If you don't see the `price` field
in your UI it is likely a mistake in your `forms.py`.

(6) Add a `ad_picture` route to your `urls.py` based on the `pics_picture` route from `pics/urls.py`:

    path('ad_picture/<int:pk>', views.stream_file, name='ad_picture'),

(5) Add the `stream_file` view from `pics/views.py` and adapt appropriately

Test to make sure you can upload, view, and update pictures with your ads and if you are using github,
once this is all working, you might want to check this in before you start on the next step.

Adding Comments to the Ads Application
--------------------------------------

In this section, you will pull bits and pieces of the `forum` sample application
into your `ads` application to add support for an optional single picture per ad.

(1) Update your `models.py` adding the comment feature from the `forums\models.py`

    class Ad(models.Model) :

        ...
        owner = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)
        comments = models.ManyToManyField(settings.AUTH_USER_MODEL,
            through='Comment', related_name='comments_owned')
        ...

    class Comment(models.Model) :
        text = models.TextField(
            validators=[MinLengthValidator(3, "Comment must be greater than 3 characters")]
        )

        ad = models.ForeignKey(Ad, on_delete=models.CASCADE)
        owner = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)

        created_at = models.DateTimeField(auto_now_add=True)
        updated_at = models.DateTimeField(auto_now=True)

        # Shows up in the admin list
        def __str__(self):
            if len(self.text) < 15 : return self.text
            return self.text[:11] + ' ...'

Do not add the Forum model - simply connect the `Comment` model to the `Ad` model. Of course do
the migrations once you have modified the model successfully.

(2) Merge the `CommentForm` from `forums/forms.py` into your `forms.py`.

(3) Adapt the techniques in the `ForumDetailView` into your `AdDetailView` to retrieve the comments to
pass into the `templates/ads/ad_detail.html` template through the context.

(4) Adapt the `templates/ads/ad_detail.html` template to show comments with a delete icon when a comment belongs
to the current logged in user.

(5) Also add the ability to add a comment to an ad in `ad_detail.html` when the user is logged in by looking
at the techniques in `forums/templates/forums/detail.html`.

(6) Add a route in `urls.py` for the `ad_comment_create` and `ad_comment_delete`
routes from `forums/urls.py`.  Make sure to use the same URL patterns as shown here:

    urlpatterns = [
        ...
        path('ad/<int:pk>/comment',
            views.CommentCreateView.as_view(), name='ad_comment_create'),
        path('comment/<int:pk>/delete',
            views.CommentDeleteView.as_view(success_url=reverse_lazy('ads')), name='ad_comment_delete'),
]

(7) Adapt the comment related views from `forums/views.py` and put them into your `view.py`.

(8) You will have to adapt the `templates/forums/comment_delete.html` template to work in your ads application.


