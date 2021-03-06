<?php
namespace Tendoo\Core\Exceptions;

use Exception;

class CoreException extends Exception
{
    public function __construct( $asyncResponse )
    {
        $this->message  =   $asyncResponse[ 'message' ];
    }
}