<?php
/*
 * This file is part of the Ambta MVCEngine component.
 *
 * (c) Ambta <info@ambta.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ambta\Component\MVCEngine\Application\Responses;

use Ambta\Component\MVCEngine\Helper\Message\Error as ERROR;

/**
 * Response, used to identify valid return from controller and to enforce proper response to client
 *
 * Supports PHP 5.4+
 *
 * @package     Ambta\Component\MVCEngine\Application\Responses
 * @version     0.0.1
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
class Response
{
    /* @var string */
    private $content;

    /* @var string */
    private $headers;

    /**
     * Constructor, used to create proper response object
     *
     * @author Lars van den Bosch <lars@ambta.com>
     * @param string $content
     * @param array $headers
     */
    public function __construct($content = "", $headers = array())
    {
        $this->content = $content;

        if(true !== empty($headers))
        {
            $this->headers = $headers;
        }
    }

    public function send()
    {
        if(true === headers_sent())
        {
            throw new \Exception(ERROR::ENGINE_RESPONSE_HEADERS_ALREADY_SEND);
        }

        echo $this->content;
    }
}