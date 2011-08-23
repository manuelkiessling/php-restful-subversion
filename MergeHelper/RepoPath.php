<?php

/**
 * PHPMergeHelper
 *
 * Copyright (c) 2010, Manuel Kiessling <manuel@kiessling.net>
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
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class representing the path to an object in a SVN repository
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_RepoPathInvalidPathException
 */
class MergeHelper_RepoPath {

	/**
	 * Internal string representation of the path
	 */
	protected $sPath = NULL;

	/**
	 * Creates the path object based on a given string
	 *
	 * @param string $sPath Path to create the object for
	 * @return void
	 * @throws MergeHelper_RepoPathInvalidPathException if the given string doesn't have the correct format
	 */
	public function __construct($sPath) {
		if (mb_substr($sPath, -1) === '/') throw new MergeHelper_RepoPathInvalidPathException(1);
		if ($sPath[0] !== '/') throw new MergeHelper_RepoPathInvalidPathException(2);
		if (mb_substr($sPath, -1) === '.') throw new MergeHelper_RepoPathInvalidPathException(3);
		if (mb_substr($sPath, -5) === '/.svn') throw new MergeHelper_RepoPathInvalidPathException(4);
		if (mb_strstr($sPath, '..')) throw new MergeHelper_RepoPathInvalidPathException(5);
		if (mb_substr($sPath, -2) === '..') throw new MergeHelper_RepoPathInvalidPathException(6);
		$this->sPath = $sPath;
	}

	public function sGetAsString() {
		return $this->sPath;
	}
	
	public function __toString() {
		return $this->sGetAsString();
	}

}
