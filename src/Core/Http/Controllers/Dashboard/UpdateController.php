<?php

namespace Tendoo\Core\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Tendoo\Core\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Event;

class UpdateController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware( function( $request, $next ) {
            return $next( $request );
        });
    }
}
