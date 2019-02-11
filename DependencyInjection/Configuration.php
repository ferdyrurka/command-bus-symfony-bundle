<?php
/**
 * Copyright (c) 2018-2018 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ferdyrurka\CommandBus\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root(Parameters::PREFIX);

        $rootNode
            ->children()

                ->scalarNode('handler_prefix')
                    ->defaultValue('Handler')
                    ->end()

                ->scalarNode('command_prefix')
                    ->defaultValue('Command')
                    ->end()


                ->scalarNode('query_handler_prefix')
                    ->defaultValue('Handler')
                    ->end()

                ->scalarNode('query_command_prefix')
                    ->defaultValue('Command')
                    ->end()

                ->booleanNode('save_command_bus_log')
                    ->defaultTrue()
                    ->end()

                ->booleanNode('save_query_bus_log')
                    ->defaultTrue()
                    ->end()

                ->booleanNode('save_query_bus_info')
                    ->defaultTrue()
                    ->end()

                ->scalarNode('database_type')
                    ->validate()
                        ->ifInArray(['elasticsearch'])
                    ->end()
                ->end()

                ->arrayNode('connection')

                    ->children()

                        ->arrayNode('elasticsearch')

                            ->children()

                                ->scalarNode('host')
                                    ->defaultValue('elasticsearch')
                                    ->end()
                                ->integerNode('port')
                                    ->defaultValue('9200')
                                    ->end()
                                ->scalarNode('scheme')
                                    ->validate()
                                        ->ifInArray(['http', 'https'])
                                    ->end()
                                    ->defaultValue('https')
                                    ->end()
                                ->scalarNode('index')
                                    ->end()
                                ->scalarNode('user')
                                    ->defaultNull()
                                    ->end()
                                ->scalarNode('pass')
                                    ->defaultNull()
                                    ->end()

                            ->end()

                    ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
