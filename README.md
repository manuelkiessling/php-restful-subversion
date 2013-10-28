# PHPRestfulSubversion

[![Build Status](https://travis-ci.org/manuelkiessling/PHPRestfulSubversion.png?branch=master)](https://travis-ci.org/manuelkiessling/PHPRestfulSubversion)

_Scroll down for detailed installation instructions._


## About

PHPRestfulSubversion...

1. ...provides a RESTful JSON webservice API to access information in your
      Subversion repository

2. ...provides tools to cache your Subversion repository in order to make
      it searchable through the webservice in a fast and simple manner

3. ...is a library of PHP classes which you can use to implement more complex
      use cases


### Project Parts

Maybe this stuff makes more sense when being visualized:


       --------------------
       | Your REST client |
       --------------------
                .
               / \
                |
                |
    --------------------------    -----------------------------------------
    |     REST webservice    |    | Your PHP tool, using the lib directly |
    --------------------------    -----------------------------------------
                .                /
               / \              /
                |              /
                |             /
    --------------------------
    |                        |     read from SVN ------------------------------
    |         Library        |        ------>    |    Cache Builder Script    |
    |                        |        <------    ------------------------------
    --------------------------     write to cache
          .          .    |
         / \        / \   |
          |          |   \ /
          |          |    .
    ------------  ------------
    | SVN Repo |  | Cache DB |
    ------------  ------------


As you can see, PHPRestfulSubversion never writes to the Subversion repository,
and the REST webservices are read-only as of now.

There is a working JavaScript/HTML REST client included which allows you to
search the Subversion cache for all changesets whose commit message contains
a certain string, and for all changesets that include a path whose name ends
on a certain string ("show me all changesets which include the file
main/default.txt").


## Installation on Linux systems

Getting PHPRestfulSubversion up and running usually takes around 10 minutes and
is a process which can roughly be split into four parts:

1. Preparing your system
2. Deploy and configure PHPRestfulSubversion
3. Build the Subversion cache
4. Configure Apache to serve the RESTful JSON webservice

The following process describes the detailed steps necessary for every part
of the process. The description of this process is targeted at an installation
on a Debian GNU/Linux 6.0 ("Squeeze") system with at least the "Standard system
utilities" collection installed.

In order to keep things a bit simpler, I assume you do everything as root.


### 1. Preparing your system

Besides the packages already available on your system and PHPRestfulSubversion
itself, you will need the following packages:

* Git
* Subversion
* SQLite 3
* PHP 5.3
* PHPUnit 3
* Apache 2

In order to achieve this, you just need to run these commands:

    apt-get update
    apt-get install git
    apt-get install subversion
    apt-get install sqlite3
    apt-get install php5-sqlite
    apt-get install libapache2-mod-php5
    apt-get install phpunit


### 2. Deploy and configure PHPRestfulSubversion

Now we are going to download PHPRestfulSubversion and check if it works:

    cd /opt
    git clone git://github.com/ManuelKiessling/PHPRestfulSubversion.git
    cd PHPRestfulSubversion/tests
    bash ./runall.sh


This should produce an output similar to

    PHPUnit 3.5.0 by Sebastian Bergmann.

    ............................................................ 60 / 74
    ..............

    Time: 0 seconds, Memory: 6.25Mb

    OK (74 tests, 70 assertions)


The important thing is that there haven't been any failures, like this:

    FAILURES!
    Tests: 91, Assertions: 92, Failures: 1.


If everything went fine, we can go on to configure our PHPRestfulSubversion
configuration.

Basically, this means to tell PHPRestfulSubversion where your Subversion repository
is and where your Subversion cache is going to be.

Have a look at _/opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.sample.conf_ - it looks
like this:

    $config['repoCacheConnectionString'] = 'sqlite:/var/tmp/PHPRestfulSubversion.RepoCache.sqlite';
    $config['repoUri'] = 'http://svn.example.com/';
    $config['repoUsername'] = 'user';
    $config['repoPassword'] = 'password';
    $config['maxImportsPerRun'] = 100;


We are going to copy this example file to _/opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.conf_
and fill in real values according to our environment.

    cp /opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.sample.conf /opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.conf
    vim /opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.conf


(You can of course use any text editor you like for editing this file).

The first value, _repoCacheConnectionString_, is probably fine for you as it
is and you don't have to change it.

_repoUri_ is the full URI to your Subversion repository, and
_repoUsername_ and _repoPassword_ are the credentials needed to access this
repository. PHPRestfulSubversion only needs read access.

The buildCache script will stop after importing _maxImportsPerRun_ revisions.
If started again, it will continue with the next batch of revisions. It's
designed that way because some Subversion repositories might be really big,
and it makes sense to not import the whole repository at once.


### 3. Build the Subversion cache

Once you've set up this configuration file with real-life values, you can start
the command line script which reads from your Subversion repository and inserts
the changesets into the cache database:

    cd /opt/PHPRestfulSubversion/bin
    ./buildCache.php /opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.conf


In order to ensure that your repository cache is always in sync with your
repository cache, create a cronjob which will insert new revisions into the
cache regularly, like this:

    * * * * *    root    /opt/PHPRestfulSubversion/bin/buildCache.php /opt/PHPRestfulSubversion/etc/PHPRestfulSubversion.conf


### 4. Configure Apache to serve the RESTful JSON webservice

PHPRestfulSubversion allows you to query information about your Subversion
repository using a RESTful JSON webservice API. In order to serve this API,
you will need to set up a webserver which will provide access to this API via
HTTP.

Here's how to configure your Apache webserver to make it serve
PHPRestfulSubversion:

I assume you want to make the webservice available at

    http://localhost:10000/


which means you would be able to request information about revision 12345 by
calling

    http://localhost:10000/changeset/12345


via GET.

To achieve this, you need to enable the mod_rewrite Apache module:

    a2enmod rewrite


Then add the following at the end of _/etc/apache2/sites-available/default_:

    # PHPRestfulSubversion REST API
    NameVirtualHost 0.0.0.0:10000
    Listen 0.0.0.0:10000
    <VirtualHost 0.0.0.0:10000>
      DocumentRoot "/opt/PHPRestfulSubversion/public"
      DirectoryIndex index.html
      <Directory "/opt/PHPRestfulSubversion/public">
        Allow from All
        RewriteEngine On
        RewriteRule !\.(js|ico|gif|jpg|png|css|html)$ ResourceDispatcher.php
      </Directory>
    </VirtualHost>


Afterwards, you need to restart your webserver:

    /etc/init.d/apache2 force-reload


Then you can open the following URL in your browser:

    http://localhost:10000/changeset/1


This should return a JSON result set with information about the first revision
in your repository.


PHPRestfulSubversion ships with a demo HTML client for the REST webservice,
which you can use by pointing your browser at

    http://localhost:10000/DemoWebserviceClient.html


## Using the PHP library

If you want to use the PHP library that works under the hood of
PHPRestfulSubversion, here is how to integrate it into your code:

    <?php
    
    use RestfulSubversion\Core\RepoCache;
    use RestfulSubversion\Core\Revision;

    require_once '/opt/PHPRestfulSubversion/lib/RestfulSubversion/Helper/Bootstrap.php';

    $repoCache = new RepoCache(new PDO('sqlite:/var/tmp/PHPRestfulSubversion.RepoCache.sqlite', NULL, NULL));
    $revision = new Revision('12345');
    $changeset = $repoCache->getChangesetForRevision($revision);

    echo $changeset->getAuthor();


## Feedback

Any feedback is highly appreciated. You can reach me at <manuel@kiessling.net>,
or open a new issue at https://github.com/ManuelKiessling/PHPRestfulSubversion/issues
