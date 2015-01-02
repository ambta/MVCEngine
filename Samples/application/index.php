<?php
/**
 * Your custom initializations above
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