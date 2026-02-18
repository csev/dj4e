# Django Girls Autograder

A series of autograders that verify student completion of milestones from the [Django Girls tutorial](https://tutorial.djangogirls.org/).

Students deploy their blog on PythonAnywhere (e.g. `https://USERNAME.pythonanywhere.com`) and submit the URL. Each milestone checks progressively more of the tutorial.

## Milestones

| Milestone | Description | Tutorial Chapters |
|-----------|-------------|-------------------|
| 01 – Grade Check | `grade_check` view returns student's check string | Django views, Django URLs |
| 02 – Post List | Home page shows post list template | Dynamic data in templates, Django templates |
| 03 – Post Detail | Individual post pages work at `/post/1/` | Add a Detail Page |
| 04 – Blog Complete | Full blog with styling, links, post detail | CSS, Template extending, Add a Detail Page |

## Student Setup

Students must:

1. Complete the Django Girls tutorial on PythonAnywhere
2. Update `views.py` so `grade_check` returns their unique check string (shown by the autograder)
3. Deploy at `https://USERNAME.pythonanywhere.com`
4. Add at least one post via Django admin for milestones 03 and 04

## Technical

- Uses `webauto.php` from the crud tool for HTTP scraping
- Relies on Tsugi LTI for grade passback
- Instructor selects milestone via Settings
