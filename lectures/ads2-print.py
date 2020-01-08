# /Users/csev/django/django_projects/chucklist/home/templates/base_bootstrap.html

<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{{ settings.APP_NAME }}{% endblock %}</title>

<meta name="dj4e" content="735b90b4568125ed6c3f678819b6e058">

<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"
    crossorigin="anonymous">

<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap-theme.min.css"
    crossorigin="anonymous">

<link rel="stylesheet"
    href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"
     crossorigin="anonymous">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"
    crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"
    crossorigin="anonymous"></script>

<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/v4-shims.css">

<script
  src="https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/2.2.7/webcomponents-loader.js"
  crossorigin="anonymous"></script>

<script
  src="https://cdnjs.cloudflare.com/ajax/libs/fetch/3.0.0/fetch.js"
  crossorigin="anonymous"></script>

</head>
<body>
{% block navbar %}
<!-- https://www.w3schools.com/booTsTrap/bootstrap_navbar.asp -->
<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
        <a class="navbar-brand" href="/">{{ settings.APP_NAME }}</a>
    </div>
    <ul class="nav navbar-nav navbar-right">
      <li>
          <a href="/">Cancel</a></li>
    </ul>
  </div>
</nav>
{% endblock %}
<div class="container">
    {% block content %}
    {% endblock %}
</div>
</body>
</html>

# /Users/csev/django/django_projects/chucklist/ads/templates/ads/base_menu.html

{% extends 'base_bootstrap.html' %}
{% block navbar %}
{% load app_tags %}
<!-- https://www.w3schools.com/booTsTrap/bootstrap_navbar.asp -->
<nav class="navbar navbar-default navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
        <a class="navbar-brand" href="/">{{ settings.APP_NAME }}</a>
    </div>
    <!-- https://stackoverflow.com/questions/22047251/django-dynamically-get-view-url-and-check-if-its-the-current-page -->
    <ul class="nav navbar-nav">
      {% url 'ads' as ads %}
      <li {% if request.get_full_path == ads %}class="active"{% endif %}>
          <a href="{% url 'ads' %}">Ads</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        {% if user.is_authenticated %}
        <li>
        <a href="{% url 'ad_create' %}">Create Ad</a>
        </li>
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                <img style="width: 25px;" src="{{ user|gravatar:60 }}"/><b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{% url 'logout' %}">Logout</a></li>
            </ul>
        </li>
        {% else %}
        <li>
        <a href="{% url 'login' %}?next={% url 'ads' %}">Login</a>
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

# The create ad / update ad form with picture support
# /Users/csev/django/django_projects/chucklist/ads2/templates/ads2/form.html

{% extends "ads2/base_menu.html" %}

{% block content %}
<p>
{% load crispy_forms_tags %}
    <form action="" method="post" id="upload_form" enctype="multipart/form-data">
    {% csrf_token %}
    {{ form|crispy }}
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="{% url 'ads2' %}" class="btn btn-secondary">Cancel</a>
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

