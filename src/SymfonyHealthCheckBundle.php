<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use SymfonyHealthCheckBundle\Check\PingCheck;
use SymfonyHealthCheckBundle\Controller\HealthController;

class SymfonyHealthCheckBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('health_checks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')->cannotBeEmpty()->end()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ping_checks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                            ->scalarNode('endpoint')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void
    {
        $containerConfigurator->import('../config/services.yaml');

        $healthCheckCollection = $containerBuilder->findDefinition(HealthController::class);

        foreach ($config['health_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = new Reference($healthCheckConfig['id']);
            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }

        foreach ($config['ping_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = $containerConfigurator->services()
                ->set(null, PingCheck::class)
                ->args($healthCheckConfig)
            ;

            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }
    }
}
