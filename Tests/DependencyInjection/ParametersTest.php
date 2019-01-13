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
use Ferdyrurka\CommandBus\FactoryMethod\DatabaseFactory;
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

        parent::setUp();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     * @runInSeparateProcess
     */
    public function testSetParameters(): void
    {
        $this->config = [
            'handler_name' => '',
            'command_name' => '',
            'save_statistic_handler' => true,
            'database_type' => 'elasticsearch',
            'connection' => [
                'elasticsearch' => []
            ]
        ];

        $databaseInterface = Mockery::mock(DatabaseInterface::class);
        $databaseInterface->shouldReceive('setParameters')->once();

        $databaseFactory = Mockery::mock('overload:' . DatabaseFactory::class);
        $databaseFactory->shouldReceive('getDatabase')->once()
            ->withArgs([$this->config['database_type']])->andReturn($databaseInterface);

        $this->containerBuilder->shouldReceive('setParameter')->times(4)
            ->withArgs(
                function (string $key, $value) {
                    $prefix = Parameters::PREFIX;

                    if (
                        $key !== $prefix . '_handler_name' &&
                        $key !== $prefix . '_command_name' &&
                        $key !== $prefix . '_save_statistic_handler' &&
                        $key !== $prefix . '_database_type'
                    ) {
                        return false;
                    }

                    if (
                        $value !== $this->config['handler_name'] &&
                        $value !== $this->config['command_name'] &&
                        $value !== $this->config['save_statistic_handler'] &&
                        $value !== $this->config['database_type']
                    ) {
                        return false;
                    }

                    return true;
                }
            )
        ;

        $parameters = new Parameters($this->containerBuilder, $this->config);
        $parameters->setParameters();
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     */
    public function testNoStatistic(): void
    {
        $this->config = [
            'handler_name' => '',
            'command_name' => '',
            'save_statistic_handler' => false
        ];

        $this->setContainerToRequiredParam();

        $parameters = new Parameters($this->containerBuilder, $this->config);
        $parameters->setParameters();

    }

    /**
     * @throws InvalidArgsConfException
     */
    public function testValidConfException(): void
    {
        $config = [
            0 => [Parameters::PREFIX => []]
        ];

        $this->expectException(InvalidArgsConfException::class);
        new Parameters($this->containerBuilder, $config);
    }

    /**
     * @throws InvalidArgsConfException
     * @throws \Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException
     */
    public function testTypeDatabaseException(): void
    {
        $this->config = [
            'handler_name' => '',
            'command_name' => '',
            'save_statistic_handler' => true
        ];

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
        $this->containerBuilder->shouldReceive('setParameter')->times(3)
            ->withArgs(
                function (string $key, $value) {
                    $prefix = Parameters::PREFIX;

                    if (
                        $key !== $prefix . '_handler_name' &&
                        $key !== $prefix . '_command_name' &&
                        $key !== $prefix . '_save_statistic_handler'
                    ) {
                        return false;
                    }

                    if (
                        $value !== $this->config['handler_name'] &&
                        $value !== $this->config['command_name'] &&
                        $value !== $this->config['save_statistic_handler']
                    ) {
                        echo $this->config['save_statistic_handler'];
                        return false;
                    }

                    return true;
                }
            )
        ;
    }
}

