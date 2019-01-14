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

use Ferdyrurka\CommandBus\Entity\Log;
use Ferdyrurka\CommandBus\Manager\ElasticSearchManager;

/**
 * Interface ElasticSearchRepositoryInterface
 * @package Ferdyrurka\CommandBus\Repository
 */
interface ElasticSearchRepositoryInterface
{
    /**
     * ElasticSearchRepositoryInterface constructor.
     * @param ElasticSearchManager $elasticSearchConnection
     */
    public function __construct(ElasticSearchManager $elasticSearchConnection);

    /**
     * @param Log $warn
     */
    public function create(Log $warn): void;
}
