# Django for Everybody

This web site is building a set of free materials, lectures, and assignments to help students learn the Django web development framework. You can take this course and receive a certificate at:

* [Coursera](https://www.coursera.org/programs/university-of-michigan-coursera-learning-program-1egh5?authProvider=umich&collectionId=&currentTab=CATALOG&productId=4wcxMIWSEeqbnQqkTNWwfw&productType=s12n&showMiniModal=true): Django for Everybody Specialization
* [edX](https://www.edx.org/xseries/michiganx-django-for-everybody): Django for Everybody XSeries Program
* [FutureLearn](https://www.futurelearn.com/programs/django): Django for Everybody Program

We use the free PythonAnywhere hosting environment to deploy and test our Django projects and applications. You can keep using this hosting environment to develop and deploy your Django applications after you complete the course.

### Technology

* https://www.dj4e.com/lessons

This site uses Tsugi framework to embed a learning management system into this site and handle the autograders. If you are interested in collaborating to build these kinds of sites for yourself, please see the tsugi.org website.

# Setting up DJ4E Development Environment (WSL + XAMPP)

This is how you install the Tsugi grading platform and the Django sample applications on a Windows machine using WSL (Windows Subsystem for Linux) and XAMPP.

### Prerequisites
* Windows 10/11 with WSL2 (Ubuntu) installed

* XAMPP installed on Windows (C:\xampp)

* Git installed inside WSL.

## Steps for XAMPP and Database Setup:

1. Start XAMPP: Open XAMPP Control Panel and start Apache and MySQL.

2. Create Database:
Go to http://localhost/phpmyadmin.

3. Create a new database named tsugi (UTF8MB4 Unicode).

4.Create User:

In the SQL tab, run:

SQL
CREATE USER 'ltiuser'@'%' IDENTIFIED BY 'ltipassword';
GRANT ALL PRIVILEGES ON tsugi.* TO 'ltiuser'@'%';
FLUSH PRIVILEGES;

5. Configure MySQL for WSL:

In XAMPP, click MySQL Config > my.ini.

6. Change bind-address=127.0.0.1 to bind-address=0.0.0.0.

7. Restart MySQL.

## Steps for Tsugi Setup
1. Navigate to Htdocs:

2. Inside Bash:
* cd /mnt/c/xampp/htdocs/
* mkdir dj4e && cd dj4e

3. Clone Tsugi:

* git clone https://github.com/csev/dj4e.git .
* cd dj4e
* git clone https://github.com/csev/tsugi.git

4. Configure Tsugi:

In the tsugi folder, copy config-dist.php to config.php.

5. Edit config.php:

* Set $wwwroot = 'http://127.0.0.1/dj4e/tsugi';

* Set $pdo = 'mysql:host=127.0.0.1;port=3306;dbname=tsugi';

* Set $adminpw = 'your_choice';

6. Open http://127.0.0.1/dj4e/tsugi/admin in your browser.

Login and click Upgrade Database.

## Setup dj4e Samples

1.Clone Samples:
Inside Bash:

* cd /mnt/c/xampp/htdocs/dj4e/
* git clone https://github.com/csev/dj4e-samples.git
* cd dj4e-samples

2. Set up environment
Inside Bash:

* sudo apt install python3-pip python3-venv libmysqlclient-dev pkg-config
* python3 -m venv venv
* source venv/bin/activate
* pip install -r requirements52.txt

3. Locate your Gateway IP: cat /etc/resolv.conf (e.g., 10.255.255.254)

4. Create dj4e-samples/settings_local.py and set the HOST to that Gateway IP:

Python
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'tsugi',
        'USER': 'ltiuser',
        'PASSWORD': 'ltipassword',
        'HOST': '10.255.255.254',
        'PORT': '3306',
    }
}
5. Run Migrations & Start:

Inside Bash:

* python manage.py migrate
* python manage.py createsuperuser
* python manage.py runserver

### Common Troubleshooting
* 404 Not Found: Ensure WSL Apache is stopped (sudo service apache2 stop) so XAMPP Apache can use Port 80

* Connection Refused: Ensure the HOST in settings_local.py matches the Gateway IP from resolv.conf

* Access Denied: Ensure the MySQL user in phpMyAdmin is granted access for '%' (any host)
