<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Connection\ElasticSearch;

use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface ElasticSearchConnectionInterface
 * @package Ferdyrurka\CommandBus\Util\ElasticSearch
 */
interface ElasticSearchConnectionInterface
{
    /**
     * ElasticSearchConnectionInterface constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @return string
     */
    public function getIndex(): string;

    /**
     * @return Client
     */
    public function getClient(): Client;
}
