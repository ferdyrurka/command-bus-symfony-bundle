<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\DependencyInjection\Database;

use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ElasticSearchDatabase
 * @package Ferdyrurka\CommandBus\DependencyInjection\Database
 */
final class ElasticSearchDatabase implements DatabaseInterface
{
    /**
     *
     */
    public const DATABASE_NAME = 'elasticsearch';

    /**
     * @var array
     */
    private $configs;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * ElasticSearchDatabase constructor.
     * @param array $configs
     * @param ContainerBuilder $containerBuilder
     * @throws InvalidArgsConfException
     */
    public function __construct(array $configs, ContainerBuilder $containerBuilder)
    {
        $this->configs = $configs;
        $this->containerBuilder = $containerBuilder;

        $this->validate();
    }

    /**
     *
     */
    public function setParameters(): void
    {
        $prefix = Parameters::PREFIX . '_' . self::DATABASE_NAME . '_';

        $this->containerBuilder->setParameter($prefix . 'host', $this->configs['host']);
        $this->containerBuilder->setParameter($prefix . 'port', $this->configs['port']);
        $this->containerBuilder->setParameter($prefix . 'scheme', $this->configs['scheme']);
        $this->containerBuilder->setParameter($prefix . 'index', $this->configs['index']);

        if (isset($this->configs['user'], $this->configs['pass'])) {
            $this->containerBuilder->setParameter($prefix . 'user', $this->configs['user']);
            $this->containerBuilder->setParameter($prefix . 'pass', $this->configs['pass']);
        }
    }

    /**
     * @throws InvalidArgsConfException
     */
    private function validate(): void
    {
        if (!isset($this->configs['host'], $this->configs['port'], $this->configs['scheme'], $this->configs['index'])) {
            throw new InvalidArgsConfException('Invalid arguments in configuration elasticsearch');
        }
    }
}
