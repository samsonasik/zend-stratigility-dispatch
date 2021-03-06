<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility\Dispatch;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\NotFoundException;
use Interop\Container\Exception\ContainerException;
use Zend\Stratigility\Dispatch\Router\RouterInterface;

class Dispatcher
{
    protected $router;
    protected $container;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(RouterInterface $router, ContainerInterface $container = null)
    {
        $this->setRouter($router);
        if (null !== $container) {
            $this->setContainer($container);
        }
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $path  = $request->getUri()->getPath();
        if (!$this->router->match($path, $request->getServerParams())) {
            return $next($request, $response);
        }
        foreach ($this->router->getMatchedParams() as $param => $value) {
            $request = $request->withAttribute($param, $value);
        }
        $action = $this->router->getMatchedAction();
        if (!$action) {
            throw new Exception\InvalidArgumentException(
                sprintf("The route %s doesn't have an action to dispatch", $this->router->getMatchedName())
            );
        }
        if (is_callable($action)) {
            return call_user_func_array($action, array(
                $request,
                $response,
                $next,
            ));
        } elseif (is_string($action)) {
            // try to get the action name from the container
            if ($this->container && $this->container->has($action)) {
                try {
                    $call = $this->container->get($action);
                    return $call($request, $response, $next);
                } catch (ContainerException $e) {
                    throw new Exception\InvalidArgumentException(
                        sprintf("The action class %s, from the container, has thrown the exception: %s", $action, $e->getMessage())
                    );
                }
            }
            if (class_exists($action)) {
                $call = new $action;
                if (is_callable($call)) {
                    return $call($request, $response, $next);
                }
            }
        }
        throw new Exception\InvalidArgumentException(
            sprintf("The action class specified %s is not invokable", $action)
        );
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
