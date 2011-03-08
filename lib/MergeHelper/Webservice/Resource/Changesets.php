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
 * Changesets resource
 * @uri /changesets
 * @category   VersionControl
 * @package    MergeHelper
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */
class MergeHelper_Webservice_Resource_Changesets extends MergeHelper_Webservice_Resource {

	public function get($request) {
		$oResponseHelper = new MergeHelper_Webservice_Helper_Response();

		$sCallback = NULL;
		if (isset($_GET['callback'])) {
			$sCallback = $_GET['callback'];
		}

		if (isset($_GET['with_message_containing'])) {
			$sSearchMode = 'with_message_containing';
			$sSearchTerm = $_GET['with_message_containing'];
		} elseif (isset($_GET['with_path_ending_on'])) {
			$sSearchMode = 'with_path_ending_on';
			$sSearchTerm = $_GET['with_path_ending_on'];
		} else {
			return $oResponseHelper->setFailedResponse(new Response($request), "You can't request an unfiltered list of all changesets. Use changesets?with_message_containing=TEXT or changesets?with_path_ending_on=TEXT instead.", $sCallback);
		}

		$oCacheDb = new PDO($this->aConfig['sRepoCacheConnectionString'], NULL, NULL);
		$oRepoCache = new MergeHelper_Core_RepoCache($oCacheDb);
		$aaChangesets = array();

		if ($sSearchMode == 'with_message_containing') {
			$aoChangesets = $oRepoCache->aoGetChangesetsWithMessageContainingText($sSearchTerm);
		} elseif ($sSearchMode == 'with_path_ending_on') {
			$aoChangesets = $oRepoCache->aoGetChangesetsWithPathEndingOn($sSearchTerm);
		}

		foreach ($aoChangesets as $oChangeset) {
			$aaChangesets[] = MergeHelper_Webservice_Resource_Changeset::aGetChangesetAsArray($oChangeset);
		}

		return $oResponseHelper->setResponse(new Response($request), array('changesets' => $aaChangesets), $sCallback);
	}

}
