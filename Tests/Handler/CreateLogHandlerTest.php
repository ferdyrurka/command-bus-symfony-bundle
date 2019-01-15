<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Handler;

use Ferdyrurka\CommandBus\Command\CreateLogCommand;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Handler\CreateLogHandler;
use Ferdyrurka\CommandBus\Repository\LogRepositoryInterface;
use \Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateLogHandlerTest
 * @package Ferdyrurka\CommandBus\Test\Handler
 */
class CreateLogHandlerTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     *
     */
    public function testHandle(): void
    {
        $logRepository = Mockery::mock(LogRepositoryInterface::class);
        $logRepository->shouldReceive('create')->once()->withArgs([Log::class]);

        $command = Mockery::mock(CreateLogCommand::class);
        $command->shouldReceive('getLog')->andReturn(Mockery::mock(Log::class));

        $createLogHandler = new CreateLogHandler($logRepository);
        $createLogHandler->handle($command);
    }
}

