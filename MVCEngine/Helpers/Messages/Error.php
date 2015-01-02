<?php
/*
 * This file is part of the Ambta MVCEngine component.
 *
 * (c) Ambta <info@ambta.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ambta\Component\MVCEngine\Helper\Message;

/**
 * Error, provides constants containing Error messages used by the engine to notify developer and user of occurred exceptions
 *
 * @package     Ambta\Component\MVCEngine
 * @version     1.0.0
 * @author      Lars van den Bosch <lars@ambta.com>
 * @copyright   (c) 2015 Ambta <https://www.ambta.com>
 */
abstract class Error
{
    /**
     * GENERAL
     */
    const GENERAL = 'Whoops, something went wrong.';

    /**
     * ENGINE
     */
    const ENGINE_RESPONSE_HEADERS_ALREADY_SEND = 'Headers have already been send';

    /**
     * APPLICATION
     */
    const APPLICATION_INITIALIZATION_FAILED = 'Failed to initialize application';

    /**
     * APPLICATION CONTROLLER
     */
    const APPLICATION_CONTROLLER_NOT_FOUND = 'Route requested controller: [%s] but the controller was not found.';
    const APPLICATION_CONTROLLER_INVALID_INSTANCE = 'Controller: [%s] must always be instance(extend) of: [Ambta\Component\MVCEngine\Application\Controller]';
    const APPLICATION_CONTROLLER_INVALID_ACTION = 'Controller: [%s] does not have an action matching: [%s]';
    const APPLICATION_CONTROLLER_INVALID_RESPONSE = 'Controller: [%s] does not return a valid response. Controller should always return an instance of: [Ambta\Component\MVCEngine\Application\Responses\Response]';
    const APPLICATION_CONTROLLER_VIEW_NOT_FOUND = 'View: [%s] not found for controller: [%s]';

    /**
     * APPLICATION CONFIGURATION
     */
    const APPLICATION_CONFIGURATION_MISSING = 'Engine failed to locate a valid routing object to render on';
    const APPLICATION_CONFIGURATION_INVALID_JSON_OBJECT = 'Config object construction prevented due invalid or empty JSON object as constructor argument';
    const APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_MISSING = 'Mandatory property: [%s] missing from configuration';
    const APPLICATION_CONFIGURATION_MANDATORY_PROPERTY_EMPTY = 'Mandatory property: [%s] empty in configuration';

    /**
     * APPLICATION ROUTING
     */
    const APPLICATION_ROUTING_MISSING = 'Engine failed to locate a valid config object to render on';
    const APPLICATION_ROUTING_PARAMETER_MISS_MATCH = 'Route parameter miss-match: controller expected %d [%s] arguments but route contained: %d [%s] arguments';
    const APPLICATION_ROUTING_PARAMETER_KEY_MISS_MATCH = 'Route parameters key miss-match: controller gave: [%s] but route contains: %s';
}
?>