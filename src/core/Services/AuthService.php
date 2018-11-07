<?php
namespace Tendoo\Core\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Tendoo\Core\Models\Role;
use Tendoo\Core\Models\User;
use Tendoo\Core\Services\Users;
use Tendoo\Core\Services\Options;
use Tendoo\Core\Facades\Hook;
use Tendoo\Core\Mail\PasswordReset;
use Tendoo\Core\Mail\PasswordUpdated;
use Tendoo\Core\Mail\UserRegistrationMail;

class AuthService 
{
    public function register( Request $request )
    {
        $userService    =   app()->make( Users::class );
        $options        =   app()->make( Options::class );

        /**
         * Trigger Action before registering the users
         * @filter:before.register
         */
        $redirect           =   Hook::filter( 'before.register', false, $request, $options );

        /**
         * A hook can control the user registration
         */
        if ( $redirect instanceof RedirectResponse ) {
            return $redirect;
        }

        $shouldActivate     =   $options->get( 'validate_users', 'false' ) === 'true' ? true : false;

        /**
         * Create user instance
         */
        $user               =   new User;
        $user->username     =   $request->input( 'username' );
        $user->password     =   bcrypt( $request->input( 'password' ) );
        $user->email        =   $request->input( 'email' );
        $user->role_id      =   $options->get( 'register_as', 1 ); // default user
        $user->active       =   $shouldActivate ? 1 : 0;
        $user->save();

        /**
         * Save user options
         * before registration
         */
        $option             =   new Options( $user->id );
        $option->set( 'theme_class', 'red-theme' );

        /**
         * Trigger Hook for the user
         * @hook:register.user
         */
        Hook::action( 'register.user', $user, $option );

        if ( $shouldActivate ) {
            $userService->sendActivationEmail( $user );
        }

        /**
         * let's notify all admin with admin role a user has been registered
         * @todo adding a filter for role selected to receive an email
         */
        if ( $options->get( 'registration_notification' ) == 'yes' ) {
            foreach( Role::where( 'namespace', 'admin' )->first()->user as $admin ) {
                Mail::to( $admin->email )
                    ->queue( new UserRegistrationMail([
                        'link'  =>  route( 'dashboard.users.list' ),
                        'user'  =>  $user
                    ]));
            }
        }
    }
}