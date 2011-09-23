#!/usr/bin/php
<?php

use RestfulSubversion\Core\Repo;
use RestfulSubversion\Core\RepoCache;
use RestfulSubversion\Core\RepoLogInterpreter;
use RestfulSubversion\Core\RepoCommandLog;
use RestfulSubversion\Core\RepoInfoInterpreter;
use RestfulSubversion\Core\RepoCommandInfo;
use RestfulSubversion\Core\RepoCommandPropget;
use RestfulSubversion\Core\RepoCommandCat;
use RestfulSubversion\Core\Revision;
use RestfulSubversion\Core\RepoFile;
use RestfulSubversion\Helper\CommandLineBuilder;
use RestfulSubversion\Helper\CommandLineExecutor;


function displayErrorWithUsageInformationAndExit($error)
{
    echo "\n";
    echo 'ERROR: '.$error;

    $info = <<<EOT


Usage information:

There are two ways of using this script:

1: You provide the path to a file containing the necessary SVN and
   cache db information

   or

2: You provide the SVN repository location, a valid SVN username and
   password, and a PDO compatible cache db connection string directly


Example for variant 1:

   buildCache.php /path/to/PHPRestfulSubversion.conf

See file PHPRestfulSubversion.sample.conf as an example of how such a config
file needs to be designed.


Example for variant 2:

   buildCache.php http://svn.example.com/ user pass sqlite:/var/tmp/svncache.db 100

This last parameter is optional and defines how many revisions should be
imported to the cache during the run of this script. After reaching this
amount of imports, the script will exit. If no value is provided, an unlimited
number of revisions will be imported (of course limited by the maximum number
of revisions that are in your repository).

EOT;

    echo $info;
    exit(1);
}

function getHighestRevisionInRepo(Repo $repo)
{
    $commandLineExecutor = new CommandLineExecutor();
    $commandLineBuilder = new CommandLineBuilder();
    $logInterpreter = new RepoLogInterpreter();

    $commandLog = new RepoCommandLog($repo, $commandLineBuilder);
    $commandLog->enableVerbose();
    $commandLog->enableXml();
    $commandLog->setRevision(new Revision('HEAD'));
    $commandline = $commandLog->getCommandline();
    $logOutput = $commandLineExecutor->getCommandResult($commandline);

    $changesets = $logInterpreter->createChangesetsFromVerboseXml($logOutput);

    foreach ($changesets as $changeset) {
        return (int)$changeset->getRevision()->getAsString();
    }
}

if (!array_key_exists(1, $argv)) {
    displayErrorWithUsageInformationAndExit("You need to provide either the path to a valid PHPRestfulSubversion.conf file or the location of a SVN repository, a SVN username and password, and a PDO compatible connection string.");
}

if (is_file($argv[1])) {
    require_once $argv[1];
    $repoUri = $configValues['repoUri'];
    $repoUsername = $configValues['repoUsername'];
    $repoPassword = $configValues['repoPassword'];
    $repoCacheConnectionString = $configValues['repoCacheConnectionString'];
    $maxImportsPerRun = 0;
    if (array_key_exists('maxImportsPerRun', $configValues)) {
        $maxImportsPerRun = $configValues['maxImportsPerRun'];
    }
} else {
    $repoUri = $argv[1];

    if (!array_key_exists(2, $argv)) {
        displayErrorWithUsageInformationAndExit("You need to provide a SVN username.");
    }
    $repoUsername = $argv[2];

    if (!array_key_exists(3, $argv)) {
        displayErrorWithUsageInformationAndExit("You need to provide a SVN password.");
    }
    $repoPassword = $argv[3];

    if (!array_key_exists(4, $argv)) {
        displayErrorWithUsageInformationAndExit("You need to provide a PDO compatible connection string.");
    }
    $repoCacheConnectionString = $argv[4];

    $maxImportsPerRun = 0;
    if (array_key_exists(5, $argv)) {
        $maxImportsPerRun = (int)$argv[5];
    }
}

if (empty($repoUri)) {
    displayErrorWithUsageInformationAndExit("No repository URI given.");
}

