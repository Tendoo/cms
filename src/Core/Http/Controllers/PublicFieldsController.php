<?php
namespace Tendoo\Core\Http\Controllers;

use Tendoo\Core\Services\Field;
use Tendoo\Core\Exceptions\CoreException;
use Tendoo\Core\Services\Modules;
use Tendoo\Core\Facades\Hook;

class PublicFieldsController extends BaseController
{
    public function getFields( $namespace )  
    {
        if ( empty( $fields     =   Hook::filter( 'public.fields', [], $namespace ) ) ) {
            /**
             * if nothing is returned, then the fields probably
             * doesn't exists.
             */
            throw new CoreException([
                'status'    =>  'failed',
                'message'   =>  __( 'Unable to get the fields with the provided namespace: ' . $namespace )
            ]);
        };

        return $fields;
    }
}