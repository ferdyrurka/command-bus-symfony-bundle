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
     * @throws InvalidArgsConfException
     */
    public function __construct(ContainerBuilder $containerBuilder, array $configs)
    {
        $this->containerBuilder = $containerBuilder;
        $this->configs = $configs;

        $this->validConf();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws UndefinedDatabaseTypeException
     */
    public function setParameters(): void
    {
        $this->setRequiredParameters();

        if ((boolean) $this->configs['save_statistic_handler']) {
            $this->setTypeDatabase();
        }
    }

    /**
     * Validate configs array to set parameters
     * @throws InvalidArgsConfException
     */
    private function validConf(): void
    {
        if (isset($this->configs[0][self::PREFIX])) {
            throw new InvalidArgsConfException('Invalid put data configuration!');
        }
    }

    /**
     * Required parameters to worked bundle
     */
    private function setRequiredParameters(): void
    {
        $this->containerBuilder->setParameter(self::PREFIX . '_handler_name', $this->configs['handler_name']);
        $this->containerBuilder->setParameter(self::PREFIX . '_command_name', $this->configs['command_name']);
        $this->containerBuilder->setParameter(self::PREFIX . '_save_statistic_handler', $this->configs['save_statistic_handler']);
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

        $this->containerBuilder->setParameter(self::PREFIX . '_database_type', $this->configs['database_type']);
    }
}
