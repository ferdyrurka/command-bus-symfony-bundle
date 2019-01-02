<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Handler;

use Ferdyrurka\CommandBus\Command\CommandInterface;

/**
 * Interface HandlerInterface
 * @package Ferdyrurka\CommandBus\Handler
 */
interface HandlerInterface
{
    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command): void;
}
