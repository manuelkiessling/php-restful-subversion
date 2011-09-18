<?php

/**
 * PHPRestfulSubversion
 *
 * Copyright (c) 2011, Manuel Kiessling <manuel@kiessling.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Manuel Kiessling nor the names of its contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\CommandLineBuilderInterface;

/**
 * Class which allows to build a svn log command line
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 * @uses       Repo
 * @uses       Revision
 * @uses       CommandLineBuilderInterface
 */
class RepoCommandLog
{
    protected $repo = null;
    protected $revision = null;
    protected $range = null;
    protected $verbose = false;
    protected $xml = false;
    protected $commandLineBuilder = null;

    /**
     * @param Repo $repo
     * @param \RestfulSubversion\Helper\CommandLineBuilderInterface $commandLineBuilder
     */
    public function __construct(Repo $repo, CommandLineBuilderInterface $commandLineBuilder)
    {
        $this->repo = $repo;
        $this->commandLineBuilder = $commandLineBuilder;
    }

    /**
     * @param Revision $revision
     * @return void
     */
    public function setRevision(Revision $revision)
    {
        $this->revision = $revision;
    }

    /**
     * Makes this a verbose svn log command
     * @return void
     */
    public function enableVerbose()
    {
        $this->verbose = true;
    }

    /**
     * Enable XML result format
     * @return void
     */
    public function enableXml()
    {
        $this->xml = true;
    }

    /**
     * @return string The built command line
     */
    public function getCommandline()
    {
        $this->commandLineBuilder->reset();
        $this->commandLineBuilder->setCommand('svn');
        $this->commandLineBuilder->addParameter('log');
        $this->commandLineBuilder->addLongSwitch('no-auth-cache');
        $this->commandLineBuilder->addLongSwitchWithValue('username', $this->repo->getUsername());
        $this->commandLineBuilder->addLongSwitchWithValue('password', $this->repo->getPassword());

        if (is_object($this->revision)) {
            $this->commandLineBuilder->addShortSwitchWithValue('r', $this->revision->getAsString());
        }

        if ($this->verbose) $this->commandLineBuilder->addShortSwitch('v');
        if ($this->xml) $this->commandLineBuilder->addLongSwitch('xml');

        $this->commandLineBuilder->addParameter($this->repo->getUri());

        return $this->commandLineBuilder->getCommandLine();
    }
}
