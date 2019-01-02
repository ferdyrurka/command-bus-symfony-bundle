<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Ferdyrurka\CommandBus\FactoryMethod;

use Ferdyrurka\CommandBus\Exception\LogFactoryException;
use Ferdyrurka\CommandBus\Repository\RepositoryInterface;
use Ferdyrurka\CommandBus\Repository\ElasticSearchRepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Class LogFactory
 * @package Ferdyrurka\CommandBus\FactoryMethod
 */
class LogFactory
{
    /**
     *
     */
    public const ELASTIC_SEARCH = 0;

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
     * @param int $type
     * @return RepositoryInterface
     * @throws LogFactoryException
     */
    public function getRepository(int $type): RepositoryInterface
    {
        if ($type === self::ELASTIC_SEARCH) {
            return $this->container->get(ElasticSearchRepositoryInterface::class);
        }

        throw new LogFactoryException('Factory not found repository by key: ' . $type);
    }
}
