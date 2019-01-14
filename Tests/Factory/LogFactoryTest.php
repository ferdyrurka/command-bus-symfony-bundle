<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Factory;

use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\Exception\LogFactoryException;
use Ferdyrurka\CommandBus\Factory\LogFactory;
use Ferdyrurka\CommandBus\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Psr\Container\ContainerInterface;

/**
 * Class LogFactoryTest
 * @package Ferdyrurka\Test\CommandBus\Factory
 */
class LogFactoryTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var LogFactory
     */
    private $logFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     */
    public function setUp(): void
    {
        $this->container = Mockery::mock(ContainerInterface::class);
        $this->logFactory = new LogFactory($this->container);

        parent::setUp();
    }

    /**
     *
     */
    public function testGetRepository(): void
    {
        $repositoryInterface = Mockery::mock(RepositoryInterface::class);

        $this->container->shouldReceive('get')->once()->andReturn($repositoryInterface);

        $this->logFactory->getRepository(ElasticSearchDatabase::DATABASE_NAME);
    }

    /**
     *
     */
    public function testRepositoryNotFound(): void
    {
        $this->expectException(LogFactoryException::class);
        $this->logFactory->getRepository('failed');
    }
}

