<?php

declare(strict_types=1);

namespace SolicitudesModule\Adapters\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Bundle de Symfony para integrar el módulo Solicitudes.
 */
class SolicitudesBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $container->import(__DIR__ . '/config/services.yaml');
    }

    public function getPath(): string
    {
        return __DIR__;
    }
}
