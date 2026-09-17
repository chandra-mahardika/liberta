<?php

namespace Liberta\Rbac\Exceptions;

class ForbiddenException extends \RuntimeException
{
    protected $code = 403;
}