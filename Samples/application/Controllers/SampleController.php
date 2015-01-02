<?php

namespace MyCustomPrefix\Application;

use \Ambta\Component\MVCEngine\Application;
use \Ambta\Component\MVCEngine\Application\Responses\Response as Response;
use Ambta\Libraries\Site\Template;

class SampleController extends Application\Controller
{
    public function defaultAction()
    {
        return $this->renderView('Default/index.php');
    }

    public function welcomeAction($name)
    {
        return $this->renderView('Welcome/index.php', array(
            'name' => $name,
        ));
    }

    public function contactAction()
    {
        return $this->renderView('Contact/index.php');
    }

    public function noViewResponseAction()
    {
        return new Response("Response without view");
    }
}