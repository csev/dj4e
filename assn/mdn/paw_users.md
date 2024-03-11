Django Users and Authentication
===============================

In this assignment we will add some non-admin users, and the code for them to log in and log out to
the LocalLibrary application.   Then we will add data model elements to represent book checkout and a screen
so users can see the books that they have checked out.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Authentication

This is a more complex tutorial so allow plenty of time to work on this tutorial.

* You will need an admin user for this assignment, if you have deleted the super user, and need to
create another one, use the following commands in a console shell:

        python manage.py createsuperuser

* Make a new user (check the autograder for specific instructions) and add it to Library Members

* Add the account authentication URLs - make sure to reload your application after you change
the `urls.py` file.  The MDN tutorial does not remind you each time you need to Reload the application.
Tend toward reloading too often versus not too often.  When in doubt, reload :)
If you are developing locally, it auto-reloads quite a bit but sometimes it is
helpful to abort and restart the `runserver` command.

* Update `settings.py` to add a reference to the new project-wide `templates` folder - Make
sure to reload your application and test.  When you change configuration files - you might break your entire
application so you want to reload on *each* configuration change so you can quickly figure out what went
wrong and fix it.  You can use `git diff` to see what you changes  you have made and if something goes wrong,
you can always revert a file to your previous version and re-make your changes.

        get checkout locallibrary/settings.py

    Lean on git to keep track of what you have and have not done.

* After you update `urls.py`, add the new templates folders

        cd ~/django_projects/locallibrary
        mkdir templates
        mkdir templates/registration

* `Reload` your application and go to a URL like

        https://mdntutorial.pythonanywhere.com/accounts/login/

    You will get a 'TemplateDoesNotExist' error - which is correct because the new templates are not there yet.
    It should show uyou the name of the template it is looking for.


* Add the template for `login.html` in the correct folder as described in the tutorial.  If you successfully
log in without doing the next step - you will be redirected to `/accounts/profile/` - we need to
change this in the next step.

* Add the `LOGIN_REDIRECT_URL` to `locallibrary/locallibrary/settings.py` as directed in the tutorial and
reload your application.

        LOGIN_REDIRECT_URL = '/'

    At this point a successful login should go to the '/catalog/' url.

* You can delay doing the four templates for `password_reset` until later - the autograder won't check these

* Update `base_generic.html` to add the logged in indication as well as Login and Logout links

* Complete the <a href="https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Authentication#Example_%E2%80%94_listing_the_current_user's_books" target="_blank">Example — listing the current user's books</a>
task - this is pretty intricate and will take some time.  This entails a change to the `models.py` and
a migration in the command line.  Once you have changed the `models.py` file, running the following commands
to update the database schema:

        cd ~/django_projects/locallibrary/
        python manage.py makemigrations
        python manage.py migrate

* Update the `catalog/admin.py` file to add code to the `BookInstance` model and reload your application.

* Then log into the `/admin' user interface in your application.  Create some new book instances
(the imprint can be any string) some instances out to a user.  Check out several book instances to different users.
Make sure
to set the status to "On Loan".

* Edit the `catalog/views.py` file and add the `LoanedBooksByUserListView` view.

* Edit the `catalog/urls.py` and add the url pattern for the `LoanedBooksByUserListView`.

* Edit `/catalog/templates/base_generic.html` and add the link to `my-borrowed`

* Restart your web application.

* Check to see if your logged in user can see their own borrowed books.   Make sure the user's book
instances have a status of "On Loan".

