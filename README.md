# PHPMergeHelper

## About

PHPMergeHelper...

1. ...is a library of PHP classes which enable you to write tools that
   will support you with your specific Subversion workflow in your
   release management process
2. ...provides a RESTful JSON webservice API to access the resources of
   this library
3. ...provides tools to cache your Subversion repository in order to make
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
the REST webservices are read-only as of now.

Well, PHPMergeHelper ships with only little direct functionality - it's a
library in the first place. The idea is to enable you to write your own tools
on top of it to actually provide functionality and value.

However, there is a working JavaScript/HTML REST client included which allows
you to search the Subversion cache for all changesets whose commit message
contains a certain string, and for all changesets that include a changed path
whose name ends on includes a certain string ("show me all changesets which
include the file main/default.txt").


## How PHPMergeHelper may help you with your approval and release process

As said PHPMergeHelper is a collection of PHP classes which allow developers to
easily create tailor-made tools for merge- and releasemanagement within SVN
repositories.

In my opinion, these tailor-made tools are necessary for two reasons:

1. The available Subversion client tools do a great job in assisting us at
merging whole branches or single commits, but they don't provide any support
for the high-level tasks of release management, e.g. when we need to find all
commits which belong to a certain project or when we need to sort out which
commits of a certain project are already merged and released and which aren't.

2. Every team of software developers does release management different.
Therefore, it's difficult (if not impossible) to create a one-size-fits-all
solution, which means that it should be easy for every team to build its own
tool.

Because it's only a collection of classes, PHPMergeHelper doesn't provide an
instant solution.

But it allows you to easily build your own tool which allows your release
manager to shortcut and even automate many tedious and time-consuming SVN
tasks.

PHPMergeHelper implements these use cases:

- "Give me a list of all revisions whose commit message contains a given string"

- "For a given full path, give me the root path of the branch the full path belongs to"

- "For a given revision, give me the root path of the branch this revision was applied to"

- "Tell me if a given list of commits all apply to the same branch"

- "Give me a list of all file and folder paths that were changed by a given commit"

By combining these use cases, you could, for example, create a tool which
allows your release manager to enter a list of issue numbers (from the
bugtracker your team uses, provided that all your developers always put the
relating issue numbers into the commit messages), whereupon the tool shows him
a chronologically ordered list of all commits for these issues, the command
lines neccessary to merge these issues to the release branch, and warn him if
there have already been release-commits for these issues.