if (empty($repoUsername)) {
    displayErrorWithUsageInformationAndExit("No repository username given.");
}

if (empty($repoPassword)) {
    displayErrorWithUsageInformationAndExit("No repository password given.");
}

if (empty($repoCacheConnectionString)) {
    displayErrorWithUsageInformationAndExit("No cache db connection string given.");
}

require_once('../lib/RestfulSubversion/Helper/Bootstrap.php');

$repo = new Repo();
$repo->setUri($repoUri);
$repo->setAuthinfo($repoUsername, $repoPassword);

$commandLineExecutor = new CommandLineExecutor();
$commandLineBuilder = new CommandLineBuilder();
$logInterpreter = new RepoLogInterpreter();
$repoCache = new RepoCache(new PDO($repoCacheConnectionString, null, null));

$highestRevisionInRepo = getHighestRevisionInRepo($repo);
$highestRevisionInRepoCache = 0;
$revision = $repoCache->getHighestRevision();
if (is_object($revision)) $highestRevisionInRepoCache = (int)$revision->getAsString();

echo 'Highest revision found in repository: '.$highestRevisionInRepo."\n";
if ($highestRevisionInRepoCache == 0) {
    echo 'Cache database is empty, starting from scratch'."\n";
    $currentRevision = 1;
} else {
    echo 'Highest revision found in cache database: '.$highestRevisionInRepoCache."\n";
    $currentRevision = $highestRevisionInRepoCache + 1;
}

$i = 0;
while ($currentRevision <= $highestRevisionInRepo) {
    if ($maxImportsPerRun != 0 && $i == $maxImportsPerRun) {
        echo "\nImported the maximum number of $i revisions for this run, exiting.\n";
        exit(0);
    }

    echo "\n";
    echo 'About to import revision '.$currentRevision.": ";

    $revision = new Revision((string)$currentRevision);

    $commandLog = new RepoCommandLog($repo, $commandLineBuilder);
    $commandLog->enableVerbose();
    $commandLog->enableXml();
    $commandLog->setRevision(new Revision((string)$currentRevision));
    $commandline = $commandLog->getCommandline();
    $logOutput = $commandLineExecutor->getCommandResult($commandline);

    $changesets = $logInterpreter->createChangesetsFromVerboseXml($logOutput);

    $commandPropget = new RepoCommandPropget($repo, $commandLineBuilder);
    $commandInfo = new RepoCommandInfo($repo, $commandLineBuilder);
    $commandCat = new RepoCommandCat($repo, $commandLineBuilder);
    $infoInterpreter = new RepoInfoInterpreter();
    foreach ($changesets as $changeset) {
        $pathOperations = $changeset->getPathOperations();
        foreach ($pathOperations as $pathOperation) {
            // get mime-type
            $commandPropget->setRevision($changeset->getRevision());
            $commandPropget->setPath($pathOperation['path']);
            $commandPropget->setPropname('svn:mime-type');
            $commandline = $commandPropget->getCommandline();
            $logOutput = $commandLineExecutor->getCommandResult($commandline);
            $mimeType = trim($logOutput);
            
            if ($mimeType === '') {
                // get kind
                $commandInfo->setRevision($changeset->getRevision());
                $commandInfo->setPath($pathOperation['path']);
                $commandInfo->enableXml();
                $commandline = $commandInfo->getCommandline();
                $logOutput = $commandLineExecutor->getCommandResult($commandline);
                
                $kind = $infoInterpreter->getKindFromXml($logOutput);
                if ($kind == 'file') {
                    $file = new RepoFile($changeset->getRevision(), $pathOperation['path']);
                    $commandCat->setRevision($changeset->getRevision());
                    $commandCat->setPath($pathOperation['path']);
                    $commandline = $commandCat->getCommandline();
                    $content = $commandLineExecutor->getCommandResult($commandline);
                    $file->setContent($content);
                    $repoCache->addRepoFile($file);
                }
            }
        }
        
        $repoCache->addChangeset($changeset);
        $currentRevision++;
    }
    $i++;

    echo "done";
}

echo "\n";
echo "All revisions imported to cache.\n";
exit(0);
