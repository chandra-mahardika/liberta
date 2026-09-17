<?php

declare(strict_types=1);

namespace Liberta\Mail\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Mail\Message;

class MailTest extends TestCase
{
    public function testMessageBuilder(): void
    {
        $message = new Message();
        $message->from('admin@example.com', 'Admin')
                ->to('user@example.com', 'User')
                ->subject('Test Subject')
                ->body('Hello World')
                ->html('<h1>Hello World</h1>');

        $this->assertEquals('Admin <admin@example.com>', $message->getFrom());
        $this->assertEquals('User <user@example.com>', $message->getTo());
        $this->assertEquals('Test Subject', $message->getSubject());
        $this->assertEquals('Hello World', $message->getBody());
        $this->assertEquals('<h1>Hello World</h1>', $message->getHtmlBody());
    }

    public function testMessageAttachment(): void
    {
        $message = new Message();
        $message->attach('/tmp/test.txt', 'test.txt');

        $attachments = $message->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals('test.txt', $attachments[0]['name']);
    }

    public function testMessageHeader(): void
    {
        $message = new Message();
        $message->header('X-Custom', 'value');

        $this->assertEquals(['X-Custom' => 'value'], $message->getHeaders());
    }
}
