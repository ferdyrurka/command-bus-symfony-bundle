<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus;

use Ferdyrurka\CommandBus\DependencyInjection\CommandBusSymfonyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CommandBusSymfonyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): CommandBusSymfonyExtension
    {
        return new CommandBusSymfonyExtension();
    }
}
