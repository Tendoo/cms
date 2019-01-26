<?php

namespace Tendoo\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Tendoo\Core\Services\Menus;
use Tendoo\Core\Services\Dashboard\MenusConfig;
use Tendoo\Core\Facades\Hook;
use Tendoo\Core\Services\Helper;
use Tendoo\Core\Services\Modules;
use Tendoo\Core\Models\User;
use Tendoo\Core\Services\Page;
use Tendoo\Core\Services\Options;
use Tendoo\Core\Services\DateService;
use Tendoo\Core\Services\UserOptions;
use Tendoo\Core\Services\Users;
use Tendoo\Core\Exceptions\AccessDeniedException;
use Tendoo\Core\Exceptions\RoleDeniedException;


class DashboardController extends BaseController
{
    protected $menus;

    public function __construct()
    {
        parent::__construct();

        // register dashboard menu singleton
        app()->singleton( 'Tendoo\Core\Services\Dashboard\MenusConfig', function( $app ) {
            return new MenusConfig();
        });

        $this->middleware( function( $request, $next ){

            /**
             * Registering stuff from middleware
             */
            $this->menus        =   app()->make( MenusConfig::class );

            return $next($request);
        });
    }
}
