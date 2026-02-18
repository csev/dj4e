# Django Girls Autograder

A series of autograders that verify student completion of milestones from the [Django Girls tutorial](https://tutorial.djangogirls.org/). Each milestone aligns with a folder/tag in the assn documentation.

Students deploy their blog on PythonAnywhere (e.g. `https://USERNAME.pythonanywhere.com`) and submit the URL.

## Milestones (match assn/django-girls folder tags)

| # | Tag | Description | Tutorial Chapter |
|---|-----|-------------|------------------|
| 01 | startproject | Project created and deployed | Starting a New Django Project! |
| 03 | admin | Django admin configured | Django admin |
| 04 | urls | grade_check view and URL routing | Django URLs, Django views |
| 05 | html | Basic HTML structure | Introduction to HTML |
| 06 | dynamic | Dynamic data in templates | Dynamic data in templates |
| 07 | templates | Post list template | Django templates |
| 08 | css | Stylesheets linked | CSS – make it pretty |
| 09 | base | Template extending (base template) | Template extending |
| 10 | detail | Post detail page and links | Add a Detail Page |

## Student Setup

- **04-urls onward**: Update `views.py` so `grade_check` returns your unique check string (shown by the autograder)
- **06-dynamic, 10-detail**: Add at least one post via Django admin

## Technical

- Uses `webauto.php` from the crud tool
- Relies on Tsugi LTI for grade passback
- Instructor selects milestone via Settings
