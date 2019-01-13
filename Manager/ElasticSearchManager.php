<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Manager;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Ferdyrurka\CommandBus\DependencyInjection\Database\ElasticSearchDatabase;
use Ferdyrurka\CommandBus\DependencyInjection\Parameters;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ElasticSearchManager
 * @package Ferdyrurka\CommandBus\Util
 */
class ElasticSearchManager implements ElasticSearchManagerInterface
{
    /**
     *
     */
    private const PREFIX = Parameters::PREFIX . '_' . ElasticSearchDatabase::DATABASE_NAME . '_';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ElasticSearchManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->client = $this->createClient();
    }

    /**
     * @return Client
     */
    public function getManager(): Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        $this->container->getParameter(self::PREFIX . 'index');
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
