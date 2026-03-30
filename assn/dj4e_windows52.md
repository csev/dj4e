Installing Django 5.2 Locally (Windows)
=======================================

This document describes how to run Django 5.2 on your Windows PC and expose it to the internet using <a href="https://localhost.run" target="_blank">localhost.run</a> so that DJ4E autograders can test your work.

When you complete this, you will have a URL from localhost.run (e.g. `https://xxxxx.lhr.life`) that you can submit to the "Install" autograder.

**Other platforms:** [WSL](dj4e_wsl52.md) · [Mac](dj4e_mac52.md) · [Linux](dj4e_linux52.md) · [Overview](dj4e_local52.md)

Prerequisites
-------------

You need the following installed on your computer:

1. **Python 3.10, 3.11, 3.12, or 3.13** – <a href="https://www.python.org/downloads/" target="_blank">Download Python</a>  
   Check **Add Python to PATH** during install.

2. **Git** – <a href="https://git-scm.com/downloads" target="_blank">Download Git</a>  
   Git for Windows installs **Git Bash**, which we recommend for running the commands below.

3. **SSH** – Used by localhost.run (no signup required). SSH comes with Git for Windows; Windows 10 and 11 also include OpenSSH.

We recommend **Git Bash** so your commands match the Mac and Linux guides. Open **Git Bash** from the Start menu instead of cmd or PowerShell.

We use the same folder layout as on Mac/Linux: `~/django_projects` for your Django apps and `~/dj4e-samples` for the sample code (in Git Bash, `~` is your user profile). The virtual environment lives in `~/.ve52`.

Creating a Virtual Environment
------------------------------

Create and activate a virtual environment for Django 5.2 in your home directory.

**Git Bash (recommended):**

    cd ~
    python -m venv .ve52
    source .ve52/Scripts/activate

**Command Prompt:**

    cd %USERPROFILE%
    python -m venv .ve52
    .ve52\Scripts\activate.bat

**PowerShell:**

    cd $env:USERPROFILE
    python -m venv .ve52
    .ve52\Scripts\Activate.ps1

If `python` is not found, try `py -3`. If you have multiple Python versions, use one that is 3.10 or newer (e.g. `py -3.12 -m venv .ve52`).

Once activated, your prompt should show `(.ve52)` at the start. Verify Python and install Django:

    pip install --upgrade pip
    pip install django==5.2
    python -m django --version

The Django version should be 5.2 or higher.

Installing dj4e-samples and Requirements
----------------------------------------

Make sure your virtual environment is activated (you should see `(.ve52)` in your prompt). If you opened a new terminal, activate it again as shown above.

**Git Bash:**

    cd ~
    git clone https://github.com/csev/dj4e-samples
    cd dj4e-samples
    git checkout django52
    git pull origin django52
    pip install --upgrade pip
    pip install -r requirements52.txt

**Command Prompt or PowerShell:**

    cd %USERPROFILE%
    git clone https://github.com/csev/dj4e-samples
    cd dj4e-samples
    git checkout django52
    git pull origin django52
    pip install --upgrade pip
    pip install -r requirements52.txt

(In PowerShell, use `cd $env:USERPROFILE` instead of `cd %USERPROFILE%` for the first line.)

Verify the installation:

    python manage.py check

Expected output includes:

    System check identified no issues (0 silenced).

Then run migrations:

    python manage.py makemigrations
    python manage.py migrate

The `dj4e-samples` folder is reference material for the course. To pull updates later:

**Git Bash:** `cd ~/dj4e-samples` then `git pull origin django52`  
**cmd/PowerShell:** `cd %USERPROFILE%\dj4e-samples` (or `$env:USERPROFILE\dj4e-samples` in PowerShell), then `git pull origin django52`

Building Your Django Application
---------------------------------

Ensure your virtual environment is activated. Create your project folder and Django project.

**Git Bash:**

    cd ~
    mkdir -p django_projects
    cd django_projects
    django-admin startproject mysite

**Command Prompt:**

    cd %USERPROFILE%
    mkdir django_projects
    cd django_projects
    django-admin startproject mysite

**PowerShell:**

    cd $env:USERPROFILE
    New-Item -ItemType Directory -Force -Path django_projects | Out-Null
    cd django_projects
    django-admin startproject mysite

In `mysite/mysite/settings.py`, edit **ALLOWED_HOSTS** and add **CSRF_TRUSTED_ORIGINS** (that setting is not in a fresh project by default—add it next to **ALLOWED_HOSTS**):

    ALLOWED_HOSTS = [ '*' ]
    CSRF_TRUSTED_ORIGINS = [
        "https://*.pythonanywhere.com",
        "https://*.lhr.life",
    ]

