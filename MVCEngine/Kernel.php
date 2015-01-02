<?php
/*
 * This file is part of the Ambta MVCEngine component.
 *
 * (c) Ambta <info@ambta.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ambta\Component\MVCEngine;

use Ambta\Component\MVCEngine\Application\Config;
use Ambta\Component\MVCEngine\Application\Controller;
use Ambta\Component\MVCEngine\Application\Routing;
use Ambta\Component\MVCEngine\Application\Responses;
use Ambta\Component\MVCEngine\Helper\Message\Error as ERROR;

/**
 * Kernel
 *
 * This is the Ambta MVC engine kernel.
 * It provides a base for php applications.
 *
 * Supports PHP 5.4+
 *
 * @package     Ambta\Component\MVCEngine
 * @version     0.0.1
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
abstract class Kernel
{
    /* @var string  */
    public $engineRoot;

    /* @var string */
    public $applicationRoot;

    /* @var array */
    public $applicationControllers;

    /* @var array */
    public $applicationModels;

    /* @var array */
    public $applicationViews;

    /* @var array */
    public $applicationResources;

    /* @var \Ambta\Component\MVCEngine\Application\Config */
    public $applicationConfig;

    /* @var \Ambta\Component\MVCEngine\Application\Routing */
    public $applicationRoute;

    /* @var \Ambta\Component\MVCEngine\Application\Responses\Response */
    public $response;

    /* @var array */
    public $mvcRootDirectoryStructure = array(
        'controllers' => '/Controllers',
        'models' => '/Models',
        'views' => '/Views',
        'resources' => '/Resources',
        'config' => '/Config',
        'routing' => '/Routing',
    );

    /* @var \Ambta\Component\MVCEngine\Kernel */
    private static $instance;

    /* @var string */
    const VERSION = "0.1";

    /* @var string */
    const VERSION_CODE = "0001";

    /**
     * Get the existing instance of the kernel
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @return \Ambta\Component\MVCEngine\Kernel
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Kernel constructor
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param string $applicationRoot
     * @throws \Exception
     */
    public function __construct($applicationRoot)
    {
        $this->applicationRoot = $applicationRoot;
        $this->engineRoot = (__DIR__);

        if(true !== $this->isValidApplicationRoot())
        {
            throw new \Exception('Provided MVC application root directory does not contain valid directory structure: '.implode(', ', $this->mvcRootDirectoryStructure));
        }

        $this->init();

        self::$instance = $this;
    }

    /**
     * Initializes the kernel, loading the engine and the application.
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @throws \Exception
     */
    private function init()
    {
        try
        {
            /**
             * Load engine
             *
             * After the engine is loaded, we can start using the engine libraries.
             */
            if(true !== $this->loadEngine())
            {
                throw new \Exception('Failed to initialize engine');
            }

            /**
             * Load application
             */
            if(true !== $this->loadApplication())
            {
                throw new \Exception(ERROR::APPLICATION_INITIALIZATION_FAILED);
            }

            /**
             * Create plain response
             */
            $this->response = new Responses\Response("");
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Ignites the engine, rendering the application in MVC structure
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @throws \Exception
     */
    public function ignite()
    {
        try
        {
            if(true !== ($this->applicationRoute instanceof Routing))
            {
                throw new \Exception(ERROR::APPLICATION_ROUTING_MISSING);
            }

            if(true !== ($this->applicationConfig instanceof Config))
            {
                throw new \Exception(ERROR::APPLICATION_CONFIGURATION_MISSING);
            }

            $applicationNamespace = $this->applicationConfig->getApplicationNamespace();
            $controllerName = $applicationNamespace.$this->applicationRoute->getController();

            if(true !== class_exists($controllerName))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONTROLLER_NOT_FOUND, $controllerName));
            }

            $routeController = new $controllerName();
            $routeAction = $this->applicationRoute->getAction();
            $rawRouteParams = $this->applicationRoute->getParams();

            if(true !== ($routeController instanceof Controller))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONTROLLER_INVALID_INSTANCE, $controllerName));
            }

            if(true !== method_exists($routeController, $routeAction))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONTROLLER_INVALID_ACTION, $controllerName, $routeAction));
            }

            //Get the controller arguments
            $controllerReflectionMethod =  new \ReflectionMethod($routeController, $routeAction);
            $controllerArguments = $controllerReflectionMethod->getParameters();
            $controllerArgumentNames = array_map(function( $item ){
                return $item->getName();
            }, $controllerArguments);

            //Validate controller parameter match
            if(count($rawRouteParams) != count($controllerArgumentNames))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_ROUTING_PARAMETER_MISS_MATCH, count($controllerArgumentNames), implode(", ", $controllerArgumentNames), count($rawRouteParams), http_build_query($rawRouteParams)));
            }

            //Bind route param to the correct controller argument position
            $routeParams = array();
            foreach($controllerArgumentNames as $i => $controllerArgumentName)
            {
                if(true !== array_key_exists($controllerArgumentName, $rawRouteParams))
                {
                    throw new \Exception(sprintf(ERROR::APPLICATION_ROUTING_PARAMETER_KEY_MISS_MATCH, $controllerArgumentName, http_build_query($rawRouteParams)));
                }

                $routeParams[$i] = $rawRouteParams[$controllerArgumentName];
            }

            $response = call_user_func_array(array($routeController, $routeAction), $routeParams);

            if(true !== ($response instanceof Responses\Response))
            {
                throw new \Exception(sprintf(ERROR::APPLICATION_CONTROLLER_INVALID_RESPONSE, $controllerName));
            }

            $this->response = $response;
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Loads the engine files
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @return bool
     */
    private function loadEngine()
    {
        try
        {
            //Load MVC engine
            $engineFiles = $this->locateFilesFromDir($this->engineRoot);

            foreach($engineFiles as $engineFile)
            {
                require_once ($engineFile['path'].$engineFile['name']);
            }

            return true;
        }catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Loads the application, initializing the configuration and routing, preparing the kernel for ignition
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @throws \Exception
     * @return bool
     */
    private function loadApplication()
    {
        try
        {
            /**
            * Locate application required files
            */

            //Configuration
            if(true !== $this->initializeConfig())
            {
                return false;
            }

            //Routing
            if(true !== $this->initializeRouting())
            {
                return false;
            }

            //Controller
            $this->applicationControllers = $this->locateFilesFromDir($this->applicationRoot.$this->mvcRootDirectoryStructure['controllers']);
            foreach($this->applicationControllers as $conroller)
            {
                $controllerFile = $conroller['path'].$conroller['name'];
                if(true !== file_exists($controllerFile))
                {
                    return false;
                }
                require_once ($controllerFile);
            }

            //Models
            $this->applicationModels = $this->locateFilesFromDir($this->applicationRoot.$this->mvcRootDirectoryStructure['models']);
            foreach($this->applicationModels as $modal)
            {
                $modelFile = $modal['path'].$modal['name'];
                if(true !== file_exists($modelFile))
                {
                    return false;
                }
                require_once ($modelFile);
            }

            //Views
            $this->applicationViews = $this->locateFilesFromDir($this->applicationRoot.$this->mvcRootDirectoryStructure['views']);

            //Resources
            $this->applicationResources = $this->locateFilesFromDir($this->applicationRoot.$this->mvcRootDirectoryStructure['resources']);

            return true;
        }catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Initializes the application configuration file, sets the config object.
     * Configuration only accepts JSON at the moment
     *
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param string $configFileName
     * @return bool
     * @throws \Exception
     */
    private function initializeConfig($configFileName = "/Config.json")
    {
        try
        {
            $configFileLoc = $this->applicationRoot.$this->mvcRootDirectoryStructure['config'].$configFileName;
            if(true !== file_exists($configFileLoc))
            {
                return false;
            }

            $configJson = file_get_contents($configFileLoc);
            $configJObj = json_decode($configJson);

            $this->applicationConfig = new Config($configJObj);

            return true;

        }catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Initializes application routing by loading the json file, locates the requested route and sets kernel routing parameters
     * Routing only accepts JSON at the moment
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param string $routeFileName
     * @return bool
     * @throws \Exception
     */
    private function initializeRouting($routeFileName = "/Routing.json")
    {
        try
        {
            $routingFileLoc = $this->applicationRoot.$this->mvcRootDirectoryStructure['routing'].$routeFileName;
            if(true !== file_exists($routingFileLoc))
            {
                return false;
            }

            $routingJson = file_get_contents($routingFileLoc);
            $routing = json_decode($routingJson, true);
            $routeMatch = false;

            foreach($routing as $routeName => $routeInfo)
            {
                $baseUrl = $this->applicationConfig->getApplicationBaseUrl();
                $baseUrlParse = parse_url($baseUrl);

                $pattern = str_replace('//', '/', ((true !== isset($baseUrlParse['path']) ? '' : $baseUrlParse['path']).(true !== isset($routeInfo['pattern']) ? '' : $routeInfo['pattern'])));
                $pattern = substr($pattern, -1) == "/" ? substr($pattern, 0, (strlen($pattern) - 1)) : $pattern;

                $controller = (true !== isset($routeInfo['controller']) ? '' : $routeInfo['controller']);
                $action = (true !== isset($routeInfo['action']) ? '' : $routeInfo['action']);

                //Strip pattern
                $realPattern = array();

                $paramKeys = array();
                $paramValues = array();

                $patternExplode = explode('/', $pattern);
                foreach($patternExplode as $i => $patternItem)
                {
                    $patternItem = trim($patternItem);
                    if(substr($patternItem, 0, 1) == '{')
                    {
                        $paramKeys[$i] = substr($patternItem, 1, (strlen($patternItem) - 2));
                        continue;
                    }

                    $realPattern[] = $patternItem;
                }

                //Get the requested pattern
                $requestPattern = substr($_SERVER['REQUEST_URI'], -1) == "/" ? substr($_SERVER['REQUEST_URI'], 0, (strlen($_SERVER['REQUEST_URI']) - 1)) : $_SERVER['REQUEST_URI'];

                $realRequestPattern = array();

                $requestPatternExplode = explode('/', $requestPattern);
                foreach($requestPatternExplode as $i => $requestedPatternItem)
                {
                    if(array_key_exists($i, $paramKeys))
                    {
                        $paramValues[$i] = $requestedPatternItem;
                        continue;
                    }

                    $realRequestPattern[] = $requestedPatternItem;
                }

                //Apply glue
                $finalPattern = implode('/', $realPattern);
                $finalRequestedPattern = implode('/', $realRequestPattern);

                if($finalPattern == $finalRequestedPattern)
                {
                    //Merge params
                    $finalParams = array();

                    foreach($paramKeys as $key => $paramKey)
                    {
                        $finalParams[$paramKey] = (isset($paramValues[$key]) ? $paramValues[$key] : '');
                    }

                    $this->applicationRoute = new Routing($routeName, $pattern, $controller, $action, $finalParams);
                    $routeMatch = true;
                    break;
                }
            }

            if(true !== $routeMatch)
            {
                //TODO: Proper error page redirect
                throw new \Exception("No route match");
            }

            return true;

        }catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Locates files and file information recursively from an directory
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param $directory
     * @param bool $includeEmptyDirs
     * @return array
     */
    private function locateFilesFromDir($directory, $includeEmptyDirs = false)
    {
        $data = array();

        $it = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $itMode = (true === $includeEmptyDirs) ? \RecursiveIteratorIterator::SELF_FIRST : \RecursiveIteratorIterator::LEAVES_ONLY;
        foreach (new \RecursiveIteratorIterator($it, $itMode) as $filename => $file)
        {
            //Parse filename to proper file name with path
            $fileName = str_replace("\\", "/", str_replace($directory, "", $filename));
            $path = str_replace("\\", "/",$directory);
            $fileDate = new \DateTime();
            $fileDate->setTimestamp($file->getMTime());

            $data[] = array(
                "name" => $fileName,
                "path" => $path,
                "size" => $file->getSize(),
                "lastModifiedDate" => $fileDate,
            );
        }

        return $data;
    }

    /**
     * Checks if the application root contains the required structure for the engine to render properly
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @return bool
     */
    private function isValidApplicationRoot()
    {
        $rootDir = $this->applicationRoot;
        $applicationStructure = $this->mvcRootDirectoryStructure;

        // Is root directory existing?
        if(true !== is_dir($rootDir))
        {
            return false;
        }

        // Confirm application root as MVC structure
        if(true !== is_array($applicationStructure))
        {
            return false;
        }

        foreach($applicationStructure as $dir)
        {
            if(true !== is_dir($rootDir.$dir))
            {
                return false;
            }
        }

        return true;
    }
}