<?php

namespace Liberta\Exception;

class AuthenticationException extends Exception
{
    public function __construct(string $message = 'Authentication failed', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}

class AuthorizationException extends Exception
{
    public function __construct(string $message = 'Insufficient permissions', ?\Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}

class TokenExpiredException extends AuthenticationException
{
    public function __construct(string $message = 'Token has expired', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}

class InvalidTokenException extends AuthenticationException
{
    public function __construct(string $message = 'Invalid token', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
