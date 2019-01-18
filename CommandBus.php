<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus;

use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\HandlerNotFoundException;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Exception;

/**
 * Class CommandBus
 * @package Ferdyrurka\CommandBus
 */
class CommandBus implements CommandBusInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * CommandBus constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param CommandInterface $command
     * @throws Exception
     */
    public function handle(CommandInterface $command): void
    {
        try {
            $this->handler = $this->getHandleFromCommand(\get_class($command));
            $this->handler->handle($command);
        } catch (Exception $e) {
            if ((bool) $this->container->getParameter(Parameters::PREFIX . '_save_statistic_handler')) {
                if (\is_object($this->handler)) {
                    $handlerNamespace = \get_class($this->handler);
                } else {
                    $handlerNamespace = '';
                }

                $createLogCommand = new CreateLogCommand(
                    $e->getMessage(),
                    $e->getLine(),
                    \get_class($e),
                    \get_class($command),
                    $handlerNamespace
                );

                $createLogHandler = $this->container->get(CreateLogHandler::class);
                $createLogHandler->handle($createLogCommand);
            }

            throw $e;
        }
    }

    /**
     * @param string $commandNamespace
     * @return HandlerInterface
     * @throws HandlerNotFoundException
     */
    protected function getHandleFromCommand(string $commandNamespace): HandlerInterface
    {
        $handlerNamespace = str_replace(
            $this->container->getParameter(Parameters::PREFIX . '_command_name'),
            $this->container->getParameter(Parameters::PREFIX . '_handler_name'),
            $commandNamespace
        );

        if (!$this->container->has($handlerNamespace)) {
            throw new HandlerNotFoundException('Handler not found by namespace: ' . $handlerNamespace);
        }

        $handler = $this->container->get($handlerNamespace);

        if (!$handler instanceof HandlerInterface) {
            throw new HandlerNotFoundException('Object not implements HandlerInterface: ' . $handlerNamespace);
        }

        return $handler;
    }
}
