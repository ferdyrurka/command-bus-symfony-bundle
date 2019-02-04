<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Manager\ElasticSearch;

use Elasticsearch\Client;
use Ferdyrurka\CommandBus\Exception\EmptyEntityException;
use Ferdyrurka\CommandBus\Util\ElasticSearch\ElasticSearchConnection;
use Ferdyrurka\CommandBus\Util\ElasticSearch\ReflectionEntity;

/**
 * Class ElasticSearchManager
 * @package Ferdyrurka\CommandBus\Util
 */
class ElasticSearchManager implements ManagerInterface
{
    /**
     * @var ElasticSearchConnection
     */
    protected $elasticSearchConnection;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var array
     */
    protected $persists;

    /**
     * ElasticSearchManager constructor.
     * @param ElasticSearchConnection $elasticSearchConnection
     */
    public function __construct(ElasticSearchConnection $elasticSearchConnection)
    {
        $this->elasticSearchConnection = $elasticSearchConnection;
    }

    /**
     * @param object $entity
     */
    public function persist(object $entity): void
    {
        $this->persists[] = $entity;
    }

    /**
     * @throws EmptyEntityException
     * @throws \ReflectionException
     */
    public function flush(): void
    {
        if (!$this->client instanceof Client || empty($this->index)) {
            $this->client = $this->elasticSearchConnection->getClient();
            $this->index = $this->elasticSearchConnection->getIndex();
        }

        foreach ($this->persists as $persist) {
            $reflectionEntity = new ReflectionEntity($persist);
            $body = $reflectionEntity->getGettersEntity();

            if (empty($body)) {
                throw new EmptyEntityException('Body is empty');
            }

            $this->client->index([
                'index' => $this->index,
                'type' => 'command-bus',
                'body' => $body
            ]);
        }
    }
}
