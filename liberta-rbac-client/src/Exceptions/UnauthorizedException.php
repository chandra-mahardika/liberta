<?php

namespace Liberta\Rbac\Exceptions;

class UnauthorizedException extends \RuntimeException
{
    protected $code = 401;
}
