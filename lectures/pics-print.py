# dj4e-samples/pics/urls.py

from django.urls import path, reverse_lazy
from . import views
from django.views.generic import TemplateView

app_name='pics'

urlpatterns = [
    path('', views.PicListView.as_view(), name='all'),
    path('pic/<int:pk>', views.PicDetailView.as_view(), name='pic_detail'),
    path('pic/create', 
        views.PicCreateView.as_view(success_url=reverse_lazy('pics:all')), name='pic_create'),
    path('pic/<int:pk>/update', 
        views.PicUpdateView.as_view(success_url=reverse_lazy('pics:all')), name='pic_update'),
    path('pic/<int:pk>/delete', 
        views.PicDeleteView.as_view(success_url=reverse_lazy('pics:all')), name='pic_delete'),
    path('pic_picture/<int:pk>', views.stream_file, name='pic_picture'),

]


# dj4e-samples/pics/models.py

from django.db import models
from django.core.validators import MinLengthValidator
from django.conf import settings

class Pic(models.Model) :
    title = models.CharField(
            max_length=200,
            validators=[MinLengthValidator(2, "Title must be greater than 2 characters")]
    )
    text = models.TextField()
    owner = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE)

    # Picture
    picture = models.BinaryField(null=True, editable=True)
    content_type = models.CharField(max_length=256, null=True, help_text='The MIMEType of the file')

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    # Shows up in the admin list
    def __str__(self):
        return self.title


# dj4e-samples/pics/forms.py

from django import forms
from pics.models import Pic
from django.core.files.uploadedfile import InMemoryUploadedFile
from pics.humanize import naturalsize

# https://docs.djangoproject.com/en/2.1/topics/http/file-uploads/
# https://stackoverflow.com/questions/2472422/django-file-upload-size-limit
# https://stackoverflow.com/questions/32007311/how-to-change-data-in-django-modelform
# https://docs.djangoproject.com/en/2.1/ref/forms/validation/#cleaning-and-validating-fields-that-depend-on-each-other

# Create the form class.
class CreateForm(forms.ModelForm):
    max_upload_limit = 2 * 1024 * 1024
    max_upload_limit_text = naturalsize(max_upload_limit)

    # Call this 'picture' so it gets copied from the form to the in-memory model
    # It will not be the "bytes", it will be the "InMemoryUploadedFile"
    # because we need to pull out things like content_type
    picture = forms.FileField(required=False, label='File to Upload <= '+max_upload_limit_text)
    upload_field_name = 'picture'

    class Meta:
        model = Pic
        fields = ['title', 'text', 'picture']  # Picture is manual

    # Validate the size of the picture
    def clean(self) :
        cleaned_data = super().clean()
        pic = cleaned_data.get('picture')
        if pic is None : return
        if len(pic) > self.max_upload_limit:
            self.add_error('picture', "File must be < "+self.max_upload_limit_text+" bytes")
            
    # Convert uploaded File object to a picture
    def save(self, commit=True) :
        instance = super(CreateForm, self).save(commit=False)

        # We only need to adjust picture if it is a freshly uploaded file
        f = instance.picture   # Make a copy
        if isinstance(f, InMemoryUploadedFile):  # Extract data from the form to the model
            bytearr = f.read();
            instance.content_type = f.content_type
            instance.picture = bytearr  # Overwrite with the actual image data

        if commit:
            instance.save()

        return instance


# dj4e-samples/pics/views.py

from django.views import View
from django.shortcuts import render, redirect, get_object_or_404
from django.urls import reverse_lazy
from django.http import HttpResponse
from django.contrib.auth.mixins import LoginRequiredMixin

from django.core.files.uploadedfile import InMemoryUploadedFile

from myarts.owner import OwnerListView, OwnerDetailView, OwnerCreateView, OwnerUpdateView, OwnerDeleteView

from pics.models import Pic
from pics.forms import CreateForm

class PicListView(OwnerListView):
    model = Pic
    template_name = "pics/list.html"

class PicDetailView(OwnerDetailView):
    model = Pic
    template_name = "pics/detail.html"

class PicCreateView(LoginRequiredMixin, View):
    template = 'pics/form.html'
    success_url = reverse_lazy('pics:all')
    def get(self, request, pk=None) :
        form = CreateForm()
        ctx = { 'form': form }
        return render(request, self.template, ctx)

    def post(self, request, pk=None) :
        form = CreateForm(request.POST, request.FILES or None)

        if not form.is_valid() :
            ctx = {'form' : form}
            return render(request, self.template, ctx)

        # Add owner to the model before saving
        pic = form.save(commit=False)
        pic.owner = self.request.user
        pic.save()
        return redirect(self.success_url)

class PicUpdateView(LoginRequiredMixin, View):
    template = 'pics/form.html'
    success_url = reverse_lazy('pics:all')
    def get(self, request, pk) :
        pic = get_object_or_404(Pic, id=pk, owner=self.request.user)
        form = CreateForm(instance=pic)
        ctx = { 'form': form }
        return render(request, self.template, ctx)

    def post(self, request, pk=None) :
        pic = get_object_or_404(Pic, id=pk, owner=self.request.user)
        form = CreateForm(request.POST, request.FILES or None, instance=pic)

        if not form.is_valid() :
            ctx = {'form' : form}
            return render(request, self.template, ctx)

        pic = form.save(commit=False)
        pic.save()

        return redirect(self.success_url)

class PicDeleteView(OwnerDeleteView):
    model = Pic
    template_name = "pics/delete.html"


def stream_file(request, pk) :
    pic = get_object_or_404(Pic, id=pk)
    response = HttpResponse()
    response['Content-Type'] = pic.content_type
    response['Content-Length'] = len(pic.picture)
    response.write(pic.picture)
    return response

# Another way to do it.
# This will handle create and update with an optional pk parameter on get and post
# We don't use the Generic or OwnerGeneric because (a) we need a form with a file
# and (b) we need to to populate the model with request.FILES
class PicFormView(LoginRequiredMixin, View):
    template = 'pics/form.html'
    success_url = reverse_lazy('pics:all')
    def get(self, request, pk=None) :
        if not pk : 
            form = CreateForm()
        else: 
            pic = get_object_or_404(Pic, id=pk, owner=self.request.user)
            form = CreateForm(instance=pic)
        ctx = { 'form': form }
        return render(request, self.template, ctx)

    def post(self, request, pk=None) :
        if not pk:
            form = CreateForm(request.POST, request.FILES or None)
        else:
            pic = get_object_or_404(Pic, id=pk, owner=self.request.user)
            form = CreateForm(request.POST, request.FILES or None, instance=pic)

        if not form.is_valid() :
            ctx = {'form' : form}
            return render(request, self.template, ctx)

        # Adjust the model owner before saving
        pic = form.save(commit=False)
        pic.owner = self.request.user
        pic.save()
        return redirect(self.success_url)

