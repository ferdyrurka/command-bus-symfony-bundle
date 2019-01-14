<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Repository;

use Elasticsearch\Client;
use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Manager\ElasticSearchManager;

/**
 * Class ElasticSearchRepository
 * @package Ferdyrurka\CommandBus\Repository
 */
class ElasticSearchRepository implements ElasticSearchRepositoryInterface, RepositoryInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $index;

    /**
     * ElasticSearchRepository constructor.
     * @param ElasticSearchManager $elasticSearchManager
     */
    public function __construct(ElasticSearchManager $elasticSearchManager)
    {
        $this->index = $elasticSearchManager->getIndex();
        $this->client = $elasticSearchManager->getManager();
    }

    /**
     * @param Log $warn
     */
    public function create(Log $warn): void
    {
        $this->client->create([
            'index' => $this->index,
            'type' => 'command-bus',
            'body' => [
                'exceptionTime' => $warn->getExceptionTime(),
                'exception' => $warn->getException(),
                'message' => $warn->getMessage(),
                'line' => $warn->getLine(),
                'handler' => $warn->getHandler(),
                'command' => $warn->getCommand()
            ]
        ]);
    }

}
