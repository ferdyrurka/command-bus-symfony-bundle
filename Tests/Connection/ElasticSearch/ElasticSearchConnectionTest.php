<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Connection\ElasticSearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Connection\ElasticSearch\ElasticSearchConnection;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ElasticSearchConnectionTest
 * @package Ferdyrurka\CommandBus\Test\Util\ElasticSearch
 */
class ElasticSearchConnectionTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     *
     */
    private const PREFIX = Parameters::PREFIX . '_' . ElasticSearchDatabase::DATABASE_NAME . '_';

    /**
     * @var array
     */
    private $host;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ClientBuilder
     */
    private $clientBuilder;

    /**
     * @var Client
     */
    private $client;

    /**
     *
     */
    public function setUp(): void
    {
        $this->container = Mockery::mock(ContainerInterface::class);

        parent::setUp();
    }

    /**
     *
     */
    public function setUpClient(): void
    {
        $this->host = [
            'host' => 'elasticsearch',
            'port' => '9300',
            'scheme' => 'http'
        ];

        $this->client = Mockery::mock(Client::class);

        $this->clientBuilder = Mockery::mock('alias:' . ClientBuilder::class);
        $this->clientBuilder->shouldReceive('setRetries')->withArgs([2])->once()->andReturn($this->clientBuilder);
        $this->clientBuilder->shouldReceive('build')->once()->andReturn($this->client);
        $this->clientBuilder->shouldReceive('create')->once()->andReturn($this->clientBuilder);

        $this->clientBuilder->shouldReceive('setHosts')->withArgs(
            function (array $host): bool {
                foreach ($host as $key => $value) {
                    if ($this->host[$key] !== $value) {
                        return false;
                    }
                }

                return true;
            }
        )
            ->andReturn($this->clientBuilder)
        ;

        parent::setUp();
    }

    /**
     *
     */
    public function testGetIndex(): void
    {
        $this->container->shouldReceive('getParameter')->withArgs([self::PREFIX . 'index'])
            ->andReturn('my-index')
        ;

        $elasticSearchConnection = new ElasticSearchConnection($this->container);
        $this->assertEquals('my-index', $elasticSearchConnection->getIndex());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateClientNotAuth(): void
    {
        $this->setUpClient();

        $this->container->shouldReceive('hasParameter')->andReturnFalse()->once();
        $this->container->shouldReceive('getParameter')->times(3)->withArgs(
            function (string $key): bool {
                if ($key !== self::PREFIX . 'host' &&
                    $key !== self::PREFIX . 'port' &&
                    $key !== self::PREFIX . 'scheme'
                ) {
                    return false;
                }

                return true;
            }
        )
            ->andReturn($this->host['host'], $this->host['port'], $this->host['scheme'])
        ;

        $elasticSearchConnection = new ElasticSearchConnection($this->container);
        $this->assertInstanceOf(Client::class, $elasticSearchConnection->getClient());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateClientAuth(): void
    {
        $this->setUpClient();

        $this->host['user'] = 'admin';
        $this->host['pass'] = 'admin!@#$%';

        $this->container->shouldReceive('hasParameter')->andReturnTrue()->times(2);
        $this->container->shouldReceive('getParameter')->times(5)->withArgs(
            function (string $key): bool {
                if ($key !== self::PREFIX . 'host' &&
                    $key !== self::PREFIX . 'port' &&
                    $key !== self::PREFIX . 'scheme' &&
                    $key !== self::PREFIX . 'user' &&
                    $key !== self::PREFIX . 'pass'
                ) {
                    return false;
                }

                return true;
            }
        )
            ->andReturn(
                $this->host['host'],
                $this->host['port'],
                $this->host['scheme'],
                $this->host['user'],
                $this->host['pass']
            )
        ;

        $elasticSearchConnection = new ElasticSearchConnection($this->container);
        $this->assertInstanceOf(Client::class, $elasticSearchConnection->getClient());
    }
}
