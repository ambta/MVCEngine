<?php
/*
 * This file is part of the Ambta MVCEngine component.
 *
 * (c) Ambta <info@ambta.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ambta\Component\MVCEngine\Application;

use Ambta\Component\MVCEngine\Helper\Message\Error as ERROR;

/**
 * Config, used as abstraction layer to hold the configuration, performs checks for mandatory properties
 *
 * Supports PHP 5.4+
 *
 * @package     Ambta\Component\MVCEngine\Application
 * @version     0.0.1
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
class Config
{
    /* @var string */
    private $applicationNamespace;

    /* @var string */
    private $applicationRoot;

    /* @var string */
    private $applicationBaseUrl;

    /**
     * Constructor, checks for mandatory properties and sets properties
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param stdClass $jObjConfig A json decoded object
     * @throws \Exception
     */
    public function __construct($jObjConfig)
    {
        /**
        * Mandatory route property check
        */
        if(true === empty($jObjConfig))
        {
            throw new \Exception(ERROR::APPLICATION_CONFIGURATION_INVALID_JSON_OBJECT);
        }

        if(true !== property_exists($jObjConfig, "application"))
        {
            throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_MISSING, 'application'));
        }else{
            /**
             * Check namespace
             */
            if(true !== property_exists($jObjConfig->application, "namespace"))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_MISSING, 'namespace'));
            }else{
                $namespace = $jObjConfig->application->namespace;
                if(true === empty($namespace))
                {
                    throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_EMPTY, 'namespace'));
                }
                $this->applicationNamespace = $namespace;
            }

            /**
             * Check application root
             */
            if(true !== property_exists($jObjConfig->application, "root"))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_MISSING, 'root'));
            }else{
                $root = $jObjConfig->application->root;
                if(true === empty($root))
                {
                    throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_EMPTY, 'root'));
                }
                $this->applicationRoot = $root;
            }

            /**
             * Check application base_url
             */
            if(true !== property_exists($jObjConfig->application, "base_url"))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_MISSING, 'base_url'));
            }else{
                $baseUrl = $jObjConfig->application->base_url;
                if(true === empty($baseUrl))
                {
                    throw new \Exception(sprintf(ERROR::APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_EMPTY, 'base_url'));
                }
                $this->applicationBaseUrl = $baseUrl;
            }
        }
    }

    /**
     * @return string
     */
    public function getApplicationNamespace()
    {
        return $this->applicationNamespace;
    }

    /**
     * @return string
     */
    public function getApplicationRoot()
    {
        return $this->applicationRoot;
    }

    /**
     * @return string
     */
    public function getApplicationBaseUrl()
    {
        return $this->applicationBaseUrl;
    }
}