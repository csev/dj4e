# dj4e-samples/favs/urls.py

from django.urls import path, reverse_lazy
from . import views
from django.views.generic import TemplateView

# In urls.py reverse_lazy('favs:all')
# In views.py class initialization reverse_lazy('favs:all')
# In views.py methods reverse('favs:all')
# In templates {% url 'favs:thing_update' thing.id %}

app_name='favs'
urlpatterns = [
    path('', views.ThingListView.as_view(), name='all'),
    path('thing/<int:pk>', views.ThingDetailView.as_view(), name='thing_detail'),
    path('thing/create', 
        views.ThingCreateView.as_view(success_url=reverse_lazy('favs:all')), name='thing_create'),
    path('thing/<int:pk>/update', 
        views.ThingUpdateView.as_view(success_url=reverse_lazy('favs:all')), name='thing_update'),
    path('thing/<int:pk>/delete', 
        views.ThingDeleteView.as_view(success_url=reverse_lazy('favs:all')), name='thing_delete'),
    path('thing/<int:pk>/favorite', 
        views.AddFavoriteView.as_view(), name='thing_favorite'),
    path('thing/<int:pk>/unfavorite', 
        views.DeleteFavoriteView.as_view(), name='thing_unfavorite'),
]


# dj4e-samples/favs/views.py

from favs.models import Thing, Fav

from django.views import View
from django.views import generic
from django.http import HttpResponse
from django.shortcuts import render, get_object_or_404

from django.contrib.auth.mixins import LoginRequiredMixin

from myarts.owner import OwnerListView, OwnerDetailView, OwnerCreateView, OwnerUpdateView, OwnerDeleteView

class ThingListView(OwnerListView):
    model = Thing
    template_name = "favs/list.html"

    def get(self, request) :
        thing_list = Thing.objects.all()
        favorites = list()
        if request.user.is_authenticated:
            # rows = [{'id': 2}]  (A list of rows)
            rows = request.user.favorite_things.values('id')
            favorites = [ row['id'] for row in rows ]
        ctx = {'thing_list' : thing_list, 'favorites': favorites}
        return render(request, self.template_name, ctx)

class ThingDetailView(OwnerDetailView):
    model = Thing
    template_name = "favs/detail.html"

class ThingCreateView(OwnerCreateView):
    model = Thing
    fields = ['title', 'text']
    template_name = "favs/form.html"

class ThingUpdateView(OwnerUpdateView):
    model = Thing
    fields = ['title', 'text']
    template_name = "favs/form.html"

class ThingDeleteView(OwnerDeleteView):
    model = Thing
    template_name = "favs/delete.html"

# csrf exemption in class based views
# https://stackoverflow.com/questions/16458166/how-to-disable-djangos-csrf-validation
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.db.utils import IntegrityError

@method_decorator(csrf_exempt, name='dispatch')
class AddFavoriteView(LoginRequiredMixin, View):
    def post(self, request, pk) :
        print("Add PK",pk)
        t = get_object_or_404(Thing, id=pk)
        fav = Fav(user=request.user, thing=t)
        try:
            fav.save()  # In case of duplicate key
        except IntegrityError as e:
            pass
        return HttpResponse()

@method_decorator(csrf_exempt, name='dispatch')
class DeleteFavoriteView(LoginRequiredMixin, View):
    def post(self, request, pk) :
        print("Delete PK",pk)
        t = get_object_or_404(Thing, id=pk)
        try:
            fav = Fav.objects.get(user=request.user, thing=t).delete()
        except Fav.DoesNotExist as e:
            pass

        return HttpResponse()



# dj4e-samples/favs/templates/favs/list.html


{% extends "base_bootstrap.html" %}
{% block content %}
<h1>Favorite Things</h1>
<p>
{% if thing_list %}
<ul>
  {% for thing in thing_list %}
    <li>
        <a href="{% url 'favs:thing_detail'  thing.id %}">{{ thing.title }}</a>
        {% if thing.owner_id == user.id %}
        (<a href="{% url 'favs:thing_update' thing.id %}">Edit</a> |
        <a href="{% url 'favs:thing_delete' thing.id %}">Delete</a>)
        {% endif %}
        {% if user.is_authenticated %}
        <!-- Two hrefs with two stacked icons each - one showing and one hidden -->
        <a href="#" onclick=
            "favPost('{% url 'favs:thing_unfavorite' thing.id %}', {{ thing.id }} );return false;"
            {% if thing.id not in favorites %} style="display: none;" {% endif %}
            id="favorite_star_{{thing.id}}">
        <span class="fa-stack" style="vertical-align: middle;">
        <i class="fa fa-star fa-stack-1x" style="color: orange;"></i>
        <i class="fa fa-star-o fa-stack-1x"></i>
        </span>
        </a>
        <!-- the second href -->
        <a href="#" onclick=
             "favPost('{% url 'favs:thing_favorite' thing.id %}', {{ thing.id }} );return false;"
            {% if thing.id in favorites %} style="display: none;" {% endif %}
            id="unfavorite_star_{{thing.id}}">
        <span class="fa-stack" style="vertical-align: middle;">
        <i class="fa fa-star fa-stack-1x" style="display: none; color: orange;"></i>
        <i class="fa fa-star-o fa-stack-1x"></i>
        </span>
        </a>
        {% endif %}
    </li>
  {% endfor %}
</ul>
{% else %}
  <p>There are no things in the database.</p>
{% endif %}
</p>
<p>
<a href="{% url 'favs:thing_create' %}">Add a Thing</a> |
{% if user.is_authenticated %}
<a href="{% url 'logout' %}?next={% url 'favs:all' %}">Logout</a>
{% else %}
<a href="{% url 'login' %}?next={% url 'favs:all' %}">Login</a>
{% endif %}
</p>
<script>
function favPost(url, thing_id) {
    console.log('Requesting JSON');
    $.post(url, {},  function(rowz){
        console.log(url, 'finished');
        $("#unfavorite_star_"+thing_id).toggle();
        $("#favorite_star_"+thing_id).toggle();
    }).fail(function(xhr) {
        console.log(url, 'error', xhr.status);
    });
}
</script>
{% endblock %}
