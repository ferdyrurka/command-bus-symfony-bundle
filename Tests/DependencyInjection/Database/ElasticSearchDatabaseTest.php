<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\DependencyInjection\Database;

use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use \Mockery;

/**
 * Class ElasticSearchDatabaseTest
 * @package Ferdyrurka\CommandBus\Test\DependencyInjection\Database
 */
class ElasticSearchDatabaseTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var array
     */
    private $configs;

    /**
     *
     */
    public function setUp()
    {
        $this->containerBuilder = Mockery::mock(ContainerBuilder::class);
        parent::setUp();
    }

    /**
     * @param int $setParametersTimes
     * @param array $configs
     * @throws \Ferdyrurka\CommandBus\Exception\InvalidArgsConfException
     * @dataProvider getDataParameters
     */
    public function testSetParameters(int $setParametersTimes, array $configs): void
    {
        $elasticSearchDatabase = new ElasticSearchDatabase($configs, $this->containerBuilder);

        $this->configs = $configs;

        $this->containerBuilder->shouldReceive('setParameter')->times($setParametersTimes)
            ->withArgs(
                function (string $key, $value) {
                    $prefix = Parameters::PREFIX . '_' . ElasticSearchDatabase::DATABASE_NAME . '_';

                    if (
                        $key !== $prefix . 'host' &&
                        $key !== $prefix . 'port' &&
                        $key !== $prefix . 'scheme' &&
                        $key !== $prefix . 'index' &&
                        $key !== $prefix . 'user' &&
                        $key !== $prefix . 'pass'
                    ) {
                        return false;
                    }

                    if ($value !== $this->configs['host'] &&
                        $value !== $this->configs['port'] &&
                        $value !== $this->configs['scheme'] &&
                        $value !== $this->configs['index']
                    ) {
                        if ($value !== $this->configs['user'] && $value !== $this->configs['pass']) {
                            return false;
                        }
                    }

                    return true;
                }
            )
        ;

        $elasticSearchDatabase->setParameters();
    }


    /**
     * @return array
     */
    public function getDataParameters(): array
    {
        $configs = [
            'host' => '192.168.1.1',
            'port' => 9200,
            'scheme' => 'http',
            'index' => 'my-index'
        ];

        $configsUsers = $configs;
        $configsUsers['user'] = 'admin';
        $configsUsers['pass'] = 'administrator';

        return [
            [
                'setParametersTimes' => 4,
                'configs' => $configs
            ],
            [
                'setParametersTimes' => 6,
                'configs' => $configsUsers
            ]
        ];
    }

    /**
     * @param array $configs
     * @throws InvalidArgsConfException
     * @dataProvider getDataInvalidException
     */
    public function testInvalidException(array $configs): void
    {
        $this->expectException(InvalidArgsConfException::class);
        $elasticSearchDatabase = new ElasticSearchDatabase($configs, $this->containerBuilder);
    }

    /**
     * @return array
     */
    public function getDataInvalidException(): array
    {
        return [
            [
                'configs' => [
                    'port' => 9200,
                    'scheme' => 'https',
                    'index' => 'my-index'
                ]
            ],
            [
                'configs' => [
                    'host' => 'localhost',
                    'scheme' => 'https',
                    'index' => 'my-index'
                ]
            ],
            [
                'configs' => [
                    'host' => 'localhost',
                    'port' => 9200,
                    'index' => 'my-index'
                ]
            ],
            [
                'configs' => [
                    'host' => 'localhost',
                    'port' => 9200,
                    'scheme' => 'https',
                ]
            ],
        ];
    }
}

