<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Factory;

use Ferdyrurka\CommandBus\Command\CreateInfoCommand;
use Ferdyrurka\CommandBus\Entity\Info;

/**
 * Class CreateInfoFactory
 * @package Ferdyrurka\CommandBus\Factory
 */
class CreateInfoFactory
{
    /**
     * @var CreateInfoCommand
     */
    private $createInfoCommand;

    /**
     * @var string
     */
    private $result;

    /**
     * CreateInfoFactory constructor.
     * @param CreateInfoCommand $createInfoCommand
     * @param string $result
     */
    public function __construct(CreateInfoCommand $createInfoCommand, string $result)
    {
        $this->createInfoCommand = $createInfoCommand;
        $this->result = $result;
    }

    /**
     * @return Info
     */
    public function createInfo(): Info
    {
        return new Info(
            $this->result,
            $this->createInfoCommand->getTime(),
            $this->createInfoCommand->getQuery(),
            $this->createInfoCommand->getCommand(),
            $this->createInfoCommand->getViewObject()
        );
    }
}
