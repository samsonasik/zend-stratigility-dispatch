<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Stratigility\Dispatch\Router;

use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;

class Aura implements RouterInterface
{
    /**
     * Aura router
     *
     * @var Aura\Router\Router
     */
    protected $router;

    /**
     * Matched Aura route
     *
     * @var Aura\Router\Route
     */
    protected $route;

    public function __construct(array $config)
    {
        $this->router = new Router(
            new RouteCollection(new RouteFactory()),
            new Generator()
        );

        foreach ($config['routes'] as $name => $data) {
            $this->router->add($name, $data['url']);
            if (!isset($data['values'])) {
                $data['values'] = [];
            }
            $data['values']['action'] = $data['action'];
            if (!isset($data['tokens'])) {
              $this->router->add($name, $data['url'])
                           ->addValues($data['values']);
            } else {
              $this->router->add($name, $data['url'])
                           ->addTokens($data['tokens'])
                           ->addValues($data['values']);
            }
        }
    }

    /**
     * @param  string $patch
     * @param  array $params
     * @return boolean
     */
    public function match($path, $params)
    {
        $this->route = $this->router->match($path, $params);
        return (!empty($this->route));
    }

    /**
     * @return array
     */
    public function getMatchedParams()
    {
        return $this->route->params;
    }

    /**
     * @return string
     */
    public function getMatchedRouteName()
    {
        return $this->route->name;
    }

    /**
     * @return mixed
     */
    public function getMatchedAction()
    {
        return $this->route->params['action'];
    }
}
