<?php

declare(strict_types=1);

namespace SymfonyHealthCheckBundle;

//use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
//use Symfony\Component\DependencyInjection\ContainerBuilder;
//use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

//use SymfonyHealthCheckBundle\Controller\HealthController;

class SymfonyHealthCheckBundle extends Bundle
{
/*    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->children()
                    ->integerNode('id')->cannotBeEmpty()->end()
                    ->scalarNode('name')->cannotBeEmpty()->end()
                    ->scalarNode('endpoint')->cannotBeEmpty()->end()
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
        $containerConfigurator->import('../config/controller.xml');
        $containerConfigurator->import('../config/service.xml');

        $healthCheckCollection = $containerBuilder->findDefinition(HealthController::class);

        foreach ($config['health_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = new Reference($healthCheckConfig['id']);
            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }

        foreach ($config['ping_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = $containerBuilder->getDefinition($healthCheckConfig['id'])
                ->replaceArgument(0, $healthCheckConfig['name'])
                ->replaceArgument(1, $healthCheckConfig['endpoint']);

            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }
    }*/
}
