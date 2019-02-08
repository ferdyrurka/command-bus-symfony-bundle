<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus;

use Ferdyrurka\CommandBus\Command\CreateInfoCommand;
use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\QueryHandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateInfoHandler;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Query\Command\QueryCommandInterface;
use Ferdyrurka\CommandBus\Query\QueryHandlerInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use Symfony\Component\DependencyInjection\Container;
use \Exception;
use \DateTime;

/**
 * Class QueryBus
 * @package Ferdyrurka\CommandBus
 */
class QueryBus implements QueryBusInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var null
     */
    protected $handler = null;

    /**
     * QueryBus constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param QueryCommandInterface $queryCommand
     * @return ViewObjectInterface
     * @throws Exception
     */
    public function handle(QueryCommandInterface $queryCommand): ViewObjectInterface
    {
        $timeExecuteStart = microtime(true);

        try {
            $this->handler = $this->getQueryHandlerFromCommand(\get_class($queryCommand));
            $queryResult = $this->handler->handle($queryCommand);
        } catch (Exception $exception) {
            if ($this->container->getParameter(Parameters::PREFIX . '_save_query_bus_log')) {
                $this->saveLog($exception, \get_class($queryCommand));
            }

            throw $exception;
        }

        if ($this->container->getParameter(Parameters::PREFIX . '_save_query_bus_info')) {
            $date = new DateTime('now');
            $handlerNamespace = '';

            if ($this->handler !== null) {
                $handlerNamespace = \get_class($this->handler);
            }

            $this->saveInfo(new CreateInfoCommand(
                $queryResult,
                $date->format('Y-m-d H:i:s'),
                $handlerNamespace,
                \get_class($queryCommand),
                \get_class($queryResult),
                microtime(true) - $timeExecuteStart
            ));
        }

        return $queryResult;
    }

    /**
     * @param string $commandNamespace
     * @return QueryHandlerInterface
     * @throws QueryHandlerNotFoundException
     */
    protected function getQueryHandlerFromCommand(string $commandNamespace) : QueryHandlerInterface
    {
        $handlerNamespace = str_replace(
            $this->container->getParameter(Parameters::PREFIX . '_query_command_prefix'),
            $this->container->getParameter(Parameters::PREFIX . '_query_handler_prefix'),
            $commandNamespace
        );

        if (!$this->container->has($handlerNamespace)) {
            throw new QueryHandlerNotFoundException('Query Handler not found by namespace: ' . $handlerNamespace);
        }

        $handler = $this->container->get($handlerNamespace);

        if (!$handler instanceof QueryHandlerInterface) {
            throw new QueryHandlerNotFoundException(
                'Object not implements QueryHandlerInterface: ' . $handlerNamespace
            );
        }

        return $handler;
    }

    /**
     * @param Exception $exception
     * @param string $commandNamespace
     * @throws Exception
     */
    protected function saveLog(Exception $exception, string $commandNamespace): void
    {
        $handlerNamespace = '';

        if ($this->handler !== null) {
            $handlerNamespace = \get_class($this->handler);
        }

        $createLogCommand = new CreateLogCommand(
            $exception->getMessage(),
            $exception->getLine(),
            \get_class($exception),
            $commandNamespace,
            $handlerNamespace
        );

        $createLogHandler = $this->container->get(CreateLogHandler::class);
        $createLogHandler->handle($createLogCommand);
    }

    /**
     * @param CreateInfoCommand $createInfoCommand
     * @throws Exception
     */
    protected function saveInfo(CreateInfoCommand $createInfoCommand): void
    {
        $createInfoHandler = $this->container->get(CreateInfoHandler::class);
        $createInfoHandler->handle($createInfoCommand);
    }
}
