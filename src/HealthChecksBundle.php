<?php

declare(strict_types=1);

namespace HealthChecksBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use HealthChecksBundle\Check\PingCheck;
use HealthChecksBundle\Controller\HealthController;

class HealthChecksBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('health_checks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ping_checks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('service')->cannotBeEmpty()->end()
                            ->scalarNode('endpoint')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void
    {
        $container->import('../config/services.yaml');

        $healthCheckCollection = $builder->findDefinition(HealthController::class);

        foreach ($config['health_checks'] as $healthCheckConfig) {
            $healthCheckDefinition = new Reference($healthCheckConfig['id']);
            $healthCheckCollection->addMethodCall('addHealthCheck', [$healthCheckDefinition]);
        }

        foreach ($config['ping_checks'] as $number => $pingCheckConfig) {
            $id = 'health_checks.ping_check_' . $number;

            $container->services()
                ->set($id, PingCheck::class)
                    ->arg('$service', $pingCheckConfig['service'])
                    ->arg('$endpoint', $pingCheckConfig['endpoint'])
            ;

            $healthCheckCollection->addMethodCall('addHealthCheck', [new Reference($id)]);
        }
    }
}
