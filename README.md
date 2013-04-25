# Integration/functional testing with Behat + Mink in Fork CMS
These files provide an infrastructure to create integration tests for your Fork CMS app.
They are developed against Fork **3.5.1**

It contains the following:
* Behat, a BDD framework including webdrivers.
* Faker, a library used to generate fixtures.
* Selenium automates browsers.

## Installation
1. Move all files in this repo to your Fork CMS project root (by means of a git remote or manually)
2. Run ```./configure_tests.sh```

This does the following things for you:
* Installs a global composer if you don't have one set up.
* Installs Selenium standalone server so you can run javascript-related tests.
* It adds Behat, Mink, Faker and all related packages to the project's composer.json.
* Runs a composer update and installs the above packages

## Configuration
You will need a test database so none of your data gets compromised while running the tests.
Simply create a copy of your app's database with the same name + ```_test``` appended to it.

**Example**: If your app's database name is ```forkcms```, your test database should be named ```forkcms_test```.

Note that it should be accessible with **the same user/password** as your app's database.

## Running the tests
Composer generated a ```bin/``` folder containing a symlink to the installed Behat executable in the vendor folders.

Run all available tests:
```
bin/behat
```

Run a specific feature:
```
bin/behat tests/fork/api/_login.feature
```

### Javascript tests
Some tests require Selenium to be up and running.
To start a server, run the following command:

```java -jar /usr/local/bin/selenium-server-standalone-2.31.0.jar```

You probably want to add an alias to your ~/.bashrc or dotfiles of choice so you don't have to type the full command each time:

```alias selenium='java -jar /usr/local/bin/selenium-server-standalone-2.31.0.jar'```

### Firefox
By default Selenium uses Firefox to run in-browser tests, so make sure it's installed.

#### Add Firefox.app location to your PATH
If you installed your Firefox in a custom location, you need to add it your PATH so Selenium knows where to look for the firefox binary:

```export PATH=$PATH:$HOME/Applications/Firefox.app/Contents/MacOS```

## Resources
If you want to learn to write tests yourself, read up on the behat/mink docs:
- [Behat docs](http://docs.behat.org/)
- [Mink docs](http://mink.behat.org/)
- [Faker docs](https://github.com/fzaninotto/Faker)
- [Wikipedia](http://en.wikipedia.org/wiki/Behavior-driven_development)

