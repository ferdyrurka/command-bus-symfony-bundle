<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
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
 * Class LogRepository
 * @package Ferdyrurka\CommandBus\Repository
 */
class LogRepository implements RepositoryInterface, LogRepositoryInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * LogRepository constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Log $log
     */
    public function create(Log $log): void
    {
        $this->manager->persist($log);
        $this->manager->flush();
    }
}
