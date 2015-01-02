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

/**
 * Routing, in charge of being an determined route object after initialization
 *
 * Supports PHP 5.4+
 *
 * @package     Ambta\Component\MVCEngine\Application
 * @version     0.0.1
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
class Routing
{
    /* @var string */
    private $name;

    /* @var string */
    private $pattern;

    /* @var string */
    private $controller;

    /* @var string */
    private $action;

    /* @var array */
    private $params;

    /**
     * Constructor, checks if mandatory routing properties are set
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param $name
     * @param $pattern
     * @param $controller
     * @param $action
     * @param array $params
     * @throws \Exception
     */
    public function __construct($name, $pattern, $controller, $action, $params = array())
    {
        /**
         * Mandatory route property check
         */
        if(true === empty($pattern) || true === empty($controller) || true === empty($action))
        {
            throw new \Exception("Mandatory routing property missing");
        }

        if(substr($action, -6) != "Action")
        {
            $action = $action."Action";
        }

        $this->name = $name;
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}