<?php
namespace Tendoo\Core\Services\Dashboard;

use Tendoo\Core\Services\Menus;

class MenusConfig extends Menus
{
    private $user;

    public function __construct()
    {
        parent::__construct();

        $this->user             =   app()->make( \Tendoo\Core\Services\Users::class );

        $dashboard              =   new \stdClass;
        $dashboard->text        =   __( 'Dashboard' );
        $dashboard->href        =   '/dashboard';
        $dashboard->label       =   10;
        $dashboard->namespace   =   'dashboard';
        $dashboard->icon        =   'dashboard';

        $this->add( $dashboard );

        $this->registerMediaMenu();
        $this->registerModulesMenu();
        $this->registerSettingsMenu();
        $this->registerUserMenu();
        $this->registerApplicationsMenu();
    }

    /**
     * register media menu
     * @return void
     */
    public function registerMediaMenu()
    {
        $user       =   app()->make( \Tendoo\Core\Services\Users::class );

        if ( $user->is([ 'admin', 'supervisor' ] ) ) {
            $media              =   new \stdClass;
            $media->text        =   __( 'Media' );
            $media->namespace   =   'media';
            $media->icon        =   'collections';
            $media->href        =   '/dashboard/medias/page/1';

            $this->add( $media );
        }
    }

    /**
     * Register User menu
     * @return void
     */
    public function registerUserMenu()
    {
        $user       =   app()->make( \Tendoo\Core\Services\Users::class );

        if ( $user->is([ 'admin' ] ) ) {
            $users                  =   new \stdClass;
            $users->text            =   __( 'Users' );
            $users->label           =   10;
            $users->namespace       =   'users';
            $users->icon            =   'people';

            $this->add( $users );
            
            $list                  =   new \stdClass;
            $list->text            =   __( 'List of users' );
            $list->href            =   '/dashboard/crud/tendoo-users';
            $list->label           =   10;
            $list->namespace       =   'users.list';
            
            $create                  =   new \stdClass;
            $create->text            =   __( 'Create a new user' );
            $create->href            =   '/dashboard/crud/tendoo-users/create';
            $create->label           =   10;
            $create->namespace       =   'users.create';

            $this->addTo( 'users', [ $list, $create ]);
        }
    }

    /**
     * Register Settings Menu
     * @return void
     */
    public function registerSettingsMenu()
    {
        $user       =   app()->make( \Tendoo\Core\Services\Users::class );

        if ( $user->is([ 'admin', 'supervisor' ] ) ) {
            $settings               =   new \stdClass;
            $settings->text         =   __( 'Settings' );
            $settings->label        =   10;
            $settings->namespace    =   'settings';
            $settings->icon         =   'settings';

            $this->add( $settings );

            $general               =   new \stdClass;
            $general->text         =   __( 'General' );
            $general->label        =   10;
            $general->namespace    =   'settings.general';
            $general->href         =   '/dashboard/settings/application?tab=dashboard.settings.general';

            $registration                   =   new \stdClass;
            $registration->text             =   __( 'Registration' );
            $registration->label            =   0;
            $registration->namespace        =   'settings.registration';
            $registration->href             =   '/dashboard/settings/application?tab=dashboard.settings.registration';

            $email                   =   new \stdClass;
            $email->text             =   __( 'Mail' );
            $email->label            =   0;
            $email->namespace        =   'settings.email';
            $email->href             =   '/dashboard/settings/application?tab=dashboard.settings.email';
            
            $recaptcha                   =   new \stdClass;
            $recaptcha->text             =   __( 'reCaptcha' );
            $recaptcha->label            =   0;
            $recaptcha->namespace        =   'settings.email';
            $recaptcha->href             =   '/dashboard/settings/application?tab=dashboard.settings.recaptcha';

            $this->addTo( 'settings', [ $general, $registration, $email, $recaptcha ]);
        }
    }

    /**
     * Register Module Menus
     * @return void
     */
    public function registerModulesMenu()
    {
        $user       =   app()->make( \Tendoo\Core\Services\Users::class );

        if ( $user->is([ 'admin', 'supervisor' ] ) ) {
            $modules                =   new \stdClass;
            $modules->text          =   __( 'Modules' );
            $modules->label         =   10;
            $modules->namespace     =   'modules';
            $modules->icon          =   'input';

            $this->add( $modules );

            $list                =   new \stdClass;
            $list->text          =   __( 'List of modules' );
            $list->href          =   '/dashboard/modules';
            $list->label         =   10;
            $list->namespace     =   'modules.list';

            $upload                =   new \stdClass;
            $upload->text          =   __( 'Upload' );
            $upload->href          =   '/dashboard/modules/upload';
            $upload->label         =   10;
            $upload->namespace     =   'modules.upload';

            $this->addTo( 'modules', [ $list, $upload ]);
        }
    }

    /**
     * An interface where user can register 
     * an application for Oauth purposes
     */
    public function registerApplicationsMenu()
    {
        $user       =   app()->make( \Tendoo\Core\Services\Users::class );

        if ( $user->is([ 'admin', 'supervisor' ] ) ) {
            $applications                =   new \stdClass;
            $applications->text          =   __( 'Applications' );
            $applications->label         =   10;
            $applications->namespace     =   'tendoo-apps';
            $applications->icon          =   'apps';
    
            $this->add( $applications );
    
            $applicationList                =   new \stdClass;
            $applicationList->text          =   __( 'All applications' );
            $applicationList->href          =   '/dashboard/crud/tendoo-apps';
            $applicationList->label         =   10;
            $applicationList->namespace     =   'tendoo-apps.list';
    
            $applicationRegister                =   new \stdClass;
            $applicationRegister->text          =   __( 'Register a new' );
            $applicationRegister->href          =   '/dashboard/crud/tendoo-apps/create';
            $applicationRegister->label         =   10;
            $applicationRegister->namespace     =   'tendoo-apps.create';
    
            $this->addTo( 'tendoo-apps', [ $applicationList, $applicationRegister ]);
        }
    }
}