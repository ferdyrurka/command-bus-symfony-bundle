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

use Ferdyrurka\CommandBus\Entity\Info;
use Ferdyrurka\CommandBus\Manager\ManagerInterface;

/**
 * Class InfoRepository
 * @package Ferdyrurka\CommandBus\Repository
 */
class InfoRepository implements RepositoryInterface, InfoRepositoryInterface
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * InfoRepository constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Info $info
     * @return mixed|void
     */
    public function create(Info $info)
    {
        $this->manager->persist($info);
        $this->manager->flush();
    }
}
