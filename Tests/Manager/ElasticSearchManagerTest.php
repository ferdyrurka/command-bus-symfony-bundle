<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Manager;

use Elasticsearch\Client;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Exception\EmptyEntityException;
use Ferdyrurka\CommandBus\Manager\ElasticSearch\ElasticSearchManager;
use Ferdyrurka\CommandBus\Connection\ElasticSearch\ElasticSearchConnection;
use Ferdyrurka\CommandBus\Util\ElasticSearch\ReflectionEntity;
use PHPUnit\Framework\TestCase;
use \Mockery;

/**
 * Class ElasticSearchManagerTest
 * @package Ferdyrurka\Test\CommandBus\Util
 */
class ElasticSearchManagerTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ElasticSearchConnection
     */
    private $esConnection;

    /**
     * @var ElasticSearchManager
     */
    private $esManager;

    public function setUp()
    {
        $this->esConnection = Mockery::mock(ElasticSearchConnection::class);
        $this->esManager = new ElasticSearchManager($this->esConnection);
        parent::setUp();
    }

    /**
     *
     */
    public function testPersist(): void
    {
        $this->esConnection->shouldReceive('getClient')->never();
        $this->esManager->persist(Mockery::mock(Log::class));
    }

    /**
     * @runInSeparateProcess
     */
    public function testFlush(): void
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('index')->once()->withArgs(
            function (array $args) : bool {
                if ($args['body']['exception'] !== '\Exception'||
                    $args['index'] !== 'my-index' ||
                    $args['type'] !== 'command-bus'
                ) {
                    return false;
                }

                return true;
            }
        );

        $this->esConnection->shouldReceive('getClient')->once()->andReturn($client);
        $this->esConnection->shouldReceive('getIndex')->once()->andReturn('my-index');

        $reflectionEntity = Mockery::mock('overload:' . ReflectionEntity::class);
        $reflectionEntity->shouldReceive('__construct')->withArgs([Log::class])->once();
        $reflectionEntity->shouldReceive('getGettersEntity')->once()->andReturn(['exception' => '\Exception']);

        $this->esManager->persist(Mockery::mock(Log::class));
        $this->esManager->flush();
    }

    /**
     * @runInSeparateProcess
     */
    public function testTwoFlush(): void
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('index')->times(2)->withArgs(
            function (array $args) : bool {
                if ((
                        $args['body']['exception'] !== '\Exception' &&
                        $args['body']['exception'] !== '\FerdyrurkaException'
                    ) ||
                    $args['index'] !== 'my-index' ||
                    $args['type'] !== 'command-bus'
                ) {
                    return false;
                }

                return true;
            }
        );

        $this->esConnection->shouldReceive('getClient')->once()->andReturn($client);
        $this->esConnection->shouldReceive('getIndex')->once()->andReturn('my-index');

        $reflectionEntity = Mockery::mock('overload:' . ReflectionEntity::class);
        $reflectionEntity->shouldReceive('__construct')->withArgs([Log::class])->once();
        $reflectionEntity->shouldReceive('getGettersEntity')->once()
            ->andReturn(['exception' => '\Exception'], ['exception' => '\FerdyrurkaException'])
        ;

        $this->esManager->persist(Mockery::mock(Log::class));
        $this->esManager->persist(Mockery::mock(Log::class));
        $this->esManager->flush();
    }

    /**
     * @runInSeparateProcess
     */
    public function testBodyEmptyException(): void
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('index')->never();

        $this->esConnection->shouldReceive('getClient')->once()->andReturn($client);
        $this->esConnection->shouldReceive('getIndex')->once()->andReturn('my-index');

        $reflectionEntity = Mockery::mock('overload:' . ReflectionEntity::class);
        $reflectionEntity->shouldReceive('__construct')->withArgs([Log::class])->once();
        $reflectionEntity->shouldReceive('getGettersEntity')->once()->andReturn([]);

        $this->esManager->persist(Mockery::mock(Log::class));

        $this->expectException(EmptyEntityException::class);
        $this->esManager->flush();
    }
}
