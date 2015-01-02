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

use Ambta\Component\MVCEngine\Application\Responses\Response;
use Ambta\Component\MVCEngine\Helper\Message\Error as ERROR;
use Ambta\Component\MVCEngine\Kernel;

/**
 * Controller, used to identify controllers and provides basic methods for developers.
 *
 * Supports PHP 5.4+
 *
 * @package     Ambta\Component\MVCEngine\Application
 * @version     0.0.1
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
class Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Basic rendering method for rendering php aware files, passing parameter array to the view
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param $view
     * @param $params
     * @return Response
     * @throws \Exception
     */
    public function renderView($view, $params = array())
    {
        $kernel = Kernel::getInstance();

        $viewPath = $kernel->applicationRoot.$kernel->mvcRootDirectoryStructure['views'].'/'.$view;

        if(true !== file_exists($viewPath))
        {
            throw new \Exception(sprintf(ERROR::APPLICATION_CONTROLLER_VIEW_NOT_FOUND, $view, $kernel->applicationRoute->getController()));
        }

        //Add parameters
        if(count($params) > 0)
        {
            foreach($params as $name => $value)
            {
                ${$name} = $value;
            }
        }

        /**
         * Render content
         */
        ob_start();

        include ($viewPath);

        $result = ob_get_clean();

        return new Response($result);
    }
}