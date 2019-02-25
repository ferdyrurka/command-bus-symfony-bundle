<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Util;

/**
 * Class NamespaceParser
 * @package Ferdyrurka\CommandBus\Util
 */
class NamespaceParser
{
    /**
     * @var string
     */
    private $commandNamespace;

    /**
     * @var string
     */
    private $commandPrefix;

    /**
     * @var string
     */
    private $handlerPrefix;

    /**
     * NamespaceParser constructor.
     * @param string $commandNamespace
     * @param string $commandPrefix
     * @param string $handlerPrefix
     */
    public function __construct(string $commandNamespace, string $commandPrefix, string $handlerPrefix)
    {
        $this->commandNamespace = $commandNamespace;
        $this->commandPrefix = $commandPrefix;
        $this->handlerPrefix = $handlerPrefix;
    }


    /**
     * @return string
     */
    public function getHandlerNamespaceByNameClass(): string
    {
        $commandNamespaceArray = explode('\\', $this->commandNamespace);
        $commandName = end($commandNamespaceArray);

        $commandHandlerName = str_replace(
            $this->commandPrefix,
            $this->handlerPrefix,
            $commandName
        );

        return str_replace($commandName, $commandHandlerName, $this->commandNamespace);
    }

    /**
     * @return string
     */
    public function getHandlerNamespaceByCommandNamespace(): string
    {
        return str_replace(
            $this->commandPrefix,
            $this->handlerPrefix,
            $this->commandNamespace
        );
    }
}
