<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility\Dispatch\Router;

interface RouterInterface
{
    /**
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @param  string $patch
     * @param  array $params
     * @return boolean
     */
    public function match($path, $params);

    /**
     * @return array
     */
    public function getMatchedParams();

    /**
     * @return string
     */
    public function getMatchedRouteName();

    /**
     * @return mixed
     */
    public function getMatchedAction();
}
