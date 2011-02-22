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
 * @subpackage Command
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Object representing a command line on a shell
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Command
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @implements MergeHelper_CommandLineInterface
 */
class MergeHelper_CommandLineBuilder implements MergeHelper_CommandLineBuilderInterface {

	protected $iNumberOfArguments;
	protected $sCommand;
	protected $asParameters;
	protected $aaShortSwitches;
	protected $aaLongSwitches;

	public function __construct() {
		$this->reset();
	}

	public function reset() {
		$this->iNumberOfArguments = 0;
		$this->sCommand = '';
		$this->asParameters = array();
		$this->aaShortSwitches = array();
		$this->aaLongSwitches = array();
	}

	public function setCommand($sCommand) {
		$this->sCommand = $sCommand;
	}
	
	public function addParameter($sParameterName) {
		$this->asParameters[$this->iNumberOfArguments] = $sParameterName;
		$this->iNumberOfArguments++;
	}
	
	public function addShortSwitch($sSwitchName) {
		$this->addShortSwitchWithValue($sSwitchName, '');
	}
	
	public function addShortSwitchWithValue($sSwitchName, $sSwitchValue) {
		$this->aaShortSwitches[$this->iNumberOfArguments] = array('sSwitchName' => $sSwitchName, 'sSwitchValue' => $sSwitchValue);
		$this->iNumberOfArguments++;
	}
	
	public function addLongSwitch($sSwitchName) {
		$this->addLongSwitchWithValue($sSwitchName, '');
	}
	
	public function addLongSwitchWithValue($sSwitchName, $sSwitchValue) {
		$this->aaLongSwitches[$this->iNumberOfArguments] = array('sSwitchName' => $sSwitchName, 'sSwitchValue' => $sSwitchValue);
		$this->iNumberOfArguments++;
	}
	
	public function sGetCommandLine() {
		$return = $this->sCommand;

		for ($i = 0; $i < $this->iNumberOfArguments; $i++) {
			
			if (isset($this->asParameters[$i])) $return .= ' '.$this->asParameters[$i];
			
			if (isset($this->aaShortSwitches[$i])) {
				$return .= ' -'.$this->aaShortSwitches[$i]['sSwitchName'];
				if ($this->aaShortSwitches[$i]['sSwitchValue'] !== '') {
					$return .= ' '.$this->aaShortSwitches[$i]['sSwitchValue'];
				}
			}
			
			if (isset($this->aaLongSwitches[$i])) {
				$return .= ' --'.$this->aaLongSwitches[$i]['sSwitchName'];
				if ($this->aaLongSwitches[$i]['sSwitchValue'] !== '') {
					$return .= '='.$this->aaLongSwitches[$i]['sSwitchValue'];
				}
			}
			
		}
		return $return;
	}

}
