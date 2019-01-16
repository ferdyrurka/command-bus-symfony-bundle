<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test;

use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\CommandBus;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\HandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;
use \Mockery;
use \Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommandBusTest
 * @package Ferdyrurka\CommandBus\Test
 */
class CommandBusTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var string
     */
    private $handlerNamespace;

    /**
     *
     */
    public function setUp()
    {
        $this->command = Mockery::mock(CommandInterface::class);
        $this->handler = Mockery::mock(HandlerInterface::class);

        $this->handlerNamespace = str_replace('Command', 'Handler', \get_class($this->command));

        parent::setUp();
    }

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    public function testHandle(): void
    {
        $logHandler = Mockery::mock(CreateLogHandler::class);
        $logHandler->shouldReceive('handle')->once()->withArgs([CreateLogCommand::class]);

        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->andReturn($this->handler, $logHandler)->times(2);

        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key &&
                        Parameters::PREFIX . '_save_statistic_handler' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', true)
        ;

        $createLogCommand = Mockery::mock('overload:' . CreateLogCommand::class, CommandInterface::class);
        $createLogCommand->shouldReceive('__construct')->withArgs(
            function (string $message, int $line, string $exception, string $command, string $handler): bool {
                if ($message !== 'Handle EXCEPTION' ||
                    $command !== \get_class($this->command) ||
                    empty($exception) ||
                    $handler !== \get_class($this->handler) ||
                    empty($line)
                ) {
                    return false;
                }

                return true;
            }
        );

        $commandBus = new CommandBus($container);

        $this->expectException(Exception::class);
        $commandBus->handle($this->command);
    }

    public function testNoSaveData(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key &&
                        Parameters::PREFIX . '_save_statistic_handler' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', false)
        ;

        $commandBus = new CommandBus($container);

        $this->expectException(Exception::class);
        $commandBus->handle($this->command);
    }

    public function testNotException(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class]);

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(2)->andReturn('Command', 'Handler')
        ;

        $commandBus = new CommandBus($container);
        $commandBus->handle($this->command);
    }

    public function testHandleNotFoundException(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(false)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key &&
                        Parameters::PREFIX . '_save_statistic_handler' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', false)
        ;

        $commandBus = new CommandBus($container);

        $this->expectException(HandlerNotFoundException::class);
        $commandBus->handle($this->command);
    }


    public function testHandlerNotImplementsInterface(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->command)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key &&
                        Parameters::PREFIX . '_save_statistic_handler' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(3)->andReturn('Command', 'Handler', false)
        ;

        $commandBus = new CommandBus($container);

        $this->expectException(HandlerNotFoundException::class);
        $commandBus->handle($this->command);
    }
}

