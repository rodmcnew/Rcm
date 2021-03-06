<?php

namespace Rcm\Factory;

use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service Factory for Rcm Cache
 *
 * Factory for Rcm Cache.
 *
 * @category  Reliv
 * @package   Rcm
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2012 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: 1.0
 * @link      https://github.com/reliv
 *
 */
class CacheFactory
{
    /**
     * Creates Service
     *
     * @param ServiceLocatorInterface $serviceLocator Zend Service Locator
     *
     * @return StorageInterface
     */
    public function __invoke($serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return StorageFactory::factory(
            [
                'adapter' => [
                    'name' => $config['rcmCache']['adapter'],
                    'options' => $config['rcmCache']['options'],
                ],
                'plugins' => $config['rcmCache']['plugins'],
            ]
        );
    }
}
