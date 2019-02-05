<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Util\ElasticSearch;

use Ferdyrurka\CommandBus\Command\CommandInterface;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Handler\HandlerInterface;
use Ferdyrurka\CommandBus\Util\ReflectionEntity;
use PHPUnit\Framework\TestCase;
use \Exception;

/**
 * Class ReflectionEntityTest
 * @package Ferdyrurka\CommandBus\Test\Util\ElasticSearch
 */
class ReflectionEntityTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGetGettersEntity(): void
    {
        $entity = new Log(
            '17.02.1999 12:00:00',
            'This is message',
            10,
            Exception::class,
            HandlerInterface::class,
            CommandInterface::class
        );

        $reflectionEntity = new ReflectionEntity($entity);
        $result = $reflectionEntity->getGettersEntity();

        $this->assertEquals($entity->getExceptionTime(), $result['exceptionTime']);
        $this->assertEquals($entity->getMessage(), $result['message']);
        $this->assertEquals($entity->getLine(), $result['line']);
        $this->assertEquals($entity->getException(), $result['exception']);
        $this->assertEquals($entity->getCommand(), $result['command']);
        $this->assertEquals($entity->getHandler(), $result['handler']);
        $this->assertFalse(isset($result['__construct']));
    }
}

