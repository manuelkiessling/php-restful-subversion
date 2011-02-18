<?php

/**
 * PHPMergeHelper
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
 * @package    PHPMergeHelper
 * @subpackage Command
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class that can interprete the XML output of svn log
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Utility
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */
class MergeHelper_RepoLogInterpreter {

	public function oGetChangesetFromXml($sXml) {
		$oXml = new SimpleXMLElement($sXml);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision((string)$oXml->logentry[0]['revision']));
		$oChangeset->setAuthor((string)$oXml->logentry[0]->author);
		$oChangeset->setDateTime(date('Y-m-d H:i:s', strtotime($oXml->logentry[0]->date)));
		$oChangeset->setMessage((string)$oXml->logentry[0]->msg);

		foreach ($oXml->logentry[0]->paths[0] as $oPath) {
			$oCopyfromPath = NULL;
			$oCopyfromRev = NULL;
			if ($oPath['copyfrom-path']) $oCopyfromPath = new MergeHelper_RepoPath((string)$oPath['copyfrom-path']);
			if ($oPath['copyfrom-rev']) $oCopyfromRev = new MergeHelper_Revision((string)$oPath['copyfrom-rev']);
			$oChangeset->addPathOperation((string)$oPath['action'],
			                              new MergeHelper_RepoPath((string)$oPath),
			                              $oCopyfromPath,
			                              $oCopyfromRev);
		}

		return $oChangeset;
	}

}