Leave `DEBUG = True`. Save the file.

Adding the Polls Application
-----------------------------

**Git Bash:**

    cd ~/django_projects/mysite
    python manage.py startapp polls

**Command Prompt:**

    cd %USERPROFILE%\django_projects\mysite
    python manage.py startapp polls

**PowerShell:**

    cd $env:USERPROFILE\django_projects\mysite
    python manage.py startapp polls

Edit `mysite/polls/views.py` and replace its contents with:

    from django.http import HttpResponse

    def index(request):
        return HttpResponse("Hello, world. You're at the polls index.")

Create `mysite/polls/urls.py` with:

    from django.urls import path
    from . import views

    urlpatterns = [
        path("", views.index, name="index"),
    ]

Edit `mysite/mysite/urls.py` and replace its contents with:

    from django.contrib import admin
    from django.urls import include, path

    urlpatterns = [
        path("polls/", include("polls.urls")),
        path("admin/", admin.site.urls),
    ]

Verify:

    python manage.py check

Running Your Server and Exposing with localhost.run
---------------------------------------------------

You need **two terminal windows** (or tabs): use the **first terminal** for Django below, and a **second terminal** for localhost.run.

**Terminal 1** (your **first terminal**) – Django server

**Git Bash:**

    cd ~
    source .ve52/Scripts/activate
    cd django_projects/mysite
    python manage.py runserver

**Command Prompt:**

    cd %USERPROFILE%
    .ve52\Scripts\activate.bat
    cd django_projects\mysite
    python manage.py runserver

**PowerShell:**

    cd $env:USERPROFILE
    .ve52\Scripts\Activate.ps1
    cd django_projects\mysite
    python manage.py runserver

Leave this running. The server listens on `http://127.0.0.1:8000/`.

**Terminal 2** (your **second terminal**) – localhost.run tunnel

In your **second terminal**, run:

    ssh -R 80:localhost:8000 localhost.run

Leave this running. localhost.run will print a public URL, for example:

    Forwarding HTTP traffic from https://xxxxx-xx-xx-xx-xx.lhr.life

That URL is what you submit to the Install autograder. It forwards traffic to your local Django server.

**Testing locally**

- Local: open `http://127.0.0.1:8000/polls` in your browser
- Public: open the localhost.run URL (e.g. `https://xxxxx.lhr.life/polls`)

You should see: "Hello, world. You're at the polls index."

Submitting to the Autograder
---------------------------

1. Keep **Terminal 1** (your **first terminal**, runserver) and **Terminal 2** (your **second terminal**, SSH tunnel) running
2. Copy the **full** localhost.run URL (e.g. `https://xxxxx.lhr.life`)
3. Submit that URL to the DJ4E Install autograder

The autograder will fetch your site through localhost.run. Each time you restart the SSH tunnel, the URL may change; if it does, submit the new URL.

Workflow: Change, Check, Restart, Test
-------------------------------------

When you change code:

1. Run `python manage.py check` to catch errors
2. Stop the server (Ctrl+C in your **first terminal** / Terminal 1) and start it again: `python manage.py runserver`
3. Test at `http://127.0.0.1:8000/` or your localhost.run URL

Your **second terminal** (Terminal 2, the tunnel) can stay running; you only need to restart the Django server.

Checkup Tool
-----------

We provide a checkup script in dj4e-samples. From **Git Bash**:

    bash ~/dj4e-samples/tools/checkup.sh

On Windows without Git Bash, run the equivalent checks manually (for example `python manage.py check` from your project directory with the venv activated).

Possible Errors
---------------

See <a href="dj4e_errors52.md" target="_blank">Fixing Common Django Errors</a> for troubleshooting.

If `ssh -R 80:localhost:8000 localhost.run` fails:

- Ensure the Django server is running in your **first terminal**
- Some networks block outbound SSH (port 22); try a different network
- Ensure SSH is available: `ssh -V`

Starting Over
-------------

To remove everything and start fresh:

**Git Bash:**

    cd ~
    rm -rf .ve52
    rm -rf dj4e-samples
    rm -rf django_projects

**Command Prompt:**

    cd %USERPROFILE%
    rmdir /s /q .ve52
    rmdir /s /q dj4e-samples
    rmdir /s /q django_projects

**PowerShell:**

    cd $env:USERPROFILE
    Remove-Item -Recurse -Force .ve52, dj4e-samples, django_projects -ErrorAction SilentlyContinue

Then follow this document from the beginning.

About Django 4.2
---------------

As of January 2026, this course uses Django 5.2. If you prefer Django 4.2, follow the <a href="dj4e_install42.md" target="_blank">Django 4.2 install instructions</a> and adapt the local steps from this document.
