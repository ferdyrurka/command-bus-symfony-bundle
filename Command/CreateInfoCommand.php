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

use Ferdyrurka\CommandBus\Entity\Info;

/**
 * Class CreateInfoCommand
 * @package Ferdyrurka\CommandBus\Command
 */
class CreateInfoCommand implements CommandInterface
{
    /**
     * @var Info
     */
    private $info;

    /**
     * CreateInfoCommand constructor.
     * @param string $result
     * @param string $time
     * @param string $query
     * @param string $command
     * @param string $viewObject
     */
    public function __construct(string $result, string $time, string $query, string $command, string $viewObject)
    {
        $this->info = new Info($result, $time, $query, $command, $viewObject);
    }

    /**
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }
}
