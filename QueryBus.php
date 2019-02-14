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
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Query\Handler\QueryInterface;
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
     * @param QueryInterface $handler
     * @return ViewObjectInterface
     * @throws Exception
     */
    public function handle(QueryInterface $handler): ViewObjectInterface
    {
        $this->handler = $handler;

        try {
            $queryResult = $this->handler->handle();
        } catch (Exception $exception) {
            if ($this->container->getParameter(Parameters::PREFIX . '_save_query_bus_log')) {
                $this->saveLog($exception);
            }

            throw $exception;
        }

        return $queryResult;
    }

    /**
     * @param Exception $exception
     * @throws Exception
     */
    protected function saveLog(Exception $exception): void
    {
        $handlerNamespace = '';

        if ($this->handler !== null) {
            $handlerNamespace = \get_class($this->handler);
        }

        $createLogCommand = new CreateLogCommand(
            $exception->getMessage(),
            $exception->getLine(),
            \get_class($exception),
            '',
            $handlerNamespace
        );

        $createLogHandler = $this->container->get(CreateLogHandler::class);
        $createLogHandler->handle($createLogCommand);
    }
}
