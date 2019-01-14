<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Repository;

use Elasticsearch\Client;
use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Exception\FerdyrurkaCommandBusException;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Ferdyrurka\CommandBus\Manager\ElasticSearchManager;
use Ferdyrurka\CommandBus\Repository\ElasticSearchRepository;
use PHPUnit\Framework\TestCase;
use \Mockery;
use \DateTime;

/**
 * Class ElasticSearchRepositoryTest
 * @package Ferdyrurka\CommandBus\Test\Repository
 */
class ElasticSearchRepositoryTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var Log
     */
    private $warn;

    /**
     * @throws \Exception
     */
    public function testCreate(): void
    {
        $date = new DateTime("now");


        $this->warn = new Log(
            $date->format("Y-m-d H:i:s"),
            'Hello World',
            20,
            FerdyrurkaCommandBusException::class,
            CommandInterface::class,
            HandlerInterface::class
        );

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('create')->withArgs(
            function (array $body): bool
            {
                if ($body['index'] !== 'my-index' || $body['type'] !== 'command-bus') {
                    return false;
                }

                $body = $body['body'];

                if (
                    $body['exceptionTime'] !== $this->warn->getExceptionTime() ||
                    $body['exception'] !== $this->warn->getException() ||
                    $body['message'] !== $this->warn->getMessage() ||
                    $body['line'] !== $this->warn->getLine() ||
                    $body['handler'] !== $this->warn->getHandler() ||
                    $body['command'] !== $this->warn->getCommand()
                ) {
                    return false;
                }

                return true;
            }
        )->once();

        $elasticSearchManager = Mockery::mock(ElasticSearchManager::class);
        $elasticSearchManager->shouldReceive('getIndex')->once()->andReturn('my-index');
        $elasticSearchManager->shouldReceive('getManager')->once()->andReturn($client);

        $elasticSearchRepository = new ElasticSearchRepository($elasticSearchManager);
        $elasticSearchRepository->create($this->warn);
    }
}

