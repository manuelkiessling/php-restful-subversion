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

/**
 * Class representing a set of changes in the repository identified by a revision
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 * @uses       Revision
 * @uses       RepoPath
 */
class Changeset
{
    protected $revision = NULL;
    protected $author = NULL;
    protected $dateTime = NULL;
    protected $message = NULL;
    protected $pathOperations = array();

    public function __construct(Revision $revision)
    {
        $this->revision = $revision;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function addPathOperation($action, RepoPath $path, RepoPath $copyfromPath = NULL, Revision $copyfromRev = NULL)
    {
        $pathOperation = array('action' => $action,
                               'path' => $path);
        if ($copyfromPath) $pathOperation['copyfromPath'] = $copyfromPath;
        if ($copyfromRev) $pathOperation['copyfromRev'] = $copyfromRev;
        $this->pathOperations[] = $pathOperation;
    }

    public function getRevision()
    {
        return $this->revision;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getPathOperations()
    {
        return $this->pathOperations;
    }
}
