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
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */

/**
 * Class which allows construction of a command line
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 * @implements RestfulSubversion_Core_CommandLineBuilderInterface
 */
class RestfulSubversion_Core_CommandLineBuilder implements RestfulSubversion_Core_CommandLineBuilderInterface {

    protected $numberOfArguments;
    protected $command;
    protected $parameters;
    protected $shortSwitches;
    protected $longSwitches;

    public function __construct() {
        $this->reset();
    }

    public function reset() {
        $this->numberOfArguments = 0;
        $this->command = '';
        $this->parameters = array();
        $this->shortSwitches = array();
        $this->longSwitches = array();
    }

    public function setCommand($command) {
        $this->command = $command;
    }
    
    public function addParameter($sParameterName) {
        $this->parameters[$this->numberOfArguments] = $sParameterName;
        $this->numberOfArguments++;
    }
    
    public function addShortSwitch($sSwitchName) {
        $this->addShortSwitchWithValue($sSwitchName, '');
    }
    
    public function addShortSwitchWithValue($sSwitchName, $sSwitchValue) {
        $this->shortSwitches[$this->numberOfArguments] = array('sSwitchName' => $sSwitchName, 'sSwitchValue' => $sSwitchValue);
        $this->numberOfArguments++;
    }
    
    public function addLongSwitch($sSwitchName) {
        $this->addLongSwitchWithValue($sSwitchName, '');
    }
    
    public function addLongSwitchWithValue($sSwitchName, $sSwitchValue) {
        $this->longSwitches[$this->numberOfArguments] = array('sSwitchName' => $sSwitchName, 'sSwitchValue' => $sSwitchValue);
        $this->numberOfArguments++;
    }
    
    public function getCommandLine() {
        $return = $this->command;

        for ($i = 0; $i < $this->numberOfArguments; $i++) {
            
            if (isset($this->parameters[$i])) $return .= ' '.$this->parameters[$i];
            
            if (isset($this->shortSwitches[$i])) {
                $return .= ' -'.$this->shortSwitches[$i]['sSwitchName'];
                if ($this->shortSwitches[$i]['sSwitchValue'] !== '') {
                    $return .= ' '.$this->shortSwitches[$i]['sSwitchValue'];
                }
            }
            
            if (isset($this->longSwitches[$i])) {
                $return .= ' --'.$this->longSwitches[$i]['sSwitchName'];
                if ($this->longSwitches[$i]['sSwitchValue'] !== '') {
                    $return .= '='.$this->longSwitches[$i]['sSwitchValue'];
                }
            }
            
        }
        return $return;
    }

}
