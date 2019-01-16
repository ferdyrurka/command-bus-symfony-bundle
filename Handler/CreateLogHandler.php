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
use Ferdyrurka\CommandBus\Repository\LogRepositoryInterface;

/**
 * Class CreateLogHandler
 * @package Ferdyrurka\CommandBus\Handler
 */
class CreateLogHandler implements HandlerInterface
{
    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * CreateLogHandler constructor.
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logRepository->create($command->getLog());
    }
}
