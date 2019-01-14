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
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Ferdyrurka\CommandBus\Exception\HandlerNotFoundException;
use Ferdyrurka\CommandBus\Factory\LogFactory;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Ferdyrurka\CommandBus\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Exception;
use \DateTime;

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
     * CommandBus constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param CommandInterface $command
     * @throws InvalidArgsConfException
     * @throws Exception
     */
    public function handle(CommandInterface $command): void
    {
        try{
            $handler = $this->getHandleFromCommand(\get_class($command));
            $handler->handle($command);
        }catch (Exception $e) {
            if ((bool) $this->container->getParameter(Parameters::PREFIX . '_save_statistic_handler')) {
                $date = new DateTime("now");

                if (\is_object($handler)) {
                    $handlerNamespace = \get_class($handler);
                } else {
                    $handlerNamespace = '';
                }

                $warn = new Log(
                    $date->format("Y-m-d H:i:s"),
                    $e->getMessage(),
                    $e->getLine(),
                    \get_class($e),
                    \get_class($command),
                    $handlerNamespace
                );

                $logRepository = $this->getLogRepository();
                $logRepository->create($warn);
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

        if(!$this->container->has($handlerNamespace)) {
            throw new HandlerNotFoundException('Handler not found by namespace: ' . $handlerNamespace);
        }

        $handler = $this->container->get($handlerNamespace);

        if (!$handler instanceof HandlerInterface) {
            throw new HandlerNotFoundException('Object not implements HandlerInterface: ' . $handlerNamespace);
        }

        return $handler;
    }

    /**
     * @return RepositoryInterface
     * @throws Ferdyrurka\CommandBus\Exception\LogFactoryException
     * @throws InvalidArgsConfException
     */
    private function getLogRepository(): RepositoryInterface
    {
        if (!$this->container->hasParameter(Parameters::PREFIX . '_database_type')) {
            throw new InvalidArgsConfException('No has parameter database_type');
        }

        $logFactory = new LogFactory($this->container);

        return $logFactory->getRepository(
            (string) $this->container->getParameter(Parameters::PREFIX . '_database_type')
        );

    }
}
