
# dj4e-samples/forums/models.py

from django.db import models
from django.core.validators import MinLengthValidator
from django.contrib.auth.models import User
from django.conf import settings

class Forum(models.Model) :
    title = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(5, "Title must be greater than 5 characters")]
    )
    text = models.TextField()
    owner = models.ForeignKey(settings.AUTH_USER_MODEL, 
        on_delete=models.CASCADE, related_name='forums_owned')
    comments = models.ManyToManyField(settings.AUTH_USER_MODEL, 
        through='Comment', related_name='forum_comments')

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    # Shows up in the admin list
    def __str__(self):
        return self.title

class Comment(models.Model) :
    text = models.TextField(
        validators=[MinLengthValidator(3, "Comment must be greater than 3 characters")]
    )

    forum = models.ForeignKey(Forum, on_delete=models.CASCADE)
    owner = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    # Shows up in the admin list
    def __str__(self):
        if len(self.text) < 15 : return self.text
        return self.text[:11] + ' ...'


# dj4e-samples/forums/forms.py

from django import forms
from django.core.exceptions import ValidationError
from django.core import validators

# strip means to remove whitespace from the beginning and the end before storing the column
class CommentForm(forms.Form):
    comment = forms.CharField(required=True, max_length=500, min_length=3, strip=True)


# dj4e-samples/forums/views.py

from forums.models import Forum, Comment

from django.views import View
from django.views import generic
from django.shortcuts import render, get_object_or_404, redirect
from django.urls import reverse

from django.contrib.auth.mixins import LoginRequiredMixin

from forums.forms import CommentForm
from myarts.owner import OwnerListView, OwnerDetailView, OwnerCreateView, OwnerUpdateView, OwnerDeleteView

class ForumListView(OwnerListView):
    model = Forum
    template_name = "forums/list.html"

class ForumDetailView(OwnerDetailView):
    model = Forum
    template_name = "forums/detail.html"
    def get(self, request, pk) :
        forum = Forum.objects.get(id=pk)
        comments = Comment.objects.filter(forum=forum).order_by('-updated_at')
        comment_form = CommentForm()
        context = { 'forum' : forum, 'comments': comments, 'comment_form': comment_form }
        return render(request, self.template_name, context)


class ForumCreateView(OwnerCreateView):
    model = Forum
    fields = ['title', 'text']
    template_name = "forums/form.html"

class ForumUpdateView(OwnerUpdateView):
    model = Forum
    fields = ['title', 'text']
    template_name = "forums/form.html"

class ForumDeleteView(OwnerDeleteView):
    model = Forum
    template_name = "forums/delete.html"

class CommentCreateView(LoginRequiredMixin, View):
    def post(self, request, pk) :
        f = get_object_or_404(Forum, id=pk)
        comment_form = CommentForm(request.POST)

        comment = Comment(text=request.POST['comment'], owner=request.user, forum=f)
        comment.save()
        return redirect(reverse('forums:forum_detail', args=[pk]))

class CommentDeleteView(OwnerDeleteView):
    model = Comment
    template_name = "forums/comment_delete.html"

    # https://stackoverflow.com/questions/26290415/deleteview-with-a-dynamic-success-url-dependent-on-id
    def get_success_url(self):
        forum = self.object.forum
        return reverse('forums:forum_detail', args=[forum.id])


# dj4e-samples/forums/templates/forums/detail.html

{% extends "base_bootstrap.html" %}
{% load crispy_forms_tags %}
{% load humanize %} <!-- https://docs.djangoproject.com/en/3.0/ref/contrib/humanize -->
{% block content %}
<h1>
{% if forum.owner == user %}
<span style="float: right;">
<a href="{% url 'forums:forum_update' forum.id %}"><i class="fa fa-pencil"></i></a>
<a href="{% url 'forums:forum_delete' forum.id %}"><i class="fa fa-trash"></i></a>
</span>
{% endif %}
{{ forum.title }}
</h1>
<p>
{{ forum.text }}
</p>
<p>
({{ forum.updated_at|naturaltime }})
</p>
{% if user.is_authenticated %}
<br clear="all"/>
<p>
<form method="post" action="{% url 'forums:forum_comment_create' forum.id %}">
    {% csrf_token %}
    {{ comment_form|crispy }}
<input type="submit" value="Submit">
<input type="submit" value="All Forums" onclick="window.location.href='{% url 'forums:all' %}';return false;">
</form>
</p>
{% endif %}
{% for comment in comments %}
<p> {{ comment.text }} 
({{ comment.updated_at|naturaltime }})
{% if user == comment.owner %}
<a href="{% url 'forums:forum_comment_delete' comment.id %}"><i class="fa fa-trash"></i></a>
{% endif %}
</p>
{% endfor %}
{% endblock %}


