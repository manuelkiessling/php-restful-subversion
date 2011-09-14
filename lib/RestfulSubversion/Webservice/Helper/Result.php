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

/**
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Webservice
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */
class RestfulSubversion_Webservice_Helper_Result {

    public static function aGetChangesetAsArray(RestfulSubversion_Core_Changeset $oChangeset) {
        $aChangeset = array();
        $aChangeset['revision'] = $oChangeset->oGetRevision()->sGetAsString();
        $aChangeset['author'] = $oChangeset->sGetAuthor();
        $aChangeset['datetime'] = $oChangeset->sGetDateTime();
        $aChangeset['message'] = $oChangeset->sGetMessage();

        $aChangeset['pathoperations'] = array();

        $aaPathoperations = $oChangeset->aaGetPathOperations();
        foreach ($aaPathoperations as $aPathoperation) {
            $aThisPathoperation = array();
            $aThisPathoperation['action'] = $aPathoperation['sAction'];
            $aThisPathoperation['path'] = $aPathoperation['oPath']->sGetAsString();
            if (array_key_exists('copyfrompath', $aPathoperation) && is_object($aPathoperation['oCopyfromPath'])) $aThisPathoperation['sCopyfromPath'] = $aPathoperation['oCopyfromPath']->sGetAsString();
            if (array_key_exists('copyfromrev', $aPathoperation) && is_object($aPathoperation['oCopyfromRev'])) $aThisPathoperation['sCopyfromRev'] = $aPathoperation['oCopyfromRev']->sGetAsString();
            $aChangeset['pathoperations'][] = $aThisPathoperation;
        }
        return $aChangeset;
    }

}
