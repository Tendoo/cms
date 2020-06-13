<?php
namespace CloudBreeze\Core\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudBreeze\Core\Models\Option;

class Oauth
{
    /**
     * Get Scopes
     * @return array of scopes
     */
    public function getScopes()
    {
        return [
            'profile'   =>  [
                'title'         =>  __( 'Read the Profile' ),
                'description'   =>  __( 'Will grant access to the profile details.' )
            ]
        ];
    }
}