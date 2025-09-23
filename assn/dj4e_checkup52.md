
Scanning your PythonAnywhere Account for Common Errors
======================================================

Over the years, many students have following the instructions for this class
have completed the assignments following the provided instructions.  But
sometimes things go wrong and students lose track of where they are
at in the instructions and they start Internet search or AI for solutions
or going back and trying to re-do steps that have already been completed.

Sometimes you are following a tutorial online and the assignment has instructed
you to do some steps differently on PythonAnywhere.  Sometimes the "do
this a little differently" recommedation is difficult to remmber
while doing an assignment and instructions get done out of order or
more then once.

We have built a tool to catch and let you know about some of the
common mistakes that happen when something goes wrong in the middle
of the instructions for an assignment.

You should probably run this checkup tool between assignments.  Reasonable
times might be before you start the next assignment or after you complete the
previous assignment.

Running the Checkup Tool on PythonAnywhere
==========================================

The checkup tool is installed in your `~/dj4e-samples` folder when you did the
first assignment to install Django on PythonAnywhere.

The following commands will get the latest version of the checkup tool and run
it:

    cd ~/dj4e-samples
    git checkout django52
    git pull

    bash ~/dj4e-samples/tools/checkup.sh

If there are no issues, it should say:

    Checkout complete

You can go back to working on your assignments :)

What Might Be Wrong
===================

The checkup checks if you are running in the right virtual enviroment and if
the Python version and Django version in the virtual environment are correct.
It also checks your folder structure for common mistakes and patches 
your `manage.py` to add some PythonAnywhere specific code.

If there are errors, you might see messages like these:

* You are not running in a virtual environment, please consult the DJ4E install instructions.

* You are running in an out-of-date Django 4.2 virtual environment - you
should be running ve52 - see DJ4E install instructions

* Not running in virtual environment ve52 - please consult the DJ4E install instructions.

* Failure: Python version 3.8.1 is outside the required range 3.10.0 to 3.13.9

* Failure: Django version 4.2.7 is NOT within the range 5.2.0 to 5.2.9

* The folder ~/djangotutorial should not exist - we use ~/django\_projects instead - you
should remove ~/djangotutorial to avoid confusion

* The folder ~/mysite should not exist - we use ~/django\_projects/mysite instead - you
should remove ~/mysite to avoid confusion

There are lots of ways to end up in these situations.  Figuring these out and fixing them is not part of the
learning objectives of the course - you should get some help if you need to to fix things up.

Over time as new mistakes are found in office hours or help sessions we can add new checks
to the checkup tool to make the logistics of this course easier for everyone.

