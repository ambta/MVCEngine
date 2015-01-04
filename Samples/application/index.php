<?php
/**
 * This is how the Engine and the application can be loaded from a php file.
 * The initialization of can be done from any valid apache www file.
 */

$engineRootLocation = (__DIR__).'/../../MVCEngine';
$applicationRootLocation = (__DIR__);

//Require the Engine kernel
require_once($engineRootLocation . '/Kernel.php');

//Require the Application kernel
require_once($applicationRootLocation.'/ApplicationKernel.php');

$applicationKernel = new \MyCustomPrefix\Application\ApplicationKernel($applicationRootLocation);
$applicationKernel->ignite();
$applicationKernel->response->send();