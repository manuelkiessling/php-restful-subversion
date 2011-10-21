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
 * @subpackage Helper
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */

namespace RestfulSubversion\Helper;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


require_once realpath(dirname(__FILE__)) . '/Autoloader.php';

spl_autoload_register('RestfulSubversion\Helper\Autoloader::load');
date_default_timezone_set('Europe/Berlin');

/**
 * Provides basic helper functions for the library
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Helper
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */
class Bootstrap
{
    /**
     * @static
     * @return string
     */
    public static function getLibraryRoot()
    {
        return realpath(dirname(__FILE__) . '/../');
    }
    
    /**
     * Bootstraps the Doctrine vendor library into the environment and returns an EntityManager based on the given options
     * @param array $options
     * @return \Doctrine\ORM\EntityManager Doctrine EntityManager
     */
    public function setupAndReturnDoctrineEntityManager(Array $options)
    {
        if (array_key_exists('libPath', $options)) {
            $libPath = $options['libPath'];
        } else {
            // sensible default
            $libPath = __DIR__ . '/../../../vendor/doctrine/lib/Doctrine/ORM/Tools/Setup.php';
        }

        require $libPath;
        \Doctrine\ORM\Tools\Setup::registerAutoloadDirectory(__DIR__ . '/../../../vendor/doctrine/lib');
        
        if (array_key_exists('isDevMode', $options)) {
            $isDevMode = $options['isDevMode'];
        } else {
            $isDevMode = false;
        }
        
        if (!array_key_exists('connectionOptions', $options)) {
            throw new \Exception('No database connection options provided!');
        }
        $connectionOptions = $options['connectionOptions'];
        
        $paths = array(__DIR__ . '/../Core/Entity');
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, $cache);
        return EntityManager::create($connectionOptions, $config);
    }
}
