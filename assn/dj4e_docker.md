# Using Docker and VSCode for Django Development

This guide will help you set up a consistent Python and Django development environment using Docker and Visual Studio Code (VSCode).

## Why Develop Locally?

There are a number of advantages to doing development work locally:

* You never have to `Reload` your application.  The Django `runserver` process monitors
changes to your files and completely restarts itself as soon as any file changes in your
project.   This makes for much quicker edit-test cycles.
* You can use a fancy text editor like VScode or even an AI-powered editors like Cursor.
* No more need to change the WGSI configuration file when you want to switch between
your project and some sample code - you can even run more than one application at the
same time on different ports.
* You can put debug `print()` statements and they come right out without having to look
at the error or server logs.  Error tracebacks and error logs come right out.
* You can work without a network connection!

## Step 1: Install Docker

### For Windows:
1. Create a free Docker Hub account at [Docker Hub](https://hub.docker.com/signup)
2. Go to [Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/)
3. Click "Download from Docker Hub" and sign in with your account
4. Run the installer and follow the prompts
5. After installation, start Docker Desktop from your Start menu

### For Mac:
1. Create a free Docker Hub account at [Docker Hub](https://hub.docker.com/signup)
2. Go to [Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/)
3. Choose the right version for your Mac (Intel chip or Apple chip)
4. Click "Download from Docker Hub" and sign in with your account
5. Run the installer and follow the prompts
6. After installation, start Docker Desktop from your Applications folder

## Step 2: Install Visual Studio Code (VS Code)

VS Code is a free code editor that works well with Docker:

1. Go to [Visual Studio Code](https://code.visualstudio.com/)
2. Download the version for your operating system
3. Run the installer and follow the prompts

> Note: Feel free to use other VSC-like editors such as Cursor. The steps below should work with any editors derived from VS Code.

## Step 3: Install Required VS Code Extensions

1. Open VS Code
2. Click on the Extensions icon on the left sidebar (or press Ctrl+Shift+X)
3. Search for and install:
   - "Remote - Containers" (or "Dev Containers" in newer versions)
   - "Python" (Microsoft's Python extension)

## Step 4: Run a Docker Container for Django Development

```bash
# Pull the Django image
docker pull xingjianzhang/dj4e:latest

# Run the container for the first time
docker run -d -p 8000:8000 --name django-dev xingjianzhang/dj4e:latest tail -f /dev/null
```

What this does:
- `-d` runs the container in the background
- `-p 8000:8000` lets you access your web apps in your browser
- `--name django-dev` gives the container a friendly name

> Note: After the first time you run the container, you can use the following command to start the container.
> This command will start the container named `django-dev` if it exists. See more details [here](#working-with-your-container).
> ```bash
> docker start django-dev
> ```

## Step 5: Connect VS Code to Your Container

At this point, the docker container is running and you can connect to it using VS Code.
Let's verify this by running the following command:

```bash
docker ps
```

You should see something like the following:
```
CONTAINER ID   IMAGE                       COMMAND               CREATED         STATUS          PORTS                    NAMES
9f83edee2162   xingjianzhang/dj4e:latest   "tail -f /dev/null"   2 minutes ago   Up 13 seconds   0.0.0.0:8000->8000/tcp   django-dev
```

This means that the container is running and listening on port 8000.

Now, let's connect VS Code to the container.


1. Press `F1` or `Ctrl+Shift+P` (`Cmd+Shift+P` on Mac) to open the Command Palette
2. Type "attach container" and select "Dev Containers: Attach to Running Container..."
3. Choose "django-dev" from the list
4. VS Code will open a new window connected to your container. **At this point, we are inside the container.**
5. In the new window, open the folder `/root`. This is your user directory in the container (i.e. `~`).
6. You can verify that the environment is correct by running the following command:

```bash
python --version        # Expected: Python 3.9.21
django-admin --version  # Expected: Django 4.2.7
```

## Step 6a: Initialize a New Django Project

If you want to start from scratch with a new Django project:

1. Open a new terminal in VS Code (Terminal → New Terminal)
2. Run the following command:

```bash
django-admin startproject mysite
```

## Step 6b: Use an Existing Django Project

If you prefer to start with an existing Django project and have it already [pushed to GitHub](dj4e_github.md):

1. Open a new terminal in VS Code (Terminal → New Terminal)
2. Run these commands:

```bash
gh auth login # Follow the instructions to login GitHub
gh repo clone <your-repo-name>
cd <your-repo-name>
python manage.py check
python manage.py migrate
python manage.py createsuperuser
```

## Step 7: Run Your Django Server

At this point, your codebase is ready to run. You can run it locally by running the following command:

```bash
python manage.py runserver 0.0.0.0:8000
```

The `0.0.0.0` part is important when running in Docker - it allows connections from outside the container.

Then navigate to http://localhost:8000 in your web browser to see your Django application!

## Working With Your Container

> Note: **You need to start the Docker Desktop** before you run any of the following commands.

### Stopping Your Container
When you're done working, you can stop the container by running the following command.
This frees up resources on your computer.
```bash
docker stop django-dev
```

### Starting Your Container Again
When you want to continue working, you need to start the container again.
```bash
docker start django-dev
```
Then reconnect VS Code as in Step 6.

## Troubleshooting

### Need to Remove and Recreate the Container
If your container is broken or you want to start fresh:
```bash
docker stop django-dev
docker rm django-dev
# Then run the docker run command from Step 5 again
```

## FAQ

### What is Docker?

Think of Docker like a magical box that lets you pack up everything your application needs:

Imagine you're making a cake 🎂:
- Traditional way: You need specific ingredients, tools, and kitchen setup
- Docker way: You pack the entire kitchen, ingredients, and tools in one portable box!

Docker does the same thing for software - it packages your application with everything it needs to run perfectly, no matter where you take it.

### What is a container?

A container is like a shipping container for software! Just like real shipping containers:
- It's standardized (works the same everywhere)
- It carries everything inside (your application and all its needs)
- It can be moved anywhere easily
- It keeps its contents separate and safe

This is why Docker's logo is a whale carrying shipping containers! 🐳
