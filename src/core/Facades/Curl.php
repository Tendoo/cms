<?php
namespace CloudBreeze\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Curl extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'tendoo.curl';
    }
}