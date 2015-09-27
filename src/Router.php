<?php

namespace Appzcoder\Routing;

/**
 * The Router class for handle all routes.
 *
 * @author  Sohel Amin <sohelamincse@gmail.com>
 */
class Router
{

    /**
     * Controller Namespace name.
     *
     * @var string
     */
    protected $controllerNamespace = "App\\Controllers\\";

    /**
     * Group Route Prefix.
     *
     * @var string
     */
    protected $groupPrefix;

    /**
     * Group Route Namespace.
     *
     * @var string
     */
    protected $groupNamespace;

    /**
     * Assign all registered routes.
     *
     * @var array
     */
    protected $routesList = [];

    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    protected $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * Instance of dispatcher class.
     *
     * @var object
     */
    protected $dispatcher;

    /**
     * Crate a new Dispatcher instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dispatcher = new Dispatcher();
        $this->dispatcher->parseURI();
    }

    /**
     * Set controller namespace.
     *
     * @param  string $namespace
     * @return void
     */
    public function setControllerNamespace($namespace)
    {
        $this->controllerNamespace = $namespace;
    }

    /**
     * Register a new GET route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function get($route, $callback)
    {
        $verbs = ['GET', 'HEAD'];
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new POST route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function post($route, $callback)
    {
        $verbs = 'POST';
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new PUT route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function put($route, $callback)
    {
        $verbs = 'PUT';
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new PATCH route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function patch($route, $callback)
    {
        $verbs = 'PATCH';
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new DELETE route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function delete($route, $callback)
    {
        $verbs = 'DELETE';
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new OPTIONS route.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function options($route, $callback)
    {
        $verbs = 'OPTIONS';
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register a new route for all http verbs.
     *
     * @param  string $route
     * @param  string $callback
     * @return boolean
     */
    public function any($route, $callback)
    {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];
        return $this->addRoute($route, $callback, $verbs);
    }

    /**
     * Register all methods route of a controller.
     *
     * @param  string $routeName
     * @param  string $controllerName
     * @return void
     */
    public function controller($routeName, $controllerName)
    {
        $classMethods = get_class_methods($this->controllerNamespace . $controllerName);

        foreach ($classMethods as $methodName) {
            if (!preg_match("/__/", $methodName)) {
                if (preg_match("/post/", $methodName)) {
                    $verbs = 'POST';
                    $route = $routeName . '/' . strtolower(str_replace('post', '', $methodName));
                } else {
                    $verbs = 'GET';
                    $route = $routeName . '/' . strtolower(str_replace('get', '', $methodName));
                }

                $callback = $controllerName . '#' . $methodName;
                $this->addRoute($route, $callback, $verbs);
            }
        }
    }

    /**
     * Register a new route for restful resource controller.
     *
     * @param  string $route
     * @param  string $controller
     * @return boolean
     */
    public function resource($route, $controller)
    {
        $this->addRoute($route, $controller . '#index', 'GET');
        $this->addRoute($route . '/create', $controller . '#create', 'GET');
        $this->addRoute($route, $controller . '#store', 'POST');
        $this->addRoute($route . '/{id}', $controller . '#show', 'GET');
        $this->addRoute($route . '/{id}/edit', $controller . '#edit', 'GET');
        $this->addRoute($route . '/{id}', $controller . '#update', ['PUT', 'PATCH']);
        $this->addRoute($route . '/{id}', $controller . '#delete', 'DELETE');

        return true;
    }

    /**
     * Register a group of routes.
     *
     * @param  string $attributes
     * @param  string $callback
     * @return boolean
     */
    public function group($attributes, $callback)
    {
        $this->groupPrefix = isset($attributes['prefix']) ? $attributes['prefix'] : null;
        $this->groupNamespace = isset($attributes['namespace']) ? $attributes['namespace'] : null;

        $callback();

        $this->groupPrefix = null;
        $this->groupNamespace = null;
    }

    /**
     * Add a route to the route collection array.
     *
     * @param  string $route
     * @param  string $callback
     * @param  string|array $verbs
     * @return void
     */
    protected function addRoute($route, $callback, $verbs = 'GET')
    {
        if (is_string($verbs)) {
            $verbs = (array) $verbs;
        }
        if (isset($this->groupPrefix)) {
            $route = '/' . $this->groupPrefix . $route;
        }
        $this->routesList[$route] = array(
            'route' => $route,
            'callback' => $callback,
            'verbs' => $verbs,
            'namespace' => $this->groupNamespace,
        );
    }

    /**
     * Get all registered routes.
     *
     * @return array
     */
    public function getRoutesList()
    {
        return $this->routesList;
    }

    /**
     * Call controller's method by given route name.
     *
     * @param  string $routeName
     * @return void
     */
    public function call($routeName)
    {
        $route = $this->routesList[$routeName];

        if ($route['callback'] instanceof \Closure) {
            echo $route['callback']();
        } else {
            $strArray = explode('#', $route['callback']);
            $controllerName = $strArray[0];
            $methodName = $strArray[1];

            if (isset($route['namespace'])) {
                $className = $this->controllerNamespace . $route['namespace'] . '\\' . $controllerName;
            } else {
                $className = $this->controllerNamespace . $controllerName;
            }

            $controller = new $className();

            $data = $this->dispatcher->getParams();

            echo $controller->$methodName($data);
        }

    }

    /**
     * Execute route for dispatch of current uri.
     *
     * @return boolean
     */
    public function execute()
    {
        return $this->dispatcher->dispatch();
    }
}
