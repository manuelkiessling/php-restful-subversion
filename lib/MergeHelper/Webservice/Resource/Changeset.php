<?php

/**
 * Changeset resource
 * @uri /changeset/:sRevisionNumber
 */
class MergeHelper_Webservice_Resource_Changeset extends MergeHelper_Webservice_Resource {

	public static function aGetChangesetAsArray(MergeHelper_Changeset $oChangeset) {
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
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);

		$oChangeset = $oRepoCache->oGetChangesetForRevision(new MergeHelper_Revision($sRevisionNumber));

		$oResponseHelper = new MergeHelper_Webservice_Helper_Response();
		return $oResponseHelper->setResponse(new Response($request), self::aGetChangesetAsArray($oChangeset));
	}

}
