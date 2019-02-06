<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);


namespace Ferdyrurka\CommandBus\Entity;


use Ferdyrurka\CommandBus\Util\Logger;

/**
 * Class Log
 * @package Ferdyrurka\CommandBus\Entity
 */
class Log
{
    /**
     * @var string
     */
    private $exceptionTime;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $line;

    /**
     * @var string
     */
    private $exception;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $handler;

    /**
     * Log constructor.
     * @param string $exceptionTime
     * @param string $message
     * @param int $line
     * @param string $exception
     * @param string $command
     * @param string $handler
     */
    public function __construct(
        string $exceptionTime,
        string $message,
        int $line,
        string $exception,
        string $command,
        string $handler
    ) {
        $this->exceptionTime = $exceptionTime;
        $this->message = $message;
        $this->line = $line;
        $this->exception = $exception;
        $this->command = $command;
        $this->handler = $handler;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return Logger::LOG;
    }

    /**
     * @return string
     */
    public function getExceptionTime(): string
    {
        return $this->exceptionTime;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getException(): string
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handler;
    }
}
