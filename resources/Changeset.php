<?php

/**
 * Changeset resource
 * @uri /changeset
 */
class ChangesetResource extends Resource {

	function get($oRequest) {
		$oResponse = new Response($oRequest);
		$oResponse->code = Response::OK;
		$oResponse->addHeader('Content-Type', 'text/plain');
		$oResponse->body = 'This is only an example';
		return $oResponse;
	}

}
