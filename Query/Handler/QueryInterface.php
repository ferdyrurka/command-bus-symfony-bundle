<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Query;

use Ferdyrurka\CommandBus\Query\Command\QueryCommandInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

/**
 * Interface QueryInterface
 * @package Ferdyrurka\CommandBus\Query
 */
interface QueryInterface
{
    /**
     * @param QueryCommandInterface $queryCommand
     * @return ViewObjectInterface
     */
    public function handle(QueryCommandInterface $queryCommand): ViewObjectInterface;
}
