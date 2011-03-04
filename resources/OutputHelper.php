<?php

class ResponseHelper {

	public function setResponse(Response $oResponse, $mBody) {
		$oResponse->code = Response::OK;
		$oResponse->addHeader('Content-Type', 'application/json');
 		$oResponse->body = json_encode($mBody);
		return $oResponse;
	}

	public function setFailedResponse(Response $oResponse) {
		$oResponse->code = Response::NOTFOUND;
		return $oResponse;
	}

}
