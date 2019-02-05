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

use Ferdyrurka\CommandBus\Command\CreateInfoCommand;
use Ferdyrurka\CommandBus\Entity\Info;
use Ferdyrurka\CommandBus\Handler\CreateInfoHandler;
use Ferdyrurka\CommandBus\Repository\InfoRepositoryInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use \Mockery;

/**
 * Class CreateInfoHandlerTest
 * @package Ferdyrurka\CommandBus\Test\Handler
 */
class CreateInfoHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     *
     */
    public function testHandle(): void
    {
        $repository = Mockery::mock(InfoRepositoryInterface::class);
        $repository->shouldReceive('create')->withArgs([Info::class])->once();

        $command = Mockery::mock(CreateInfoCommand::class);
        $command->shouldReceive('getInfo')->once()->andReturn(Mockery::mock(Info::class));

        $handler = new CreateInfoHandler($repository);
        $handler->handle($command);
    }
}

