<?php

declare(strict_types = 1);

namespace App\Exceptions;

class FileUploadingException extends \Exception
{
    protected $message = 'Somthing is wrong with the file';
}