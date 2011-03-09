# PHPMergeHelper

## About

PHPMergeHelper...

1. ...is a library of PHP classes which enable you to write tools that
   will support you with your specific Subversion workflow in your
   release management process
2. ...provides a RESTful JSON webservice API to access the resources of
   this library
3. ...provides tool to cache your Subversion repository in order to make
   it searchable through the library in a fast and simple manner

## Project Parts

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


As you can see, PHPMergeHelper never writes to the Subversion repository, and
the REST webservices are read only as of now.

Well, PHPMergeHelper ships with only little direct functionality - it's a
library in the first place. The idea is to enable you to write your own tools
on top of it to actually provide functionality and value.

However, there is a working JavaScript/HTML REST client included which allows
you to search the Subversion cache for all changesets whose commit message
contains a certain string, and for all changesets that include a changed path
whose name ends on includes a certain string ("show me all changesets which
include the file main/default.txt").
