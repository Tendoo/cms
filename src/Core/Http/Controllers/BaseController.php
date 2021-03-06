<?php
namespace Tendoo\Core\Http\Controllers;

use Tendoo\Core\Services\Helper;
use Tendoo\Core\Services\Modules;
use Tendoo\Core\Models\User;
use Tendoo\Core\Services\Page;
use Tendoo\Core\Services\Options;
use Tendoo\Core\Services\DateService;
use Tendoo\Core\Services\UserOptions;
use Tendoo\Core\Services\Users;
use Tendoo\Core\Exceptions\FeatureDisabledException;
use Tendoo\Core\Exceptions\AccessDeniedException;
use Tendoo\Core\Exceptions\RoleDeniedException;

use Illuminate\Support\Facades\Event;

class BaseController extends Controller
{
    protected $options;
    protected $userOptions;
    protected $modules;
    protected $menus;

    public function __construct()
    {        
        if ( Helper::AppIsInstalled() ) {
            $this->middleware( function( $request, $next ){

                /**
                 * Registering stuff from middleware
                 */
                $this->options      =   app()->make( Options::class );
                $this->userOptions  =   app()->make( UserOptions::class );
                $this->modules      =   app()->make( Modules::class );
                $this->date         =   app()->make( DateService::class );
                $this->userService  =   app()->make( Users::class );

                return $next($request);
            });
        }
    }

    /**
     * Check permission
     */
    public function checkFeature( $option, $textValue = null )
    {
        if ( $this->options->get( $option ) === null && $textValue == null ) {
            throw new FeatureDisabledException();
        } else if ( $this->options->get( $option ) !== $textValue ) {
            throw new FeatureDisabledException();
        }
    }

    /**
     * Check permission
     */
    public function checkPermission( $permission )
    {
        if ( ! User::allowedTo( $permission ) ) {
            throw new AccessDeniedException( sprintf( 
                __( 'You don\'t have access to that page. Your role doesn\'t have the required permission %s' ),
                $permission
            ) );
        }
    }

    /**
     * Check role
     */
    public function checkRoles( array $roles )
    {
        if ( ! $this->userService->is( $roles ) ) {
            throw new RoleDeniedException( $roles );
        }
    }
}