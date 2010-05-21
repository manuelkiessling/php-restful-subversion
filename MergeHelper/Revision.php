<?php

/**
 * PHPMergeHelper
 *
 * Copyright (c) 2010, Manuel Kiessling <manuel@kiessling.net>
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
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class implementing a Mediator pattern to allow effective use of the library
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Base
 */
class MergeHelper_Revision extends MergeHelper_Base {

	private $sBegin = NULL;
	private $sEnd = NULL;
	
	/**
	 * @todo Check and Exception needed for number format
	 */
	public function __construct($sBegin, $sEnd = NULL) {
	
		parent::__preConstruct();
		$this->sBegin = $sBegin;
		$this->sEnd = $sEnd;
		parent::__construct();
	
	}
	
	public function sGetNumber() {
	
		if (is_null($this->sEnd)) return $this->sBegin;
		return $this->sBegin.':'.$this->sEnd;
	
	}
	
	public function sGetNumberInverted() {
	
		if (is_null($this->sEnd)) return '-'.$this->sBegin;
		return $this->sEnd.':'.$this->sBegin;
	
	}
	
	public function __toString() {
		return (string)$this->sGetNumber();
	}
	
	public function sGetNumberBegin() {
		return $this->sBegin;
	}
	
	public function sGetNumberEnd() {
		return $this->sEnd;
	}
	
	public function bIsRange() {
	
		if (is_null($this->sEnd)) return FALSE;
		return TRUE;
	
	}
	
}
