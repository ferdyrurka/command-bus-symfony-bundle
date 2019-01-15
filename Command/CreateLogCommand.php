<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Command;

use Ferdyrurka\CommandBus\Entity\Log;
use \DateTime;

/**
 * Class CreateLogCommand
 * @package Ferdyrurka\CommandBus\Command
 */
class CreateLogCommand implements CommandInterface
{
    /**
     * @var Log
     */
    private $log;

    /**
     * CreateLogCommand constructor.
     * @param string $message
     * @param int $line
     * @param string $exception
     * @param string $command
     * @param string $handler
     * @throws \Exception
     */
    public function __construct(
        string $message,
        int $line,
        string $exception,
        string $command,
        string $handler
    ) {
        $date = new DateTime("now");

        $this->log = new Log($date->format("Y-m-d H:i:s"), $message, $line, $exception, $command, $handler);
    }

    /**
     * @return Log
     */
    public function getLog(): Log
    {
        return $this->log;
    }
}
