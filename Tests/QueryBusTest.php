<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test;

use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Query\Handler\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use Ferdyrurka\CommandBus\QueryBus;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\DependencyInjection\Container;
use \Exception;

/**
 * Class QueryBusTest
 * @package Ferdyrurka\CommandBus\Test
 */
class QueryBusTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var QueryBus
     */
    private $queryBus;

    /**
     * @var QueryInterface
     */
    private $handler;

    /**
     *
     */
    public function setUp(): void
    {
        $this->handler = Mockery::mock(QueryInterface::class);
        $this->container = Mockery::mock(Container::class);
        $this->queryBus = new QueryBus($this->container);
    }

    /**
     * @throws \Exception
     */
    public function testHandle(): void
    {
        $viewObject = Mockery::mock(ViewObjectInterface::class);
        $this->handler->shouldReceive('handle')->once()->andReturn($viewObject);

        $this->queryBus->handle($this->handler);
    }

    /**
     * @throws \Exception
     */
    public function testSaveLog(): void
    {
        $this->handler->shouldReceive('handle')->once()->andThrow(Exception::class);

        $createLogHandler = Mockery::mock(CreateLogHandler::class);
        $createLogHandler->shouldReceive('handle')->once()->withArgs([CreateLogCommand::class]);

        $this->container->shouldReceive('getParameter')->withArgs([Parameters::PREFIX . '_save_query_bus_log'])
            ->andReturnTrue()->once()
        ;

        $this->container->shouldReceive('get')->once()->withArgs([CreateLogHandler::class])
            ->andReturn($createLogHandler)
        ;

        $this->expectException(Exception::class);
        $this->queryBus->handle($this->handler);
    }

    /**
     * @throws Exception
     */
    public function testNotSaveLog(): void
    {
        $this->handler->shouldReceive('handle')->once()->andThrow(Exception::class);

        $this->container->shouldReceive('getParameter')->withArgs([Parameters::PREFIX . '_save_query_bus_log'])
            ->andReturnFalse()->once()
        ;

        $this->expectException(Exception::class);
        $this->queryBus->handle($this->handler);
    }
}
