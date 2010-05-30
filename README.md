# PHPMergeHelper

** A collection of PHP classes which make SVN merge management simple. **

## What is PHPMergeHelper good for?

PHPMergeHelper is a collection of PHP classes which allow developers to easily create tailor-made
tools for merge- and releasemanagement within SVN repositories.

In my opinion, these tailor-made tools are necessary for two reasons:

First: The available Subversion client tools do a great job in assisting us at merging whole
branches or single commits, but they don't provide any support for the high-level tasks of release
management, e.g. when we need to find all commits which belong to a certain project or when we need
to sort out which commits of a certain project are already merged and released and which aren't.

Second: Every team of software developers does release management different. Therefore, it's
difficult (if not impossible) to create a one-size-fits-all solution, which means that it should be
easy for every team to build its own tool.

Because it's only a collection of classes, PHPMergeHelper doesn't provide an instant solution!

But it allows you to easily build your own tool which allows your release manager to shortcut and
even automate many tedious and time-consuming SVN tasks.

PHPMergeHelper implements these use cases:

- "Give me a list of all revisions whose commit message contains a given string"

- "For a given full path, give me the root path of the branch the full path belongs to"

- "For a given revision, give me the root path of the branch this revision was applied to"

- "Tell me if a given list of commits all apply to the same branch"

- "Give me a list of all file and folder paths that were changed by a given commit"

By combining these use cases, you could, for example, create a tool which allows your release
manager to enter a list of issue numbers (from the bugtracker your team uses, provided that all your
developers always put the relating issue numbers into the commit messages), whereupon the tool
shows him a chronologically ordered list of all commits for these issues, the command lines
neccessary to merge these issues, and warn him if there are already commits in older releases for
certain issues.
