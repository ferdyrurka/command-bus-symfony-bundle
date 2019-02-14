<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\DependencyInjection;

use Ferdyrurka\CommandBus\DependencyInjection\Database\DatabaseInterface;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Ferdyrurka\CommandBus\Factory\DatabaseFactory;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ParametersTest
 * @package Ferdyrurka\CommandBus\Test\DependencyInjection
 */
class ParametersTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var
     */
    private $containerBuilder;

    /**
     * @var
     */
    private $config;

    /**
     *
     */
    public function setUp(): void
    {
        $this->containerBuilder = Mockery::mock(ContainerBuilder::class);
        $this->config = [
            'handler_prefix' => '',
            'command_prefix' => '',
            'query_handler_prefix' => '',
            'query_command_prefix' => '',
            'save_command_bus_log' => false,
            'save_query_bus_log' => false,
            'save_query_bus_info' => false,
            'database_type' => 'elasticsearch',
            'connection' => [
                'elasticsearch' => []
            ]
        ];

        parent::setUp();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     * @runInSeparateProcess
     */
    public function testSetParameters(): void
    {
        $this->config['save_command_bus_log'] = true;

        $databaseInterface = Mockery::mock(DatabaseInterface::class);
        $databaseInterface->shouldReceive('setParameters')->once();

        $databaseFactory = Mockery::mock('overload:' . DatabaseFactory::class);
        $databaseFactory->shouldReceive('getDatabase')->once()
            ->withArgs([$this->config['database_type']])->andReturn($databaseInterface);

        $this->setContainerToRequiredParam();

        $parameters = new Parameters($this->containerBuilder, $this->config);
        $parameters->setParameters();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     */
    public function testNoStatistic(): void
    {
        unset($this->config['connection'], $this->config['database_type']);

        $this->setContainerToRequiredParam();

        $parameters = new Parameters($this->containerBuilder, $this->config);
        $parameters->setParameters();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     */
    public function testTypeDatabaseException(): void
    {
        $this->config['save_query_bus_info'] = true;
        unset($this->config['database_type']);

        $this->setContainerToRequiredParam();

        $parameters = new Parameters($this->containerBuilder, $this->config);

        $this->expectException(InvalidArgsConfException::class);
        $parameters->setParameters();
    }

    /**
     *
     */
    private function setContainerToRequiredParam(): void
    {
        $this->containerBuilder->shouldReceive('setParameter')->times(4)
            ->withArgs(
                function (string $key, $value) {
                    $prefix = Parameters::PREFIX;

                    if ($key !== $prefix . '_handler_prefix' &&
                        $key !== $prefix . '_command_prefix' &&
                        $key !== $prefix . '_save_command_bus_log' &&
                        $key !== $prefix . '_save_query_bus_log'
                    ) {
                        return false;
                    }

                    if ($value !== $this->config['handler_prefix'] &&
                        $value !== $this->config['command_prefix'] &&
                        $value !== $this->config['save_command_bus_log'] &&
                        $value !== $this->config['save_query_bus_log']
                    ) {
                        return false;
                    }

                    return true;
                }
            )
        ;
    }
}

