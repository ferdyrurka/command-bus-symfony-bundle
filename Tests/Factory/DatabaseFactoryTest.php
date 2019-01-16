<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Factory;

use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\Exception\InvalidArgsConfException;
use Ferdyrurka\CommandBus\Exception\UndefinedDatabaseTypeException;
use Ferdyrurka\CommandBus\Factory\DatabaseFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use \Mockery;

class DatabaseFactoryTest extends TestCase
{
    /**
     * @var DatabaseFactory
     */
    private $databaseFactory;

    public function setUp(): void
    {
        $this->databaseFactory = new DatabaseFactory(
            ['elasticsearch' => [
                    'host' => 'localhost',
                    'scheme' => 'http',
                    'port' => 9200,
                    'index' => 'index'
                ]
            ],
            Mockery::mock(ContainerBuilder::class)
        );

        parent::setUp();
    }

    public function testGetDatabase(): void
    {
        $this->assertInstanceOf(
            ElasticSearchDatabase::class,
            $this->databaseFactory->getDatabase(ElasticSearchDatabase::DATABASE_NAME)
        );
    }

    public function testUndefinedKeyException(): void
    {
        $this->expectException(UndefinedDatabaseTypeException::class);
        $this->databaseFactory->getDatabase('NOT INDEX');
    }

    public function testThrowConstructor(): void
    {
        $databaseFactory = new DatabaseFactory(
            ['elasticsearch' => []],
            Mockery::mock(ContainerBuilder::class)
        );

        $this->expectException(InvalidArgsConfException::class);
        $databaseFactory->getDatabase(ElasticSearchDatabase::DATABASE_NAME);
    }
}

