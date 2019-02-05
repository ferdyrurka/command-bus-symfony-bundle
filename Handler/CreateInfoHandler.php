<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Handler;

use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Repository\InfoRepositoryInterface;

/**
 * Class CreateInfoHandler
 * @package Ferdyrurka\CommandBus\Handler
 */
class CreateInfoHandler implements HandlerInterface
{
    /**
     * @var InfoRepositoryInterface
     */
    private $infoRepository;

    /**
     * CreateInfoHandler constructor.
     * @param InfoRepositoryInterface $infoRepository
     */
    public function __construct(InfoRepositoryInterface $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->infoRepository->create($command->getInfo());
    }
}
