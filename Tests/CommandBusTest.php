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
use Ferdyrurka\CommandBus\CommandBus;
use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Exception\HandlerNotFoundException;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Ferdyrurka\CommandBus\Factory\LogFactory;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Ferdyrurka\CommandBus\Repository\ElasticSearchRepository;
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
     * @throws InvalidArgsConfException
     * @runInSeparateProcess
     */
    public function testHandle(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();

        $container->shouldReceive('hasParameter')->withArgs([Parameters::PREFIX . '_database_type'])->once()
            ->andReturnTrue();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
                        Parameters::PREFIX . '_handler_name' !== $key &&
                        Parameters::PREFIX . '_save_statistic_handler' !== $key &&
                        Parameters::PREFIX . '_database_type' !== $key
                    ) {
                        return false;
                    }

                    return true;
                }
            )
            ->times(4)->andReturn('Command', 'Handler', true, ElasticSearchDatabase::DATABASE_NAME)
        ;

        $elasticSearchRepository = Mockery::mock(ElasticSearchRepository::class);
        $elasticSearchRepository->shouldReceive('create')->withArgs(
            function (Log $warn): bool
            {
                if (
                    $warn->getMessage() !== 'Handle EXCEPTION' ||
                    $warn->getCommand() !== \get_class($this->command) ||
                    $warn->getHandler() !== \get_class($this->handler) ||
                    empty($warn->getExceptionTime()) ||
                    empty($warn->getLine())
                ) {
                    return false;
                }

                return true;
            }
        )->once();

        $logFactory = Mockery::mock('overload:' . LogFactory::class);
        $logFactory->shouldReceive('__construct')->withArgs([ContainerInterface::class])->once();
        $logFactory->shouldReceive('getRepository')->once()->withArgs([ElasticSearchDatabase::DATABASE_NAME])
            ->andReturn($elasticSearchRepository);

        $commandBus = new CommandBus($container);

        $this->expectException(Exception::class);
        $commandBus->handle($this->command);
    }

    /**
     * @throws InvalidArgsConfException
     */
    public function testNoSaveData(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
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

    /**
     * @throws InvalidArgsConfException
     */
    public function testNotException(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class]);

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
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

    /**
     * @throws InvalidArgsConfException
     */
    public function testInvalidArgsException(): void
    {
        $this->handler->shouldReceive('handle')->once()->withArgs([CommandInterface::class])
            ->andThrow(new Exception('Handle EXCEPTION'));

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->handler)->once();

        $container->shouldReceive('hasParameter')->withArgs([Parameters::PREFIX . '_database_type'])->once()
            ->andReturnFalse();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
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

        $commandBus = new CommandBus($container);

        $this->expectException(InvalidArgsConfException::class);
        $commandBus->handle($this->command);
    }

    /**
     * @throws InvalidArgsConfException
     */
    public function testHandleNotFoundException(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(false)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
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

    /**
     * @throws InvalidArgsConfException
     */
    public function testHandlerNotImplementsInterface(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('has')->withArgs([$this->handlerNamespace])->andReturn(true)->once();
        $container->shouldReceive('get')->withArgs([$this->handlerNamespace])->andReturn($this->command)->once();
        $container->shouldReceive('getParameter')
            ->withArgs(
                function (string $key): bool
                {
                    if (
                        Parameters::PREFIX . '_command_name' !== $key &&
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

