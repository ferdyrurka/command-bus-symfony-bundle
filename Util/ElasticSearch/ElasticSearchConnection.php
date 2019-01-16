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

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ElasticSearchConnection
 * @package Ferdyrurka\CommandBus\Util\ElasticSearch
 */
class ElasticSearchConnection implements ElasticSearchConnectionInterface
{
    /**
     *
     */
    protected const PREFIX = Parameters::PREFIX . '_' . ElasticSearchDatabase::DATABASE_NAME . '_';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $index;

    /**
     * @var Client
     */
    protected $client;

    /**
     * ElasticSearchConnection constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        if (empty($this->index)) {
            $this->index = $this->createIndex();
        }

        return $this->index;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if (!$this->client instanceof Client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    /**
     * @return string
     */
    protected function createIndex(): string
    {
        return $this->container->getParameter(self::PREFIX . 'index');
    }

    /**
     * @return Client
     */
    protected function createClient(): Client
    {
        $hosts = [
            'host' => $this->container->getParameter(self::PREFIX . 'host'),
            'port' => $this->container->getParameter(self::PREFIX . 'port'),
            'scheme' => $this->container->getParameter(self::PREFIX . 'scheme')
        ];

        if (
            $this->container->hasParameter(self::PREFIX . 'user') &&
            $this->container->hasParameter(self::PREFIX . 'pass')
        ) {
            $hosts['user'] = $this->container->getParameter(self::PREFIX . 'user');
            $hosts['pass'] = $this->container->getParameter(self::PREFIX . 'pass');
        }

        return ClientBuilder::create()
            ->setHosts($hosts)
            ->setRetries(2)
            ->build();
    }
}
