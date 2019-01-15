DJango Forms and Authentication
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

If You Are Keeping Your Projects GitHub
---------------------------------------

At this point, once your models are working, you might want to add the new files
and check your modifications into github.

    cd ~/django_projects/locallibrary/catalog
    git status
    git add ... (add files as approptiate)
    git commit -a -m "All borrowed complete"
    git push

You might also want to tag this version of the code in case you need to come back to it:

    git tag borrowed
    git push origin --tags


Adding the renew form
---------------------

* Go through the Forms tutorial - it is pretty straighforward.

* Update the template in `catalog/templates/catalog/bookinstance_list_borrowed_all.html` to call the new
renewal form from the "all bororred" view, add these three lines after the 'book-detial' line.

        ...
                <a href="{% url 'book-detail' bookinst.book.pk %}">{{bookinst.book.title}}</a>
                {% if perms.catalog.can_mark_returned %}   
                 - <a href="{% url 'renew-book-librarian' bookinst.id %}">Renew</a>  - 
                {% endif %}
                ({{bookinst.borrower}}, {{ bookinst.due_back }})
        ...

You should reload your web application and test both the 'All borrowed' view and the librarian renewal
feature.

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

