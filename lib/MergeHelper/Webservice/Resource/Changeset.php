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
 * @package    MergeHelper
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * @uri /changeset/:sRevisionNumber
 * @category   VersionControl
 * @package    MergeHelper
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */
class MergeHelper_Webservice_Resource_Changeset extends MergeHelper_Webservice_Resource {

	public static function aGetChangesetAsArray(MergeHelper_Core_Changeset $oChangeset) {
		$aChangeset = array();
		$aChangeset['sRevisionNumber'] = $oChangeset->oGetRevision()->sGetAsString();
		$aChangeset['sAuthor'] = $oChangeset->sGetAuthor();
		$aChangeset['sDateTime'] = $oChangeset->sGetDateTime();
		$aChangeset['sMessage'] = $oChangeset->sGetMessage();

		$aChangeset['aaPathoperations'] = array();

		$aaPathoperations = $oChangeset->aaGetPathOperations();
		foreach ($aaPathoperations as $aPathoperation) {
			$aThisPathoperation = array();
			$aThisPathoperation['sAction'] = $aPathoperation['sAction'];
			$aThisPathoperation['sPath'] = $aPathoperation['oPath']->sGetAsString();
			if (array_key_exists('oCopyfromPath', $aPathoperation) && is_object($aPathoperation['oCopyfromPath'])) $aThisPathoperation['sCopyfromPath'] = $aPathoperation['oCopyfromPath']->sGetAsString();
			if (array_key_exists('oCopyfromRev', $aPathoperation) && is_object($aPathoperation['oCopyfromRev'])) $aThisPathoperation['sCopyfromRev'] = $aPathoperation['oCopyfromRev']->sGetAsString();
			$aChangeset['aaPathoperations'][] = $aThisPathoperation;
		}
		return $aChangeset;
	}

	public function get($request, $sRevisionNumber) {
		$oCacheDb = new PDO($this->aConfig['sRepoCacheConnectionString'], NULL, NULL);
		$oRepoCache = new MergeHelper_Core_RepoCache($oCacheDb);

		$oChangeset = $oRepoCache->oGetChangesetForRevision(new MergeHelper_Core_Revision($sRevisionNumber));

		$oResponseHelper = new MergeHelper_Webservice_Helper_Response();
		return $oResponseHelper->setResponse(new Response($request), self::aGetChangesetAsArray($oChangeset));
	}

}
