<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\Test\CommandBus\FactoryMethod;

use Ferdyrurka\CommandBus\Exception\LogFactoryException;
use Ferdyrurka\CommandBus\FactoryMethod\LogFactory;
use Ferdyrurka\CommandBus\Repository\ElasticSearchRepositoryInterface;
use Ferdyrurka\CommandBus\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Psr\Container\ContainerInterface;

/**
 * Class LogFactoryTest
 * @package Ferdyrurka\Test\CommandBus\FactoryMethod
 */
class LogFactoryTest extends TestCase
{

    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var
     */
    private $logFactory;

    /**
     * @var
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

        $this->logFactory->getRepository(LogFactory::ELASTIC_SEARCH);
    }

    /**
     *
     */
    public function testRepositoryNotFound(): void
    {
        $this->expectException(LogFactoryException::class);
        $this->logFactory->getRepository(2);
    }
}

