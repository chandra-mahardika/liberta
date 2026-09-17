<?php

declare(strict_types=1);

namespace Liberta\Sse\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Sse\Event;

class SseTest extends TestCase
{
    public function testEventToString(): void
    {
        $event = (new Event())->id('1')->event('message')->data('hello');

        $output = (string) $event;

        $this->assertStringContainsString('id: 1', $output);
        $this->assertStringContainsString('event: message', $output);
        $this->assertStringContainsString('data: hello', $output);
    }

    public function testEventJson(): void
    {
        $event = (new Event())->json(['foo' => 'bar']);

        $output = (string) $event;

        $this->assertStringContainsString('data: {"foo":"bar"}', $output);
    }

    public function testEventRetry(): void
    {
        $event = (new Event())->retry(5000);

        $output = (string) $event;

        $this->assertStringContainsString('retry: 5000', $output);
    }

    public function testEventComment(): void
    {
        $event = (new Event())->data('line1');

        $output = (string) $event;

        $this->assertStringContainsString('data: line1', $output);
    }
}
