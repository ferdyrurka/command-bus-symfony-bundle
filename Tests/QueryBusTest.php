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

use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Command\CreateInfoCommand;
use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\HandlerNotFoundException;
use Ferdyrurka\CommandBus\Exception\QueryHandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateInfoHandler;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Ferdyrurka\CommandBus\Query\Command\QueryCommandInterface;
use Ferdyrurka\CommandBus\Query\QueryHandlerInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use Ferdyrurka\CommandBus\QueryBus;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\DependencyInjection\Container;

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
     * @var QueryCommandInterface
     */
    private $command;

    /**
     * @var string
     */
    private $handlerNamespace;

    /**
     *
     */
    public function setUp(): void
    {
        $this->command = Mockery::mock(QueryCommandInterface::class);
        $this->container = Mockery::mock(Container::class);
        $this->queryBus = new QueryBus($this->container);
        $this->handlerNamespace = str_replace('Command', 'Handler', \get_class($this->command));
    }

    /**
     * @throws \Exception
     */
    public function testHandleAndSaveResult(): void
    {
        $viewObject = Mockery::mock(ViewObjectInterface::class);

        $handler = Mockery::mock(QueryHandlerInterface::class);
        $handler->shouldReceive('handle')->withArgs([QueryCommandInterface::class])->once()->andReturn($viewObject);

        $createInfoHandler = Mockery::mock(CreateInfoHandler::class);
        $createInfoHandler->shouldReceive('handle')->once()->withArgs([CreateInfoCommand::class]);

        $this->setParameterInfo(true);
        $this->container->shouldReceive('get')
            ->withArgs(
                function (string $class): bool {
                    if (CreateInfoHandler::class !== $class &&
                        $this->handlerNamespace !== $class
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(2)->andReturn($handler, $createInfoHandler);
        $this->container->shouldReceive('has')->withArgs([$this->handlerNamespace])->once()->andReturn(true);

        $viewObjectResult = $this->queryBus->handle($this->command);

        $this->assertInstanceOf(ViewObjectInterface::class, $viewObjectResult);
    }

    /**
     * @throws \Exception
     */
    public function testNotFoundHandlerAndSaveLog(): void
    {
        $createLogHandler = Mockery::mock(CreateLogHandler::class);
        $createLogHandler->shouldReceive('handle')->withArgs([CreateLogCommand::class]);

        $this->setParameterLog(true);
        $this->container->shouldReceive('has')->withArgs([$this->handlerNamespace])->once()->andReturn(false);
        $this->container->shouldReceive('get')->once()->withArgs([CreateLogHandler::class])
            ->andReturn($createLogHandler);

        $this->expectException(QueryHandlerNotFoundException::class);
        $this->queryBus->handle($this->command);
    }

    /**
     * @throws \Exception
     */
    public function testNotImplHandlerInterface(): void
    {
        $createLogHandler = Mockery::mock(CreateLogHandler::class);
        $createLogHandler->shouldReceive('handle')->withArgs([CreateLogCommand::class]);

        $this->setParameterLog(true);
        $this->container->shouldReceive('has')->withArgs([$this->handlerNamespace])->once()->andReturn(true);
        $this->container->shouldReceive('get')->times(2)
            ->withArgs(
                function (string $class): bool {
                    if (CreateLogHandler::class !== $class &&
                        $this->handlerNamespace !== $class
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->andReturn(Mockery::mock(ViewObjectInterface::class), $createLogHandler)
        ;

        $this->expectException(QueryHandlerNotFoundException::class);
        $this->queryBus->handle($this->command);
    }

    /**
     * @throws \Exception
     */
    public function testNotSaveResult(): void
    {
        $viewObject = Mockery::mock(ViewObjectInterface::class);

        $handler = Mockery::mock(QueryHandlerInterface::class);
        $handler->shouldReceive('handle')->withArgs([QueryCommandInterface::class])->once()->andReturn($viewObject);

        $this->setParameterInfo(false);
        $this->container->shouldReceive('get')->withArgs([$this->handlerNamespace])->once()->andReturn($handler);
        $this->container->shouldReceive('has')->withArgs([$this->handlerNamespace])->once()->andReturn(true);

        $viewObjectResult = $this->queryBus->handle($this->command);

        $this->assertInstanceOf(ViewObjectInterface::class, $viewObjectResult);
    }

    /**
     * @throws \Exception
     */
    public function testNotSaveLog(): void
    {
        $this->setParameterLog(false);
        $this->container->shouldReceive('has')->withArgs([$this->handlerNamespace])->once()->andReturn(false);

        $this->expectException(QueryHandlerNotFoundException::class);
        $this->queryBus->handle($this->command);
    }

    /**
     * @param bool $saveQueryBusLog
     */
    private function setParameterLog(bool $saveQueryBusLog): void
    {
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $name): bool {
                    if ($name !== Parameters::PREFIX . '_query_command_prefix' &&
                        $name !== Parameters::PREFIX . '_query_handler_prefix' &&
                        $name !== Parameters::PREFIX . '_save_query_bus_log'
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', $saveQueryBusLog);
    }

    /**
     * @param bool $saveInfo
     */
    private function setParameterInfo(bool $saveInfo) : void
    {
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $name): bool {
                    if ($name !== Parameters::PREFIX . '_query_command_prefix' &&
                        $name !== Parameters::PREFIX . '_query_handler_prefix' &&
                        $name !== Parameters::PREFIX . '_save_query_bus_info'
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', $saveInfo);
    }
}
