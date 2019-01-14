<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Factory;

use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\Exception\LogFactoryException;
use Ferdyrurka\CommandBus\Repository\RepositoryInterface;
use Ferdyrurka\CommandBus\Repository\ElasticSearchRepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Class LogFactory
 * @package Ferdyrurka\CommandBus\Factory
 */
class LogFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * LogFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $type
     * @return RepositoryInterface
     * @throws LogFactoryException
     */
    public function getRepository(string $type): RepositoryInterface
    {
        switch ($type) {
            case ElasticSearchDatabase::DATABASE_NAME:
                return $this->container->get(ElasticSearchRepositoryInterface::class);
            default:
                throw new LogFactoryException('Factory not found repository by key: ' . $type);
        }
    }
}
