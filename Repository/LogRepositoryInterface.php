<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Repository;

use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Manager\ElasticSearch\ManagerInterface;

/**
 * Interface LogRepositoryInterface
 * @package Ferdyrurka\CommandBus\Repository
 */
interface LogRepositoryInterface
{
    /**
     * LogRepositoryInterface constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager);

    /**
     * @param Log $log
     */
    public function create(Log $log): void;
}
