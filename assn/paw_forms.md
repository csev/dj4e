Django Forms and Authentication
===============================

For this task, we will go back to the previous tutorial and add a view
for librarians to see checked all checked out books.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Authentication#Challenge_yourself

Then we will complete the Forms tutorial:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Forms

Adding the librarian view
-------------------------

This is a set of steps to walk through the challenge that is not included in the MDN tutorial.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Authentication#Challenge_yourself

* In `catalog/models.py`, update the `BookInstance` model and add the permissions line:

        class Meta:
            ordering = ['due_back']
            permissions = (("can_mark_returned", "Set book as returned"),)

* Then run your migration

        cd ~/django_projects/locallibrary
        python3 manage.py makemigrations
        python3 manage.py migrate

* Reload your application, go into the admin page -> Groups and change the 'Library Staff' group
and add the `catalog | book instance | Set book as returned` permission to the group and save the group.

* Add the following to the `urls.py` (this is where we define 'all-borrowed' to fix the error above):

        urlpatterns += [
            path('borrowed/', views.LoanedBooksListView.as_view(), name='all-borrowed'),
        ]

* Add the following to `catalog/views.py`:

        from django.contrib.auth.mixins import PermissionRequiredMixin

        class LoanedBooksListView(PermissionRequiredMixin,generic.ListView):
            """Generic class-based view listing books on loan to current user."""
            model = BookInstance
            permission_required = 'catalog.can_mark_returned'
            template_name ='catalog/bookinstance_list_borrowed_all.html'
            paginate_by = 10

            def get_queryset(self):
                return BookInstance.objects.filter(status__exact='o').order_by('due_back')

    Note that `get_queryset()` is quite similar to the same method in `LoadedBooksByUserListView`
    except that we have removed the filter by logged in user.

* Create the template in `catalog/templates/catalog/bookinstance_list_borrowed_all.html`

        {% extends "base_generic.html" %}
        {% block content %}
            <h1>Borrowed books</h1>
            {% if bookinstance_list %}
            <ul>
            {% for bookinst in bookinstance_list %}
            <li class="{% if bookinst.is_overdue %}text-danger{% endif %}">
                <a href="{% url 'book-detail' bookinst.book.pk %}">{{bookinst.book.title}}</a>
                ({{bookinst.borrower}}, {{ bookinst.due_back }})
            </li>
            {% endfor %}
            </ul>
            {% else %}
            <p>There are no books borrowed.</p>
            {% endif %}
        {% endblock %}

* Add this to `templates/base_generic.html` right below 'My Borrowed'

        {% if perms.catalog.can_mark_returned %}
        <li><a href="{% url 'all-borrowed'%}">All Borrowed</a></li>   
        {% endif %}

* Then `Reload` the application and log in with an account that is in the group 'Library Staff'
and verify that the 'All borrowed` code works.

* If you are using git, you should add and commit all your files and tag your repo
with a tag like "borrowed"

Adding the renew form
---------------------

Now that we have the 'All borrowed view' lets make some forms to support some librarian use cases.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Forms

* Create `catalog/forms.py` as described in the tutorial

* Add a pattern to `catalog/urls.py` for the `renew_book_librarian` view

* Add the `renew_book_librarian` view to `catalog/views.py` as described in the tutorial

* Add the `catalog/templates/catalog/book_renew_librarian.html` template as described in tht tutorial

* Update the template in `catalog/templates/catalog/bookinstance_list_borrowed_all.html` to call the new
renewal form from the "all borrowed" view, add these three lines after the 'book-detial' line.

        ...
                <a href="{% url 'book-detail' bookinst.book.pk %}">{{bookinst.book.title}}</a>
                {% if perms.catalog.can_mark_returned %}   
                 - <a href="{% url 'renew-book-librarian' bookinst.id %}">Renew</a>  - 
                {% endif %}
                ({{bookinst.borrower}}, {{ bookinst.due_back }})
        ...

* You should reload your web application and test both the 'All borrowed' view and the librarian renewal
feature.  Make sure when you change the due date that it actually changes.  Try some invalid dates like
next year to test data validation.

* If your code is working to this point, and you are using git you might want to commit
it all and tag it with a tag like "renew"

        git add catalog/forms.py catalog/templates/catalog/*
        git commit -a -m "Adding the first form"
        git push
        git tag renew
        git push --tags

The tutorial shows an equivalent way of building a model-based view.  It is not necessary to actually build
this equivalent form.

Adding the CRUD views
---------------------

Continuing with the Forms tutorial, we add three CRUD views:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Forms#Generic_editing_views

* Add the Create Update, and Delete views to `views.py` as described in the tutorial.  But we want to
only allow logged in users to access these views.  If you want to take the extra step, you can create a new permission
and use the `PermissionRequiredMixin` to be more fine-grained.  At a minimum, make sure that you have to be
logged in to do the CRUD operations.  

    Simply add the `LoginRequiredMixin` to each of your classes (we imported it earlier in the file)

        class AuthorCreate(LoginRequiredMixin,CreateView):
            model = Author
            fields = '__all__'
            initial = {'date_of_death': '05/01/2018'}

    __You don't want to leave a web site up with some 
    wide-open URLs that accept data from the internet without a log in - that would be very irresponsible
    and make your hosting provider very unhappy.__
        
* Create the `catalog/templates/catalog/author_form.html` template as described in the tutorial

* Create the `catalog/templates/catalog/author_confirm_delete.html` template as described in the tutorial

* Add the url mappings for the Create Update, and Delete views `urls.py` as described in the tutorial

* Test that you can Create, Update, and Delete by logging in as a valid user and manually going to the URLs:

        http://mdntutorial.pythonanywhere.com/catalog/author/create/
        http://mdntutorial.pythonanywhere.com/catalog/author/12/update
        http://mdntutorial.pythonanywhere.com/catalog/author/12/delete

    You can go into an author detail page and add "update" or "delete" to the end of the URL to access
    those views.

* If your code is working to this point, and you are using git you might want to commit
it all and tag it with a tag like "crud"

Congratulations
---------------

If you have made it this far, you have seen a lot of the patterns used in developing a Django application.
Hopefully you learned some of it along the way as you move into building your own applications from scratch
going forward.
