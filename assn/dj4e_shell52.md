
Using the Command Line Interface / Shell on PythonAnywhere
==========================================================

A lot of the assignments in this course require you to use the Bash
Shell (Linux command line interface) on PythonAnywhere.  In this example:

    (.ve52) 17:33 ~ $ python --version
    Python 3.10.5
    (.ve52) 17:36 ~ $ python -m django --version
    5.2

The Linux shell commands in the above example are:

* `python --version`
* `python -m django --version`

In general in this class, the first assignment is to set up a virtual environment
so you can control which version of Python and Django you are using.  Consult the
install documentation to see how to set up the virtual environment and how to
automatically start every shell and activate the virtual environment.

Django advances its version and the required Python version once per year and
the course needs to upgrade to the latest Django version from time to time.
Normally these upgrades don't really change how Django works or the commands you
need to type so not all videos are re-recorded for every new version of Django.  

So don't be concerned if you see a different virtual environment, Python version,
or Django version in a shell prompt in a video - the rest of the video is fine
and applies across multiple versions of Django.

Some Linux Shell Commands
-------------------------

Here are a few Linux commands that we use a lot:

* `ls` - List the files and directories in the current working directory.

* `pwd` - Print the current working directory.  Shows the current folder
name and all its parent folders.

* `ls -l` - Prints a list of files and folders in the current directory
with extra detail.

* `cd` - Change directory.  Which you just type `cd` with no parameter
it takes to your home directory - the directory that you are placed in 
when you log in.

* `cd ~/django_projects` - Go into the folder named `django_projects` directly below your home
folder.  The `~` on shell commands is a short cut for "the path to my
home folder".

* `rm sqlite3.db` - Remove a file - in this case the file `sqlite3.db` in
the current working directory.

* `grep -r autos_create *` - Search files in the current folder and below for
files that contain the string `autos_create` and show the file where the search
string is found and the line of the file that contains the string.

* `clear` - Clear the screen

* `up-arrow` - You can scroll back and forth through previous commands
using the up and down arrows on your keyboard.  You can press `return`
to re-execute a previous command once you scroll to it.

Other Interfaces
----------------

You are not always typing commands to the to the Linux Shell in the command
line.  Usually the Linux shell prompt ends in dollar sign `$`.  You need to
look at the prompt and recognize which program that is asking you for commands.

Here are some other command line environments, what their prompts look like,
and (most importantly) how to exit them.  You will learn to use them as the
course progresses.  Make sure to bookmark this page and come
back when you get stuck.

**Interactive Python**

In this example, you run Python interactively so you can type python code to experiment
with code fragments:

    (.ve52) 03:37 ~ $ python
    Python 3.9.5 (default, May 27 2021, 19:45:35) 
    [GCC 9.3.0] on linux
    Type "help", "copyright", "credits" or "license" for more information.
    >>> print('Hello world')
    Hello world
    >>> for i in range(2) : 
    ...     print(i)
    ... 
    0
    1
    >>> quit()
    (.ve52) 03:38 ~ $ 

You start in a shell prompt `$` and run Python and get the "three chevron" prompt `>>>`
and you can type Python commands until you type `quit()`.  Note that you can also
type `exit()` to leave interactive Python.

Sometimes you enter a statement like `for` that required multiple indented python
statements until you de-indent and enter a blank line.  When python is prompting
for more statements it changes the prompt from three chevrons to `...`.

**SQLite Interface**

Sometimes you run the SQLite command line interface.  Once you start the interface
from the shell prompt, your prompt becomes `sqlite>` until you exit with `.quit`.

At this prompt you type SQL (database) statements or SQLite commands.

    (.ve52) 03:45 ~/django_projects/mysite (master)$ sqlite3 db.sqlite3 
    SQLite version 3.31.1 2020-01-27 19:55:54
    Enter ".help" for usage hints.
    sqlite> .tables
    auth_user_groups            polls2_choice             
    auth_user_user_permissions  polls2_question           
    sqlite> select * from polls2_choice;
    1|42|1213|1
    2|Something else|90|1
    sqlite> select * 
       ...> from polls2_choice
       ...> ;
    1|42|1213|1
    2|Something else|90|1
    sqlite> .quit
    (.ve52) 03:46 ~/django_projects/mysite (master)$

In the above example `.tables` is a SQLite command and `select * from polls2_choice;`
is an SQL command.   SQL commands terminate with a semicolon and can span multiple
lines.  So if you type part of a SQL statement without a semicolon, SQLite changes
the prompt to `...>` to allow you to enter more SQL until you enter a `;` at
which time the prompt changes back to `sqlite>`.

You exit the SQLite interface and get back to the Linux Shell with `.quit`.

**The Django Shell**

Later in the course, you will be using the Django Shell to interact directly
with your database through your Django models.  You use `python manager.py shell`
in your project folder to start the Django Shell.   

The Django Shell is just a Python Shell but all of your application code has been
loaded.   So you can execute Django statements as well as python statements.

    (.ve52) 03:54 ~/django_projects/mysite (master)$ python manage.py shell                                                                   
    Python 3.9.5 (default, May 27 2021, 19:45:35) 
    [GCC 9.3.0] on linux
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> print('Hello world')
    Hello world
    >>> from polls.models import Question
    >>> q = Question(question_text='Hello world')
    >>> quit()
    (.ve52) 03:55 ~/django_projects/mysite (master)$ 

In the above you are importing the `Question` model from the `polls/models.py`
file and then you are creating a `Question` object with some initial text and
assigning it to the variable `q`.

You exit the Django Shell using the same `quit()` command as you use to exit
the Python Shell.  Remember that the Django Shell is just a Python Shell with
your Django project pre-loaded.   That is why you need to run the command
in the folder where the `manage.py` file is housed.

