
Using the Command Line Interface on PythonAnywhere
==================================================

A lot of the assignments in this course require you to use the Bash
shell (Linux command line interface) on PythonAnywhere.  In this example:

    17:33 ~ $ workon django4
    (django4) 17:33 ~ $ python --version
    Python 3.9.5
    (django4) 17:36 ~ $ python -m django --version
    4.0.7

The shell commands that are typed are 

* `workon django4`
* `python --version`
* `python -m django --version`

Each time you start a new shell, you need to type `workon django4`.  If you
leave and come back to a shell that is still running, if you see the '(django4)'
in your prompt - you do not have to re-run the `workon` command.  It just needs
to be done once per shell.

Some Bash Commands
------------------

Here are a few bash commands that we use a lot:

* `ls` - List the files and directories in the current working directory.

* `pwd` - Print the current working directory.  Shows the current folder
name and all its parent folders.

* `ls -l` - Prints a list of files and folders in the current directory
with extra detail.

* `cd` - Change directory.  Which you just type `cd` with no parameter
it takes to your home directory - the directory that you are placed in 
when you log in.

* `cd ~/mysite` - Go into the folder named `mysite` directly below your home
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







How and when you exit the Django shell
-------------------------------------- 

In tutorial 2, you edit `models.py` and run the Django Shell, then you edit
the `models.py` file again and then run the shell again. What the tutorial does
not mention is the need to exit and restart the shell any time you change
`models.py`.  The tutorial tells you to run the shell again but it does not
tell you to exit the existing shell first - so you might see an error like this:

    (django4) 17:16 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some django shell stuff

    >>> python manage.py shell
    File "<console>", line 1
        python manage.py shell
            ^
    SyntaxError: invalid syntax
    >>> 

The correct way is to exit the shell and restart it.

    (django4) 17:20 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some django shell stuff

    >>> quit()
    (django4) 17:20 ~/django_projects/mysite (master)$ 

Then you edit your `models.py` and *re-start* the Django shell from the
`bash` console:

    (django4) 17:24 ~/django_projects/mysite (master)$ python manage.py shell
    Type "help", "copyright", "credits" or "license" for more information.
    (InteractiveConsole)
    >>> # Do some more django shell stuff

    >>> quit()
    (django4) 17:20 ~/django_projects/mysite (master)$ 

After a while you will understand that you need to be in `bash` (dollar sign prompt)
to run bash commands and be in the Django shell (>>> prompt) to run Django commands.

