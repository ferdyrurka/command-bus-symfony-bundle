<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\FactoryMethod;

use Ferdyrurka\CommandBus\DependencyInjection\Database\DatabaseInterface;
use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DatabaseFactory
 * @package Ferdyrurka\CommandBus\FactoryMethod
 */
class DatabaseFactory
{
    /**
     * @var array
     */
    private $configs;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * DatabaseFactory constructor.
     * @param array $configs
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(array $configs, ContainerBuilder $containerBuilder)
    {
        $this->configs = $configs;
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param string $key
     * @return DatabaseInterface
     * @throws UndefinedDatabaseTypeException
     * @throws \Ferdyrurka\CommandBus\Exception\InvalidArgsConfException
     */
    public function getDatabase(string $key): DatabaseInterface
    {
        switch ($key) {
            case ElasticSearchDatabase::DATABASE_NAME:
                $databaseType = new ElasticSearchDatabase($this->configs[$key], $this->containerBuilder);
                break;
            default:
                throw new UndefinedDatabaseTypeException('Undefined database type!');
        }

        if (!$databaseType instanceof DatabaseInterface) {
            throw new UndefinedDatabaseTypeException('Database type not implements DatabaseInterface');
        }

        return $databaseType;
    }
}
