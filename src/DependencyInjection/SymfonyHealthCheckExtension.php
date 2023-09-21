<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use SymfonyHealthCheckBundle\Controller\HealthController;

class SymfonyHealthCheckExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controller.xml');

        $this->loadServices($config, $loader, $container);
    }

    /**
     * @throws Exception
     */
    private function loadServices(
        array $config,
        XmlFileLoader $loader,
        ContainerBuilder $container
    ): void {
        $loader->load('services.xml');

        $healthCheckCollection = $container->findDefinition(HealthController::class);

        foreach ($config['health_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = new Reference($healthCheckConfig['id']);
            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }

        foreach ($config['ping_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = $container->getDefinition($healthCheckConfig['id'])
                ->replaceArgument(0, $healthCheckConfig['name'])
                ->replaceArgument(1, $healthCheckConfig['endpoint']);

            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }
    }
}
