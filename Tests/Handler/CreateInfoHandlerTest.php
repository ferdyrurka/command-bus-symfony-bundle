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
use Ferdyrurka\CommandBus\Factory\CreateInfoFactory;
use Ferdyrurka\CommandBus\Handler\CreateInfoHandler;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;
use Ferdyrurka\CommandBus\Repository\InfoRepositoryInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CreateInfoHandlerTest
 * @package Ferdyrurka\CommandBus\Test\Handler
 */
class CreateInfoHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @runInSeparateProcess
     */
    public function testHandle(): void
    {
        $serializer = Mockery::mock('overload:' . Serializer::class);
        $serializer->shouldReceive('__construct')
            ->withArgs(
                function (array $normalizers, array $encoders): bool {
                    if (!$normalizers[0] instanceof ObjectNormalizer ||
                        !$encoders[0] instanceof JsonEncode
                    ) {
                        return false;
                    }

                    return true;
                }
            )->once();
        ;
        $serializer->shouldReceive('serialize')->once()->withArgs([ViewObjectInterface::class, 'json'])
            ->andReturn(json_encode(['data' => 'helloWorld']));

        $createInfoFactory = Mockery::mock('overload:' . CreateInfoFactory::class);
        $createInfoFactory->shouldReceive('__construct')->once()
            ->withArgs(
                function (CreateInfoCommand $createInfoCommand, string $serialize) {
                    if (json_decode($serialize, true)['data'] !== 'helloWorld') {
                        return false;
                    }

                    return true;
                }
            )
        ;
        $createInfoFactory->shouldReceive('createInfo')->once()->andReturn(Mockery::mock(Info::class));

        $repository = Mockery::mock(InfoRepositoryInterface::class);
        $repository->shouldReceive('create')->withArgs([Info::class])->once();

        $command = Mockery::mock(CreateInfoCommand::class);
        $command->shouldReceive('getResult')->once()->andReturn();

        $handler = new CreateInfoHandler($repository);
        $handler->handle($command);
    }
}

