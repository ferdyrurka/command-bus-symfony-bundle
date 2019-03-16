<?php
/**
 * Copyright (c) 2018-2019 Łukasz Staniszewski <kontakt@lukaszstaniszewski.pl>
 *
 * For the full copyright and license information, please view the
 * https://github.com/ferdyrurka/command-bus-symfony-bundle/blob/master/LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Ferdyrurka\CommandBus\Test\Util;

use Ferdyrurka\CommandBus\Util\NamespaceParser;
use PHPUnit\Framework\TestCase;

/**
 * Class NamespaceParserTest
 * @package Ferdyrurka\CommandBus\Test\Util
 */
class NamespaceParserTest extends TestCase
{
    /**
     * @var NamespaceParser
     */
    private $namespaceParser;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->namespaceParser = new NamespaceParser('Command\\CreateCommand', 'Command', 'Handler');
    }


    /**
     *
     */
    public function testGetHandlerNamespaceByNameClass(): void
    {
        $this->assertEquals(
            'Command\\CreateHandler',
            $this->namespaceParser->getHandlerNamespaceByNameClass()
        );
    }

    /**
     *
     */
    public function testGetHandlerNamespaceByCommandNamespace(): void
    {
        $this->assertEquals(
            'Handler\\CreateHandler',
            $this->namespaceParser->getHandlerNamespaceByCommandNamespace()
        );
    }
}

