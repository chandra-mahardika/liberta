<?php

declare(strict_types=1);

namespace Liberta\Container\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Container\Container;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testBindAndResolve(): void
    {
        $this->container->bind('foo', fn() => 'bar');
        $this->container->bind('counter', fn() => new class {
            public int $value = 0;
        });

        $this->assertEquals('bar', $this->container->resolve('foo'));
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $this->container->singleton('db', fn() => new class {
            public int $id = 1;
        });

        $a = $this->container->resolve('db');
        $b = $this->container->resolve('db');

        $this->assertSame($a, $b);
    }

    public function testInstanceReturnsSameObject(): void
    {
        $obj = new \stdClass();
        $obj->name = 'test';

        $this->container->instance('obj', $obj);

        $this->assertSame($obj, $this->container->resolve('obj'));
        $this->assertEquals('test', $this->container->resolve('obj')->name);
    }

    public function testAutoConstructViaReflection(): void
    {
        $this->container->bind(StubService::class, fn() => new StubService(new StubDependency()));
        $this->container->bind(StubDependency::class, fn() => new StubDependency());

        $result = $this->container->make(StubService::class);

        $this->assertInstanceOf(StubService::class, $result);
    }

    public function testResolveNonExistentThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->container->resolve('nonexistent');
    }
}

class StubDependency {}
class StubService {
    public function __construct(public StubDependency $dep) {}
}
