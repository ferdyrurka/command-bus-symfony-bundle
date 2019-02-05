<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Manager;

/**
 * Interface ManagerInterface
 * @package Ferdyrurka\CommandBus\Manager
 */
interface ManagerInterface
{
    /**
     * @param object $entity
     */
    public function persist(object $entity): void;

    /**
     *
     */
    public function flush(): void;
}
