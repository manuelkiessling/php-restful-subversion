<?php

/**
 * PHPRestfulSubversion
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
 * @package    RestfulSubversion
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */

namespace RestfulSubversion\Webservice\Helper;

/**
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */
class ResponseTransformer
{
    /**
     * @param \Response $response
     * @param $body
     * @param null|string $callback Callback name to put the response into
     * @return \Response
     */
    public function setResponse(\Response $response, $body, $callback = null)
    {
        $response->code = \Response::OK;
        $response->addHeader('Content-Type', 'application/json');
        if (is_string($callback)) {
            $response->body = $callback . '(' . json_encode($body) . ');';
        } else {
            $response->body = json_encode($body);
        }
        return $response;
    }

    /**
     * @param \Response $response
     * @param string $errorMessage
     * @param null|string $callback Callback name to put the response into
     * @return \Response
     */
    public function setFailedResponse(\Response $response, $errorMessage = 'This request is not valid.', $callback = null)
    {
        $response->code = \Response::BADREQUEST;
        if (is_string($callback)) {
            $response->body = $callback . '(' . json_encode(array('error' => true, 'errorMessage' => $errorMessage)) . ');';
        } else {
            $response->body = json_encode(array('error' => true, 'error' => $errorMessage));
        }
        return $response;
    }
}
