<?php

namespace ZendTest\Stratigility\Dispatch;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stratigility\Dispatch\Router\Aura;
use Zend\Stratigility\Dispatch\Dispatcher;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

class DispatcherTest extends TestCase
{
    public function setUp()
    {

        $this->request  = new ServerRequest();
        $this->response = new Response();
    }

    public function testDispatchInvokableClass()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url' => '/',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Home'
                ]
            ]
        ];

        $dispatch = new Dispatcher(new Aura($config));
        $this->request = $this->request->withUri(new Uri($config['routes']['home']['url']));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }

    /**
     *  @expectedException  Zend\Stratigility\Dispatch\Exception\InvalidArgumentException
     */
    public function testDispatchNotInvokableClass()
    {
        $config = [
            'routes' => [
                'home' => [
                    'url' => '/error',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\Bar'
                ]
            ]
        ];

        $dispatch = new Dispatcher(new Aura($config));
        $this->request = $this->request->withUri(new Uri($config['routes']['home']['url']));
        $result = $dispatch($this->request, $this->response, function(){});
    }

    public function testDispatchCallable()
    {
        $config = [
            'routes' => [
                'page' => [
                    'url' => '/page',
                    'action' => function($request, $response, $next){
                       return true;
                    }
                ]
            ]
        ];

        $dispatch = new Dispatcher(new Aura($config));
        $this->request = $this->request->withUri(new Uri($config['routes']['page']['url']));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }

    public function testDispatchCallableStringClassMethodAction()
    {
        $config = [
            'routes' => [
                'myclass' => [
                    'url' => '/',
                    'action' => 'ZendTest\Stratigility\Dispatch\TestAsset\ClassMethod::myMethod'
                ]
            ]
        ];

        $dispatch = new Dispatcher(new Aura($config));
        $this->request = $this->request->withUri(new Uri($config['routes']['myclass']['url']));
        $result = $dispatch($this->request, $this->response, function(){});

        $this->assertTrue($result);
    }
}
