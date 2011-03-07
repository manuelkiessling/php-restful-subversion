<?php

/**
 * Changesets resource
 * @uri /changesets
 */
class MergeHelper_Webservice_Resource_Changesets extends MergeHelper_Webservice_Resource {

	public function get($request) {
		$oResponseHelper = new MergeHelper_Webservice_Helper_Response();

		if (isset($_GET['with_message_containing'])) {
			$sSearchMode = 'with_message_containing';
			$sSearchTerm = $_GET['with_message_containing'];
		} elseif (isset($_GET['with_path_ending_on'])) {
			$sSearchMode = 'with_path_ending_on';
			$sSearchTerm = $_GET['with_path_ending_on'];
		} else {
			return $oResponseHelper->setFailedResponse(new Response($request), "You can't request an unfiltered list of all changesets. Use changesets?with_message_containing=<text> or changesets?with_path_ending_on=<text> instead.");
		}

		if (isset($_GET['callback'])) {
			$sCallback = $_GET['callback'];
		}

		$oCacheDb = new PDO($this->aConfig['sRepoCacheConnectionString'], NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
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
