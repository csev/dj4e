Django Detail Pages
===================

In this assignment we will add detail pages for books and authors.

https://developer.mozilla.org/en-US/docs/Learn/Server-side/Django/Generic_views

If you are submitting this assignment to the DJ4E autograder for this assignment,
you should check the autograder for specific instructions that
the autograder requires for this assignment.

Complete the following sections of the Views tutorial:

* Go into the `catalog` application

        cd ~/django_projects/locallibrary/catalog

* Edit `catalog/urls.py` and add the `BookListView` and `BookDetailView` lines to the `urlpatterns` list.

        urlpatterns = [
            path('', views.index, name='index'),
            path('books/', views.BookListView.as_view(), name='books'),
            path('book/<int:pk>', views.BookDetailView.as_view(), name='book-detail'),
        ]

* Edit `catalog/views.py` for the views.index as suggested in the tutorial, adding these lines

        from django.views import generic

        class BookListView(generic.ListView):
            model = Book
            paginate_by = 2

        class BookDetailView(generic.DetailView):
            model = Book

    Since list and detail views are so common in programming Django provides us classes for us to reuse/extend.
    This saves us a lot of typing almost the same thing over and over again.

* You will have to make the `catalog` folder under the `templates` folder if it does not already exist

        cd ~/django_projects/locallibrary/catalog
        mkdir templates/catalog

* Create the files `catalog/templates/catalog/book_list.html` and `catalog/templates/catalog/book_detail.html` as per
    the tutorial.

* Add pagination the pagination code to `templates/base_generic.html` and make sure you have at least
three books so you can set the pagination to two in the view and then test pagination.
Once you test the pagination set `paginate_by = 20` so the autograder finds your books.

* Reload your application under the `Web` tab in
<a href="https://www.pythonanywhere.com" target="_blank">PythonAnywhere</a>

* Visit the catalog site
<a href="http://mdntutorial.pythonanywhere.com/catalog" target="_blank">http://mdntutorial.pythonanywhere.com/catalog</a>
and explore the list and detail views.

* You do not have to do the the "Challenge yourself" portion at the end of the tutorial.
If you do, in the `templates/catalog/author_detail.html` file when
you want to loop through all the books for a particular author, use the following pattern.

        {% for book in author.book_set.all %}
            ...
        {% endfor %}

    The rough translation of this statement is, 'For the current author get a set of all the
    books written by that author.

References
----------

https://docs.djangoproject.com/en/5.2/ref/class-based-views/generic-display/

https://docs.djangoproject.com/en/5.2/topics/db/queries/#following-relationships-backward

