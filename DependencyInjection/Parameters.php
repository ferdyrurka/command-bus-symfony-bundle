<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\DependencyInjection;

use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException;
use Ferdyrurka\CommandBus\Factory\DatabaseFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Parameters
 * @package Ferdyrurka\CommandBus\DependencyInjection
 */
class Parameters
{
    /**
     *
     */
    public const PREFIX = 'command_bus_symfony';

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var array
     */
    private $configs;

    /**
     * Parameters constructor.
     * @param ContainerBuilder $containerBuilder
     * @param array $configs
     */
    public function __construct(ContainerBuilder $containerBuilder, array $configs)
    {
        $this->containerBuilder = $containerBuilder;
        $this->configs = $configs;
    }

    /**
     * @throws InvalidArgsConfException
     * @throws UndefinedDatabaseTypeException
     */
    public function setParameters(): void
    {
        $this->setRequiredParameters();

        if ((boolean) $this->configs['save_command_bus_log'] ||
            (boolean) $this->configs['save_query_bus_log']
        ) {
            $this->setTypeDatabase();
        }
    }

    /**
     * Required parameters to worked bundle
     */
    private function setRequiredParameters(): void
    {
        # CommandBus

        $this->containerBuilder->setParameter(self::PREFIX . '_handler_prefix', $this->configs['handler_prefix']);
        $this->containerBuilder->setParameter(self::PREFIX . '_command_prefix', $this->configs['command_prefix']);

        $this->containerBuilder->setParameter(
            self::PREFIX . '_save_command_bus_log',
            $this->configs['save_command_bus_log']
        );

        $this->containerBuilder->setParameter(
            self::PREFIX . '_replace_command_namespace',
            $this->configs['replace_command_namespace']
        );

        # QueryBus

        $this->containerBuilder->setParameter(
            self::PREFIX . '_query_handler_prefix',
            $this->configs['query_handler_prefix']
        );
        $this->containerBuilder->setParameter(self::PREFIX . '_query_prefix', $this->configs['query_prefix']);

        $this->containerBuilder->setParameter(
            self::PREFIX . '_save_query_bus_log',
            $this->configs['save_query_bus_log']
        );

        $this->containerBuilder->setParameter(
            self::PREFIX . '_replace_query_namespace',
            $this->configs['replace_query_namespace']
        );
    }

    /**
     * @throws InvalidArgsConfException
     * @throws UndefinedDatabaseTypeException
     */
    private function setTypeDatabase(): void
    {
        if (!isset($this->configs['database_type'])) {
            throw new InvalidArgsConfException('Database type is required configuration!');
        }

        $databaseFactory = new DatabaseFactory($this->configs['connection'], $this->containerBuilder);
        $databaseType = $databaseFactory->getDatabase($this->configs['database_type']);
        $databaseType->setParameters();
    }
}
