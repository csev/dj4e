Installing Django 5.2 Locally (WSL)
===================================

This document is for **Windows Subsystem for Linux** (WSL). You run Linux commands inside WSL (for example **Ubuntu** from the Microsoft Store) while staying on a Windows machine. The flow matches native Linux; this page adds only WSL setup and a few Windows-specific tips.

When you complete this, you will have a URL from localhost.run (e.g. `https://xxxxx.lhr.lt`) that you can submit to the "Install" autograder.

**Other platforms:** [Windows (native)](dj4e_windows52.md) · [Linux](dj4e_linux52.md) · [Mac](dj4e_mac52.md) · [Overview](dj4e_local52.md)

Prerequisites
-------------

1. **WSL 2** – Install and update WSL, then install a Linux distribution (Ubuntu is a common choice). Official steps: <a href="https://learn.microsoft.com/en-us/windows/wsl/install" target="_blank">Install WSL on Windows</a>.  
   Open **Ubuntu** (or your distro) from the Start menu; the rest of this guide uses that terminal.

2. **Python 3.10, 3.11, 3.12, or 3.13** – In your WSL terminal, on Debian/Ubuntu:

        sudo apt update
        sudo apt install python3 python3-venv python3-pip

   Other distros: use their package manager to install Python 3 and `venv` support.

3. **Git** – Debian/Ubuntu: `sudo apt install git`

4. **SSH** – Used by localhost.run. OpenSSH is included with typical WSL images; if `ssh` is missing, install `openssh-client` with your package manager.

**Where to put your files:** Keep projects under your **Linux home** (`cd ~`), not under `/mnt/c/...`. I/O on the Windows drive from WSL is slower and can cause odd permission or line-ending issues with tools like Git and Python.

We use `~/django_projects` for your Django apps and `~/dj4e-samples` for the sample code. The virtual environment lives in `~/.ve52`.

Creating a Virtual Environment
------------------------------

In your **WSL** terminal:

    cd ~
    python3 -m venv .ve52
    source .ve52/bin/activate

If `python3` is not found, install it as above or use a specific version (e.g. `python3.12 -m venv .ve52`).

Once activated, your prompt should show `(.ve52)` at the start. Verify Python and install Django:

    pip install --upgrade pip
    pip install django==5.2
    python -m django --version

The Django version should be 5.2 or higher.

Installing dj4e-samples and Requirements
----------------------------------------

Make sure your virtual environment is activated (you should see `(.ve52)` in your prompt). If you opened a new terminal, activate it again as shown above.

    cd ~
    git clone https://github.com/csev/dj4e-samples
    cd dj4e-samples
    git checkout django52
    git pull origin django52
    pip install --upgrade pip
    pip install -r requirements52.txt

Verify the installation:

    python manage.py check

Expected output includes:

    System check identified no issues (0 silenced).

Then run migrations:

    python manage.py makemigrations
    python manage.py migrate

To pull updates later:

    cd ~/dj4e-samples
    git pull origin django52

Building Your Django Application
---------------------------------

Ensure your virtual environment is activated. Create your project folder and Django project:

    cd ~
    mkdir -p django_projects
    cd django_projects
    django-admin startproject mysite

Edit `mysite/mysite/settings.py` and set:

    ALLOWED_HOSTS = [ '*' ]

Leave `DEBUG = True`. Save the file. You can edit files with `nano`, `vim`, or <a href="https://code.visualstudio.com/docs/remote/wsl" target="_blank">Visual Studio Code with the WSL extension</a> (open the folder from WSL).

Adding the Polls Application
-----------------------------

Create the polls app and add the initial view:

    cd ~/django_projects/mysite
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

You need **two WSL terminal windows** (or tabs)—for example two **Ubuntu** windows.

**Terminal 1 – Django server**

    cd ~
    source .ve52/bin/activate
    cd django_projects/mysite
    python manage.py runserver

Leave this running. The server listens on `http://127.0.0.1:8000/`.

**Terminal 2 – localhost.run tunnel**

    ssh -R 80:localhost:8000 localhost.run

Leave this running. localhost.run will print a public URL, for example:

    Forwarding HTTP traffic from https://xxxxx-xx-xx-xx-xx.lhr.lt

That URL is what you submit to the Install autograder.

**Testing locally**

- In **Windows** Edge or Chrome, try `http://127.0.0.1:8000/polls` or `http://localhost:8000/polls`. Recent WSL versions forward `localhost` from Windows to your WSL services.
- If the page does not load, run `python manage.py runserver 0.0.0.0:8000` instead, then in a WSL terminal run `hostname -I` and open `http://<first-ip>:8000/polls` from Windows (or search for “WSL localhost forwarding” for your Windows version).
- Public: open the localhost.run URL (e.g. `https://xxxxx.lhr.lt/polls`).

You should see: "Hello, world. You're at the polls index."

Submitting to the Autograder
---------------------------

1. Keep **Terminal 1** (runserver) and **Terminal 2** (ssh tunnel) running
2. Copy the **full** localhost.run URL (e.g. `https://xxxxx.lhr.lt`)
3. Submit that URL to the DJ4E Install autograder

The autograder will fetch your site through localhost.run. Each time you restart the SSH tunnel, the URL may change; if it does, submit the new URL.

Workflow: Change, Check, Restart, Test
-------------------------------------

When you change code:

1. Run `python manage.py check` to catch errors
2. Stop the server (Ctrl+C in Terminal 1) and start it again: `python manage.py runserver`
3. Test at `http://127.0.0.1:8000/` or your localhost.run URL

The tunnel (Terminal 2) can stay running; you only need to restart the Django server.

Checkup Tool
-----------

From WSL:

    bash ~/dj4e-samples/tools/checkup.sh

Possible Errors
---------------

See <a href="dj4e_errors52.md" target="_blank">Fixing Common Django Errors</a> for troubleshooting.

If `ssh -R 80:localhost:8000 localhost.run` fails:

- Ensure the Django server is running in the other terminal
- Some networks block outbound SSH (port 22); try a different network

Starting Over
-------------

To remove everything and start fresh:

    cd ~
    rm -rf .ve52
    rm -rf dj4e-samples
    rm -rf django_projects

Then follow this document from the beginning.

About Django 4.2
---------------

As of January 2026, this course uses Django 5.2. If you prefer Django 4.2, follow the <a href="dj4e_install42.md" target="_blank">Django 4.2 install instructions</a> and adapt the local steps from this document.
