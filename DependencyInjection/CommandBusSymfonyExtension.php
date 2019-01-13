<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class CommandBusSymfonyExtension
 * @package Ferdyrurka\CommandBus\DependencyInjection
 */
class CommandBusSymfonyExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Ferdyrurka\CommandBus\Exception\InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $config = $config[0][Parameters::PREFIX];

        $parameters = new Parameters($container, $config);
        $parameters->setParameters();
    }
}
