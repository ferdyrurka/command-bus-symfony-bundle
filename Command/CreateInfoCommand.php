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

use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

/**
 * Class CreateInfoCommand
 * @package Ferdyrurka\CommandBus\Command
 */
class CreateInfoCommand implements CommandInterface
{
    /**
     * @var ViewObjectInterface
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
     * @var integer
     */
    private $timeExecute;

    /**
     * CreateInfoCommand constructor.
     * @param ViewObjectInterface $result
     * @param string $time
     * @param string $query
     * @param string $command
     * @param string $viewObject
     */
    public function __construct(
        ViewObjectInterface $result,
        string $time,
        string $query,
        string $command,
        string $viewObject,
        int $timeExecute
    ) {
        $this->result = $result;
        $this->time = $time;
        $this->query = $query;
        $this->command = $command;
        $this->viewObject = $viewObject;
        $this->timeExecute = $timeExecute;
    }

    /**
     * @return ViewObjectInterface
     */
    public function getResult(): ViewObjectInterface
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
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
    public function getViewObject(): string
    {
        return $this->viewObject;
    }

    /**
     * @return int
     */
    public function getTimeExecute(): int
    {
        return $this->timeExecute;
    }
}
