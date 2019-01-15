<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Util\ElasticSearch;

/**
 * Interface ReflectionEntityInterface
 * @package Ferdyrurka\CommandBus\Util\ElasticSearch
 */
interface ReflectionEntityInterface
{
    /**
     * ReflectionEntityInterface constructor.
     * @param object $entity
     */
    public function __construct(object $entity);

    /**
     * @return array
     */
    public function getGettersEntity(): array;
}
