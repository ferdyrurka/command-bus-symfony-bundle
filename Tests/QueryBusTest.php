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
use Ferdyrurka\CommandBus\Exception\QueryHandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Query\Handler\QueryHandlerInterface;
use Ferdyrurka\CommandBus\Query\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use Ferdyrurka\CommandBus\QueryBus;
use Ferdyrurka\CommandBus\Util\NamespaceParser;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Exception;

/**
 * Class QueryBusTest
 * @package Ferdyrurka\CommandBus\Test
 */
class QueryBusTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface
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
     * @var QueryInterface
     */
    private $query;

    /**
     * @var string
     */
    private $handlerNamespace;

    /**
     *
     */
    public function setUp(): void
    {
        $this->query = Mockery::mock(QueryInterface::class);
        $this->handler = Mockery::mock(QueryHandlerInterface::class);
        $this->container = Mockery::mock(ContainerInterface::class);
        $this->queryBus = new QueryBus($this->container);

        $namespaceParser = new NamespaceParser(\get_class($this->query), 'Query', 'QueryHandler');
        $this->handlerNamespace = $namespaceParser->getHandlerNamespaceByCommandNamespace();
    }

    /**
     * @throws \Exception
     */
    public function testHandle(): void
    {
        $viewObject = Mockery::mock(ViewObjectInterface::class);
        $this->handler->shouldReceive('handle')->withArgs([QueryInterface::class])
            ->once()->andReturn($viewObject)
        ;

        $this->setContainer(true);
        $this->container->shouldReceive('get')->once()->withArgs([$this->handlerNamespace])->andReturn($this->handler);

        $this->queryBus->handle($this->query);
    }

    /**
     * @throws QueryHandlerNotFoundException
     */
    public function testNotHasHandler(): void
    {
        $this->setContainer(false);

        $this->expectException(QueryHandlerNotFoundException::class);
        $this->queryBus->handle($this->query);
    }

    /**
     * @throws QueryHandlerNotFoundException
     */
    public function testNotImplQueryHandler(): void
    {
        $this->setContainer(true);
        $this->container->shouldReceive('get')->once()->withArgs([$this->handlerNamespace])
            ->andReturn(Mockery::mock(ViewObjectInterface::class))
        ;

        $this->expectException(QueryHandlerNotFoundException::class);
        $this->queryBus->handle($this->query);
    }

    /**
     * @throws \Exception
     */
    public function testSaveLog(): void
    {
        $this->handler->shouldReceive('handle')->withArgs([QueryInterface::class])->once()->andThrow(Exception::class);

        $createLogHandler = Mockery::mock(CreateLogHandler::class);
        $createLogHandler->shouldReceive('handle')->once()->withArgs([CreateLogCommand::class]);

        $this->setContainer(true);
        $this->container->shouldReceive('getParameter')->withArgs([Parameters::PREFIX . '_save_query_bus_log'])
            ->andReturnTrue()->once()
        ;

        $this->container->shouldReceive('get')->twice()->withArgs(
            function (string $key) : bool {
                if ($key !== CreateLogHandler::class &&
                    $key !== $this->handlerNamespace
                ) {
                    return false;
                }

                return true;
            }
        )
            ->andReturn($this->handler, $createLogHandler)
        ;

        $this->expectException(Exception::class);
        $this->queryBus->handle($this->query);
    }

    /**
     * @throws Exception
     */
    public function testNotSaveLog(): void
    {
        $this->handler->shouldReceive('handle')->once()->andThrow(Exception::class);

        $this->setContainer(true);
        $this->container->shouldReceive('get')->once()->withArgs([$this->handlerNamespace])->andReturn($this->handler);
        $this->container->shouldReceive('getParameter')->withArgs([Parameters::PREFIX . '_save_query_bus_log'])
            ->andReturnFalse()->once()
        ;

        $this->expectException(Exception::class);
        $this->queryBus->handle($this->query);
    }

    /**
     * @throws QueryHandlerNotFoundException
     */
    public function testHandleOkWithReplaceOnlyNameClass(): void
    {
        $namespaceParser = new NamespaceParser(\get_class($this->query), 'Query', 'QueryHandler');
        $this->handlerNamespace = $namespaceParser->getHandlerNamespaceByNameClass();

        $viewObject = Mockery::mock(ViewObjectInterface::class);
        $this->handler->shouldReceive('handle')->withArgs([QueryInterface::class])
            ->once()->andReturn($viewObject)
        ;

        $this->setContainer(true, true);
        $this->container->shouldReceive('get')->once()->withArgs([$this->handlerNamespace])->andReturn($this->handler);

        $this->queryBus->handle($this->query);
    }

    /**
     * @param bool $has
     * @param bool $replaceQueryNamespace
     */
    private function setContainer(bool $has, bool $replaceQueryNamespace = false): void
    {
        $this->container->shouldReceive('has')->once()->withArgs([$this->handlerNamespace])->andReturn($has);
        $this->container->shouldReceive('getParameter')->times(3)->withArgs(
            function (string $key): bool {
                if ($key !== Parameters::PREFIX . '_query_prefix' &&
                    $key !== Parameters::PREFIX . '_query_handler_prefix' &&
                    $key !== Parameters::PREFIX . '_replace_query_namespace'
                ) {
                    return false;
                }

                return true;
            }
        )->andReturn('Query', 'QueryHandler', $replaceQueryNamespace)
        ;
    }
}
