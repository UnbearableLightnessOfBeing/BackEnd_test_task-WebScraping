<?php

declare(strict_types = 1);

namespace App\Exceptions;

class NoPostsException extends \Exception
{
    protected $message = 'There is no posts here';
}