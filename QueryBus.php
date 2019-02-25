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

use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\QueryHandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Query\Handler\QueryHandlerInterface;
use Ferdyrurka\CommandBus\Query\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use \Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QueryBus
 * @package Ferdyrurka\CommandBus
 */
class QueryBus implements QueryBusInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var null
     */
    protected $handler = null;

    /**
     * QueryBus constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param QueryInterface $query
     * @return ViewObjectInterface
     * @throws QueryHandlerNotFoundException
     * @throws Exception
     */
    public function handle(QueryInterface $query): ViewObjectInterface
    {
        $this->handler = $this->getQueryHandlerFromCommand(\get_class($query));

        try {
            $queryResult = $this->handler->handle($query);
        } catch (Exception $exception) {
            if ($this->container->getParameter(Parameters::PREFIX . '_save_query_bus_log')) {
                $this->saveLog($exception, \get_class($query));
            }

            throw $exception;
        }

        return $queryResult;
    }

    /**
     * @param string $queryNamespace
     * @return QueryHandlerInterface
     * @throws QueryHandlerNotFoundException
     */
    protected function getQueryHandlerFromCommand(string $queryNamespace) : QueryHandlerInterface
    {
        if ((bool) $this->container->getParameter(Parameters::PREFIX . '_replace_query_namespace')) {
            $handlerNamespace = $this->getHandlerNamespaceByNameClass($queryNamespace);
        } else {
            $handlerNamespace = $this->getHandlerNamespaceByQueryNamespace($queryNamespace);
        }

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
     * @param string $queryNamespace
     * @throws Exception
     */
    protected function saveLog(Exception $exception, string $queryNamespace): void
    {
        $handlerNamespace = '';

        if ($this->handler !== null) {
            $handlerNamespace = \get_class($this->handler);
        }

        $createLogCommand = new CreateLogCommand(
            $exception->getMessage(),
            $exception->getLine(),
            \get_class($exception),
            $queryNamespace,
            $handlerNamespace
        );

        $createLogHandler = $this->container->get(CreateLogHandler::class);
        $createLogHandler->handle($createLogCommand);
    }

    protected function getHandlerNamespaceByNameClass(string $queryNamespace): string
    {
        $queryNamespaceArray = explode('//', $queryNamespace);
        $queryName = end($queryNamespaceArray);

        $queryHandlerName = str_replace(
            $this->container->getParameter(Parameters::PREFIX . '_query_prefix'),
            $this->container->getParameter(Parameters::PREFIX . '_query_handler_prefix'),
            $queryName
        );

        return str_replace($queryName, $queryHandlerName, $queryNamespace);
    }

    protected function getHandlerNamespaceByQueryNamespace(string $queryNamespace): string
    {
        return str_replace(
            $this->container->getParameter(Parameters::PREFIX . '_query_prefix'),
            $this->container->getParameter(Parameters::PREFIX . '_query_handler_prefix'),
            $queryNamespace
        );
    }
}
