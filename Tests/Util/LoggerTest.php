<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Util;

use Ferdyrurka\CommandBus\Exception\FerdyrurkaCommandBusException;
use Ferdyrurka\CommandBus\Util\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerTest
 * @package Ferdyrurka\CommandBus\Test\Util
 */
class LoggerTest extends TestCase
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->logger = new Logger();
    }

    /**
     * @throws FerdyrurkaCommandBusException
     */
    public function testConstToName(): void
    {
        $this->assertEquals('info', $this->logger->constToName(Logger::INFO));
        $this->assertEquals('log', $this->logger->constToName(Logger::LOG));
    }

    /**
     * @throws FerdyrurkaCommandBusException
     */
    public function testUndefinedConst(): void
    {
        $this->expectException(FerdyrurkaCommandBusException::class);
        $this->logger->constToName(30000);
    }
}

