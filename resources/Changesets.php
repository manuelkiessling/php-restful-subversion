<?php

/**
 * Changesets resource
 * @uri /changesets/with_text/{sText}
 */
class ChangesetsResource extends MergeHelper_Resource {

	public function get($oRequest) {
		$oCacheDb = new PDO($this->aConfig['sRepoCacheConnectionString'], NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);

		$aaChangesets = array();
		$aoChangesets = $oRepoCache->aoGetChangesetsWithMessageContainingText("$oRequest");
		foreach ($aoChangesets as $oChangeset) {
			$aaChangesets[] = ChangesetResource::aGetChangesetAsArray($oChangeset);
		}

		$oResponseHelper = new ResponseHelper();
		return $oResponseHelper->setResponse(new Response($oRequest), $aaChangesets);
	}

}
