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
use Ferdyrurka\CommandBus\Util\NamespaceParser;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $handlerNamespace;

    private $commandBus;

    /**
     *
     */
    public function setUp(): void
    {
        $this->command = Mockery::mock(CommandInterface::class);
        $this->handler = Mockery::mock(HandlerInterface::class);
        $this->container = Mockery::mock(ContainerInterface::class);
        $this->commandBus = new CommandBus($this->container);

        $namespaceParser = new NamespaceParser(\get_class($this->command), 'Command', 'Handler');
        $this->handlerNamespace = $namespaceParser->getHandlerNamespaceByCommandNamespace();
    }

    /**
     * @throws Exception
     * @runInSeparateProcess
     */
    public function testHandleException(): void
    {
        $logHandler = Mockery::mock(CreateLogHandler::class);
        $logHandler->shouldReceive('handle')->once()->withArgs([CreateLogCommand::class]);

        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $this->setContainer(true);
        $this->container->shouldReceive('get')->andReturn($this->handler, $logHandler)->times(2);
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_save_command_bus_log' !== $key) {
                        return false;
                    }

                    return true;
                }
            )
            ->once()->andReturn(true)
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

        $this->expectException(Exception::class);
        $this->commandBus->handle($this->command);
    }

    /**
     * @throws Exception
     */
    public function testNoSaveData(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $this->setContainer(true);
        $this->container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_save_command_bus_log' !== $key) {
                        return false;
                    }

                    return true;
                }
            )
            ->once()->andReturn(false)
        ;

        $this->expectException(Exception::class);
        $this->commandBus->handle($this->command);
    }

    /**
     * @throws Exception
     */
    public function testNotException(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class]);

        $this->container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $this->setContainer(true);

        $this->commandBus->handle($this->command);
    }

    /**
     * @throws Exception
     */
    public function testHandleNotFoundException(): void
    {
        $this->setContainer(false);
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_save_command_bus_log' !== $key) {
                        return false;
                    }

                    return true;
                }
            )
            ->once()->andReturn(false)
        ;

        $this->expectException(HandlerNotFoundException::class);
        $this->commandBus->handle($this->command);
    }


    /**
     * @throws Exception
     */
    public function testHandlerNotImplementsInterface(): void
    {
        $this->setContainer(true);
        $this->container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->command)->once();
        $this->container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool {
                    if (Parameters::PREFIX . '_save_command_bus_log' !== $key) {
                        return false;
                    }

                    return true;
                }
            )
            ->once()->andReturn(false)
        ;

        $this->expectException(HandlerNotFoundException::class);
        $this->commandBus->handle($this->command);
    }

    /**
     * @throws Exception
     */
    public function testHandleOkWithReplaceOnlyNameClass(): void
    {
        $namespaceParser = new NamespaceParser(\get_class($this->command), 'Command', 'Handler');
        $this->handlerNamespace = $namespaceParser->getHandlerNamespaceByNameClass();

        $this->handler->shouldReceive('handle')->once();

        $this->setContainer(true, true);
        $this->container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();

        $this->commandBus->handle($this->command);
    }

    /**
     * @param bool $has
     * @param bool $replaceCommandNamespace
     */
    private function setContainer(bool $has, bool $replaceCommandNamespace = false): void
    {
        $this->container->shouldReceive('has')->once()->withArgs([$this->handlerNamespace])->andReturn($has);
        $this->container->shouldReceive('getParameter')->times(3)->withArgs(
            function (string $key): bool {
                if ($key !== Parameters::PREFIX . '_command_prefix' &&
                    $key !== Parameters::PREFIX . '_handler_prefix' &&
                    $key !== Parameters::PREFIX . '_replace_command_namespace'
                ) {
                    return false;
                }

                return true;
            }
        )->andReturn('Command', 'Handler', $replaceCommandNamespace)
        ;
    }
}
