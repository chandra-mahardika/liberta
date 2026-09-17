<?php

namespace Liberta\WebSocket;

interface MessageHandler
{
    public function onOpen(Connection $connection): void;
    public function onMessage(Connection $connection, string $message): void;
    public function onClose(Connection $connection): void;
    public function onError(Connection $connection, \Throwable $e): void;
}
