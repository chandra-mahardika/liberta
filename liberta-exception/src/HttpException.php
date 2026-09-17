<?php

namespace Liberta\Exception;

class HttpException extends Exception
{
    public function __construct(
        string $message = 'HTTP Error',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Not Found', ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}

class BadRequestException extends HttpException
{
    public function __construct(string $message = 'Bad Request', ?\Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = 'Unauthorized', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}

class ForbiddenException extends HttpException
{
    public function __construct(string $message = 'Forbidden', ?\Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}

class MethodNotAllowedException extends HttpException
{
    public function __construct(string $message = 'Method Not Allowed', ?\Throwable $previous = null)
    {
        parent::__construct($message, 405, $previous);
    }
}

class TooManyRequestsException extends HttpException
{
    public function __construct(string $message = 'Too Many Requests', ?\Throwable $previous = null)
    {
        parent::__construct($message, 429, $previous);
    }
}

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = 'Internal Server Error', ?\Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
