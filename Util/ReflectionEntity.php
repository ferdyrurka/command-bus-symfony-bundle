<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Util;

use \ReflectionClass;

/**
 * Class ReflectionEntity
 * @package Ferdyrurka\CommandBus\Util\ElasticSearch
 */
final class ReflectionEntity implements ReflectionEntityInterface
{
    /**
     * @var object
     */
    private $entity;

    /**
     * ReflectionEntity constructor.
     * @param object $entity
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getGettersEntity(): array
    {
        $reflection = new ReflectionClass($this->entity);
        $gettersInArray = [];

        foreach ($reflection->getMethods() as $method) {
            $nameMethod = $method->getName();

            if (strpos($nameMethod,'get') === false) {
                continue;
            }

            $gettersInArray[lcfirst(str_ireplace('get','', $nameMethod))] = $this->entity->$nameMethod();
        }

        return $gettersInArray;
    }
}
