<?php

class MergeHelper_Webservice_Helper_Response {

	public function setResponse(Response $oResponse, $mBody, $sCallback) {
		$oResponse->code = Response::OK;
		$oResponse->addHeader('Content-Type', 'application/json');
 		$oResponse->body = $sCallback.'('.json_encode($mBody).');';
		return $oResponse;
	}

	public function setFailedResponse(Response $oResponse, $sErrorMessage = 'This request is not valid.') {
		$oResponse->code = Response::NOTFOUND;
		$oResponse->body = json_encode(array('error' => $sErrorMessage));
		return $oResponse;
	}

}
