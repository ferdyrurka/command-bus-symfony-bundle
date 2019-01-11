<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\DependencyInjection\Database;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface DatabaseInterface
 * @package Ferdyrurka\CommandBus\DependencyInjection\Database
 */
interface DatabaseInterface
{
    /**
     * DatabaseInterface constructor.
     * @param array $configs
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(array $configs, ContainerBuilder $containerBuilder);

    /**
     * @return void
     */
    public function setParameters(): void;
}
