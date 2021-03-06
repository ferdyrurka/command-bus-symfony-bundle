<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Query\Handler;

use Ferdyrurka\CommandBus\Query\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

/**
 * Interface QueryHandlerInterface
 * @package Ferdyrurka\CommandBus\Query\Handler
 */
interface QueryHandlerInterface
{
    /**
     * @return ViewObjectInterface
     */
    public function handle(QueryInterface $query): ViewObjectInterface;
}
