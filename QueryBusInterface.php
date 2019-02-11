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

use Ferdyrurka\CommandBus\Query\Handler\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

/**
 * Interface QueryBusInterface
 * @package Ferdyrurka\CommandBus
 */
interface QueryBusInterface
{
    /**
     * @param QueryInterface $handler
     * @return ViewObjectInterface
     */
    public function handle(QueryInterface $handler): ViewObjectInterface;
}
