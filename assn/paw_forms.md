DJango Forms and Authentication
===============================

In this tutorial, we will add a 'renew' Form that is usable by librarians:

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Forms

Once that is done, we will go back to the previous tutorial and add a view
for librarians to see checked out books and link it into the new form.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Authentication#Challenge_yourself

And once that is done, we will complete the Forms tutorial:

Adding the renew form
---------------------

Go through the Forms tutorial.  In the `views.py` in the `renew_book_librarian` view,
comment out one of the provided lines and replace it as follows:

        # return HttpResponseRedirect(reverse('all-borrowed') )                                                             
        return HttpResponseRedirect(reverse('index') )                                                             

Once you finish the `templates/catalog/book_renew_librarian.html` task and get to
the "Testing the page", you will have to hand construct the URL to see your new
user interface.   To do this go into the 'Admin' and look at a book instance that has been checked out
and grab the "id" of the BookInstance and make a URL that looks like the following with the book's ID:

http://mdntutorial.pythonanywhere.com/catalog/book/31cf12c7-6b83-4bb4-8ffd-6c0058a044ba/renew/

You should be able to update the due date and verify that the new date is in the database
by loking at the BookInstance using the `admin` interface.

Once you have verified that the new form is working, change the `views.py` back to:

        return HttpResponseRedirect(reverse('all-borrowed') )                                                             
        # Delete this -> return HttpResponseRedirect(reverse('index') )                                                             
Once you restore this, when you update the due date it will update, but you will get a message
stating `Reverse for 'all-borrowed' not found.` when you press the `Submit` button.  It is OK
we will create a view with a name of `all borrowed` in the next part of this assignment.

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

* Create the template in `templates/catalog/bookinstance_list_borrowed_all.html`

        {% extends "base_generic.html" %}
        {% block content %}
            <h1>Borrowed books</h1>
            {% if bookinstance_list %}
            <ul>
            {% for bookinst in bookinstance_list %}
            <li class="{% if bookinst.is_overdue %}text-danger{% endif %}">
                <a href="{% url 'book-detail' bookinst.book.pk %}">{{bookinst.book.title}}</a>
                {% if perms.catalog.can_mark_returned %}-
                 <a href="{% url 'renew-book-librarian' bookinst.id %}">Renew</a>  - 
                {% endif %}
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

If You Are Keeping Your Projects GitHub
---------------------------------------

At this point, once your models are working, you might want to add the new files
and check your modifications into github.

    cd ~/django_projects/locallibrary/catalog
    git status
    git add ... (add files as approptiate)
    git commit -a -m "Forms tutorial complete"
    git push

You might also want to tag this version of the code in case you need to come back to it:

    git tag forms
    git push origin --tags


References
----------

https://docs.djangoproject.com/en/2.1/ref/class-based-views/generic-display/

https://docs.djangoproject.com/en/2.0/topics/db/queries/#following-relationships-backward

