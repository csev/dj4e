# Django Girls Autograder

A series of autograders that verify student completion of milestones from the [Django Girls tutorial](https://tutorial.djangogirls.org/). Each milestone aligns with a folder/tag in the assn documentation.

Students deploy their blog on PythonAnywhere (e.g. `https://USERNAME.pythonanywhere.com`) and submit the URL.

## Milestones (match assn/django-girls folder tags)

| # | Tag | Description | Tutorial Chapter |
|---|-----|-------------|------------------|
| 01 | startproject | Project created and deployed | Starting a New Django Project! |
| 03 | admin | Django admin configured | Django admin |
| 05 | html | grade_check, URLs, and HTML (post_list template) | Django URLs, views, Introduction to HTML |
| 07 | templates | Post list template with dynamic data | Django ORM, Dynamic data in templates, Django templates |
| 08 | css | Stylesheets linked | CSS – make it pretty |
| 09 | base | Template extending (base template) | Template extending |
| 10 | detail | Post detail page and links | Add a Detail Page |

## Student Setup

- **05-html onward**: Update `views.py` so `grade_check` returns your unique check string (shown by the autograder)
- **07-templates, 10-detail**: Add at least one post via Django admin

## Technical

- Uses `webauto.php` from the crud tool
- Relies on Tsugi LTI for grade passback
- Instructor selects milestone via Settings
