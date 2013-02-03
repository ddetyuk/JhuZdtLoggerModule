<?php

namespace Jhu\ZdtLoggerModule;

use Jhu\ZdtLoggerModule\ServiceFactory;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 * @since   0.1
 * @author  Jérémy Huet <jeremy.huet@gmail.com>
 * @link    https://github.com/jhuet/JhuZdtLoggerModule
 * @package Jhu\ZdtLoggerModule
 * @license MIT
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    InitProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $manager)
    {
        $events = $manager->getEventManager();
        // Initialize logger collector once the profiler is initialized itself
        $events->attach('profiler_init', function(EventInterface $e) use ($manager) {
            $manager->getEvent()->getParam('ServiceManager')->get('Jhu\ZdtLoggerModule\Collector');
        } );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'Jhu\ZdtLoggerModule\Writer'    => 'Jhu\ZdtLoggerModule\Writer\Stack'
            ),

            'factories' => array(
                'Jhu\ZdtLoggerModule\Logger'    => new ServiceFactory\LoggerFactory(),
                'Jhu\ZdtLoggerModule\Collector' => new ServiceFactory\CollectorFactory(),
            ),

            'aliases' => array(
                'jhu.zdt_logger' => 'Jhu\ZdtLoggerModule\Logger',
            ),
        );
    }
}
