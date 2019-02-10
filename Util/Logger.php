<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Util;

use Ferdyrurka\CommandBus\Exception\FerdyrurkaCommandBusException;

/**
 * Class Logger
 * @package Ferdyrurka\CommandBus\Util
 */
class Logger
{
    public const LOG = 500;
    public const INFO = 200;

    /**
     * @param int $loggerType
     * @return string
     * @throws FerdyrurkaCommandBusException
     */
    public function constToName(int $loggerType): string
    {
        switch ($loggerType) {
            case self::LOG:
                return 'log';
            case self::INFO:
                return 'info';
            default:
                throw new FerdyrurkaCommandBusException('Undefined type logger!');
        }
    }
}
