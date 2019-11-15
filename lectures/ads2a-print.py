
# django_projects/chucklist/ads2/urls.py


from django.urls import path, reverse_lazy
from . import views
from django.views.generic import TemplateView

# Note "ads2" should be "ads" everywhere in student projects
app_name='ads2'
urlpatterns = [
    path('', views.AdListView.as_view()),
    path('ads', views.AdListView.as_view(), name='all'),
    path('ad/<int:pk>', views.AdDetailView.as_view(), name='ad_detail'),
    path('ad/create', 
        views.AdCreateView.as_view(success_url=reverse_lazy('ads2:all')), name='ad_create'),
    path('ad/<int:pk>/update', 
        views.AdUpdateView.as_view(success_url=reverse_lazy('ads2:all')), name='ad_update'),
    path('ad/<int:pk>/delete', 
        views.AdDeleteView.as_view(success_url=reverse_lazy('ads2:all')), name='ad_delete'),
    path('ad/<int:pk>/comment',
        views.CommentCreateView.as_view(), name='comment_create'),
    path('comment/<int:pk>/delete',
        views.CommentDeleteView.as_view(success_url=reverse_lazy('ads2')), name='comment_delete'),
    path('ad_picture/<int:pk>', views.stream_file, name='picture'),
]



# django_projects/chucklist/ads2/views.py

from ads2.models import Ad, Comment
from ads2.forms import CommentForm, CreateForm

# Note - "ads2" should be "ads" in student projects

from django.views import View
from django.views import generic
from django.http import HttpResponse
from django.urls import reverse_lazy
from django.shortcuts import render, get_object_or_404, redirect
from django.contrib.auth.mixins import LoginRequiredMixin

from ads.owner import OwnerListView, OwnerDetailView, OwnerCreateView, OwnerUpdateView, OwnerDeleteView

from ads.misc import cleanup

class AdListView(OwnerListView):
    model = Ad
    template_name = "ads2/list.html"

    def get_queryset(self):
        cleanup(self.model)
        return super(AdListView, self).get_queryset()

class AdDetailView(OwnerDetailView):
    model = Ad
    template_name = "ads2/detail.html"
    def get(self, request, pk) :
        ad = Ad.objects.get(id=pk)
        comments = Comment.objects.filter(ad=ad).order_by('-updated_at')[:20]
        comment_form = CommentForm()
        context = { 'ad' : ad, 'comments': comments, 'comment_form': comment_form }
        return render(request, self.template_name, context)

# We can't extend OwnerCreateView because we have a special form and process for files
class AdCreateView(LoginRequiredMixin, View):
    template = 'ads2/form.html'
    success_url = None   # See urls.py
    def get(self, request) :
        form = CreateForm()
        ctx = { 'form': form }
        return render(request, self.template, ctx)

    def post(self, request, pk=None) :
        form = CreateForm(request.POST, request.FILES or None)
        if not form.is_valid() :
            ctx = {'form' : form}
            return render(request, self.template, ctx)

        # Adjust the model owner before saving
        inst = form.save(commit=False)
        inst.owner = self.request.user
        inst.save()
        return redirect(self.success_url)

class AdUpdateView(LoginRequiredMixin, View):
    template = 'ads2/form.html'
    success_url = None   # See urls.py
    def get(self, request, pk) :
        inst = get_object_or_404(Ad, id=pk, owner=self.request.user)
        form = CreateForm(instance=inst)
        ctx = { 'form': form }
        return render(request, self.template, ctx)

    def post(self, request, pk=None) :
        inst = get_object_or_404(Ad, id=pk, owner=self.request.user)
        form = CreateForm(request.POST, request.FILES or None, instance=inst)

        if not form.is_valid() :
            ctx = {'form' : form}
            return render(request, self.template, ctx)

        # Adjust the model owner before saving
        inst = form.save(commit=False)
        inst.owner = self.request.user
        inst.save()
        return redirect(self.success_url)

class AdDeleteView(OwnerDeleteView):
    model = Ad
    template_name = "ads2/delete.html"

class CommentCreateView(View):
    def post(self, request, pk) :
        a = get_object_or_404(Ad, id=pk)
        comment_form = CommentForm(request.POST)

        comment = Comment(text=request.POST['comment'], owner=request.user, ad=a)
        comment.save()
        return redirect(reverse_lazy('ads2:ad_detail', args=[pk]))

class CommentDeleteView(OwnerDeleteView):
    model = Comment
    template_name = "ads2/comment_delete.html"

    # https://stackoverflow.com/questions/26290415/deleteview-with-a-dynamic-success-url-dependent-on-id
    def get_success_url(self):
        ad = self.object.ad
        return reverse_lazy('ads2:ad_detail', args=[ad.id])

def stream_file(request, pk) :
    ad = get_object_or_404(Ad, id=pk)
    response = HttpResponse()
    response['Content-Type'] = ad.content_type
    response['Content-Length'] = len(ad.picture)
    response.write(ad.picture)
    return response


# django_projects/chucklist/ads2/templates/ads2/base_menu.html

{% extends 'base_bootstrap.html' %}
{# Note that 'ads2' should be 'ads' in student projects #}
{% block navbar %}
{% load app_tags %}
<!-- https://www.w3schools.com/booTsTrap/bootstrap_navbar.asp -->
<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
        <a class="navbar-brand" href="{% url 'ads2:all' %}">{{ settings.APP_NAME }}</a>
    </div>
    <!-- https://stackoverflow.com/questions/22047251/django-dynamically-get-view-url-and-check-if-its-the-current-page -->
    <ul class="nav navbar-nav">
      {% url 'ads2:all' as x %}
      <li {% if request.get_full_path == x %}class="active"{% endif %}>
          <a href="{% url 'ads2:all' %}">Ads</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        {% if user.is_authenticated %}
        <li>
        <a href="{% url 'ads2:ad_create' %}">Create Ad</a>
        </li>
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle"><img style="width: 25px;" src="{{ user|gravatar:60 }}"/><b class="caret"></b></a>
        <ul class="dropdown-menu">
            <li><a href="{% url 'logout' %}?next={% url 'ads2:all' %}">Logout</a></li>
        </ul>
        {% else %}
        <li>
        <a href="{% url 'login' %}?next={% url 'ads2:all' %}">Login</a>
        </li>
        {% endif %}
    </ul>
  </div>
</nav>
{% endblock %}
{% block content %}
<h4>I am content in {{ request.path }} base_menu.html</h4>
<p>
Request_path: {{ request.path }}
</p>
{% endblock %}


# django_projects/chucklist/ads2/templates/ads2/form.html

{% extends "ads2/base_menu.html" %}
  
{% block content %}
<p>
{% load crispy_forms_tags %}
    <form action="" method="post" id="upload_form" enctype="multipart/form-data">
    {% csrf_token %}
    {{ form|crispy }}
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="{% url 'ads2:all' %}" class="btn btn-secondary">Cancel</a>
  </form>
</p>
<!-- https://stackoverflow.com/questions/2472422/django-file-upload-size-limit -->
<script>
$("#upload_form").submit(function() {
  console.log('Checking file size');
  if (window.File && window.FileReader && window.FileList && window.Blob) {
      var file = $('#id_{{ form.upload_field_name }}')[0].files[0];
      if (file && file.size > {{ form.max_upload_limit }} ) {
          alert("File " + file.name + " of type " + file.type + " must be < {{ form.max_upload_limit_text }}");
      return false;
    }
  }
});
</script>
{% endblock %}

