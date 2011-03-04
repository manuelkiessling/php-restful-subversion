<?php

/**
 * Changesets resource
 * @uri /changesets/{sFirstParam}/{sSecondParam}
 */
class ChangesetsResource extends MergeHelper_Resource {

	public function get($request, $sFirstParam = NULL, $sSecondParam = NULL) {
		$oCacheDb = new PDO($this->aConfig['sRepoCacheConnectionString'], NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$oResponseHelper = new ResponseHelper();
		$aaChangesets = array();

		if ($sFirstParam == 'with_message_containing') {
			$aoChangesets = $oRepoCache->aoGetChangesetsWithMessageContainingText($sSecondParam);
		} elseif ($sFirstParam == 'with_path_ending_on') {
			$aoChangesets = $oRepoCache->aoGetChangesetsWithPathEndingOn($sSecondParam);
		} else {
			$oResponseHelper->setFailedResponse(new Response($request));
		}

		foreach ($aoChangesets as $oChangeset) {
			$aaChangesets[] = ChangesetResource::aGetChangesetAsArray($oChangeset);
		}

		return $oResponseHelper->setResponse(new Response($request), $aaChangesets);
	}

}
