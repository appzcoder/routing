<?php

namespace Appzcoder\Routing;

use Appzcoder\Routing\Exception\MethodNotAllowedException;
use Appzcoder\Routing\Exception\NotFoundException;

/**
 * The Route Dispatcher class.
 *
 * @author  Sohel Amin <sohelamincse@gmail.com>
 */
class Dispatcher
{

    /**
     * Current URI or Route.
     *
     * @var string
     */
    protected $routeName;

    /**
     * Current URL's HTTP Method.
     *
     * @var string
     */
    protected $httpMethod;

    /**
     * All params of current URI.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor function
     */
    public function __construct()
    {

    }

    /**
     * Parse URI from the address bar.
     *
     * @return void
     */
    public function parseURI()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];

        $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        if (isset($_SERVER['SCRIPT_NAME'])) {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        $this->routeName = $uri;
    }

    /**
     * Dispatch the URI to the matched route.
     *
     * @return void
     */
    public function dispatch()
    {
        $routesList = \Route::getRoutesList();

        foreach ($routesList as $key => $value) {
            $routeWithPattern = str_replace("{id}", "[0-9]+", $key);
            $routeWithPattern = str_replace("{name}", "[a-zA-Z_ ]+", $routeWithPattern);
            $routeWithPattern = str_replace('/', '\/', ltrim($routeWithPattern, '/'));

            $regex = '/\/' . $routeWithPattern . '(\/|)$/';
            if (preg_match($regex, $this->routeName, $matches)) {
                if ($matches[0] == $this->routeName) {
                    $matchedRouteName = $key;
                }
            }
        }

        if (isset($matchedRouteName) && in_array($this->httpMethod, $routesList[$matchedRouteName]['verbs']) && array_key_exists($matchedRouteName, $routesList)) {
            $this->setParams($matchedRouteName);
            \Route::call($matchedRouteName);
        } elseif (isset($matchedRouteName) && !in_array($this->httpMethod, $routesList[$matchedRouteName]['verbs']) && array_key_exists($matchedRouteName, $routesList)) {
            throw new MethodNotAllowedException('405 Method Not Allowed');
        } else {
            throw new NotFoundException('404 Not Found');
        }

    }

    /**
     * Store the parsed params from current URI.
     *
     * @param  string $route
     * @return void
     */
    protected function setParams($route)
    {
        $paramsArray = explode('/', $route);
        $routeNamesArray = explode('/', $this->routeName);
        $flippedParamsArray = array_flip($paramsArray);

        $this->params['id'] = isset($flippedParamsArray['{id}']) ? $routeNamesArray[$flippedParamsArray['{id}']] : '';
        $this->params['name'] = isset($flippedParamsArray['{name}']) ? $routeNamesArray[$flippedParamsArray['{name}']] : '';
    }

    /**
     * Get all params of current URI
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}
