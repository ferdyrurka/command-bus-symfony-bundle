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
 * Class Info
 * @package Ferdyrurka\CommandBus\Entity
 */
class Info
{
    /**
     * @var string
     */
    private $result;

    /**
     * @var string
     */
    private $time;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $viewObject;

    /**
     * Info constructor.
     * @param string $result
     * @param string $time
     * @param string $query
     * @param string $command
     * @param string $viewObject
     */
    public function __construct(string $result, string $time, string $query, string $command, string $viewObject)
    {
        $this->result = $result;
        $this->time = $time;
        $this->query = $query;
        $this->command = $command;
        $this->viewObject = $viewObject;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return Logger::INFO;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getViewObject(): string
    {
        return $this->viewObject;
    }

    /**
     * @param string $viewObject
     */
    public function setViewObject(string $viewObject): void
    {
        $this->viewObject = $viewObject;
    }
}
