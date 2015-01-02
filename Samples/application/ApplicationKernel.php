<?php

namespace MyCustomPrefix\Application;

use \Ambta\Component\MVCEngine;

class ApplicationKernel extends MVCEngine\Kernel
{
    public function __construct($applicationRoot)
    {
        parent::__construct($applicationRoot);
    }


    /**
     * Define custom methods in this class
     */
}