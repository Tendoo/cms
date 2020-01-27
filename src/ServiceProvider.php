<?php
namespace Tendoo;

if ( ! defined( '_SLASH_' ) ) {
    define( '_SLASH_', DIRECTORY_SEPARATOR );
}

define( 'TENDOO_ROOT', __DIR__ );

/**
 * Updating this will force 
 * assets and database migration
 */
define( 'TENDOO_VERSION', '5.0' );
define( 'TENDOO_ASSETS_VERSION', '1.8.1' );
define( 'TENDOO_DB_VERSION', '1.12' );

require_once TENDOO_ROOT . '/core/Services/Helper.php';
require_once TENDOO_ROOT . '/core/Services/HelperFunctions.php';

use Illuminate\Http\Request;

use Tendoo\Core\Models\Role;
use Illuminate\Routing\Router;
use Tendoo\Core\Http\TendooKernel;
use Illuminate\Support\Facades\URL;
use Jackiedo\DotenvEditor\DotenvEditor;
use Tendoo\Core\Observers\RoleObserver;
use Tendoo\Core\Console\Commands\OptionGet;
use Tendoo\Core\Console\Commands\OptionSet;
use Orchestra\Parser\Xml\Reader as XmlReader;
use Tendoo\Core\Console\Commands\EnableModule;
use Tendoo\Core\Console\Commands\ModuleModels;
use Tendoo\Core\Console\Commands\OptionDelete;
use Tendoo\Core\Console\Commands\ResetCommand;
use Tendoo\Core\Console\Commands\DisableModule;
use Tendoo\Core\Console\Commands\GenerateModule;
use Tendoo\Core\Console\Commands\PublishCommand;
use Tendoo\Core\Console\Commands\RefreshCommand;
use Orchestra\Parser\Xml\Document as XmlDocument;

use Tendoo\Core\Console\Commands\ModuleController;
use Tendoo\Core\Console\Commands\ModuleMigrations;
use Tendoo\Core\Http\Middleware\SafeURLMiddleware;
use Tendoo\Core\Console\Commands\EnvEditorGetCommand;

use Tendoo\Core\Console\Commands\EnvEditorSetCommand;
use Tendoo\Core\Console\Commands\ModuleSymlinkCommand;

use Tendoo\Core\Console\Commands\MakeModuleServiceProvider;
use Tendoo\Core\Console\Commands\ModuleCrudGeneratorCommand;
use Tendoo\Core\Console\Commands\DeleteExpiredOptionsCommand;
use Illuminate\Support\ServiceProvider as CoreServiceProvider;

class ServiceProvider extends CoreServiceProvider
{
    protected $defer    =   false;
    
    /**
     * boot method
     */
    public function boot( Router $router )
    {        
        if ( Request::isSecure() ) {
            URL::forceSchema('https');
        }

        /**
         * Register DotEnv Editor
         */
        $this->app->bind( 'dotenv-editor', 'Jackiedo\DotenvEditor\DotenvEditor');
        $this->app->bind( 'tendoo.doteditor', 'Jackiedo\DotenvEditor\DotenvEditor');
        $this->app->bind( 'tendoo.hook', 'TorMorten\Eventy\Events');
    
        /**
         * register CURL
         */
        $this->app->bind( 'tendoo.curl', 'Ixudra\Curl\CurlService' );
        $this->app->bind( 'tendoo.page', 'Tendoo\Core\Services\Page' );
        $this->app->bind( 'tendoo.helper', 'Tendoo\Core\Services\Helper' );
        $this->app->bind( 'tendoo.field', 'Tendoo\Core\Services\Field' );
        $this->app->bind( 'tendoo.modules', 'Tendoo\Core\Services\Modules' );
        
        /**
         * Register the route provider 
         * before the Laravel Route Provider
         */
        $this->app->register( \Barryvdh\Cors\ServiceProvider::class );
        $this->app->register( \Tendoo\Core\Providers\TendooAppServiceProvider::class );
        $this->app->register( \Tendoo\Core\Providers\TendooEventServiceProvider::class );
        $this->app->register( \Tendoo\Core\Providers\TendooModulesServiceProvider::class );
        $this->app->register( \Tendoo\Core\Providers\TendooUserOptionsServiceProvider::class );
        $this->app->register( \Tendoo\Core\Providers\TendooRouteServiceProvider::class );
        $this->app->register( 'TorMorten\Eventy\EventServiceProvider' );
        $this->app->register( 'TorMorten\Eventy\EventBladeServiceProvider' );

        /**
         * Register Middleware
         */
        $router->aliasMiddleware( 'app.installed', \Tendoo\Core\Http\Middleware\AppInstalled::class );
        $router->aliasMiddleware( 'app.notInstalled', \Tendoo\Core\Http\Middleware\AppNotInstalled::class );
        $router->aliasMiddleware( 'expect.unlogged', \Tendoo\Core\Http\Middleware\RedirectIfAuthenticated::class );
        $router->aliasMiddleware( 'expect.logged', \Tendoo\Core\Http\Middleware\RedirectIfNotAuthenticated::class ); 
        $router->aliasMiddleware( 'can.register', \Tendoo\Core\Http\Middleware\CheckRegistrationStatus::class );
        $router->aliasMiddleware( 'check.migrations', \Tendoo\Core\Http\Middleware\CheckMigrations::class );
        $router->aliasMiddleware( 'tendoo.guard', \Tendoo\Core\Http\Middleware\LoadApi::class );
        $router->aliasMiddleware( 'tendoo.auth', \Tendoo\Core\Http\Middleware\TendooAuth::class );
        $router->aliasMiddleware( 'tendoo.silent-auth', \Tendoo\Core\Http\Middleware\TendooSilentAuth::class );
        $router->aliasMiddleware( 'tendoo.guest', \Tendoo\Core\Http\Middleware\TendooGuest::class );
        $router->aliasMiddleware( 'tendoo.cors', \Barryvdh\Cors\HandleCors::class );   
        $router->aliasMiddleware( 'tendoo.prevent.not-installed', \Tendoo\Core\Http\Middleware\AppInstalled::class );
        $router->aliasMiddleware( 'tendoo.prevent.installed', \Tendoo\Core\Http\Middleware\AppNotInstalled::class );
        $router->aliasMiddleware( 'tendoo.prevent.flood', \Tendoo\Core\Http\Middleware\PreventFloodRequest::class );   
        $router->aliasMiddleware( 'tendoo.safe-url', SafeURLMiddleware::class );   
        
        $corePath       =   base_path() . _SLASH_ . 'core' . _SLASH_ ;
        $configPath     =   base_path() . _SLASH_ . 'config' . _SLASH_ ;
        $publicPath     =   base_path() . _SLASH_ . 'public' . _SLASH_ . 'tendoo';
        $servicePath    =   $corePath . 'Services';

        /**
         * we don't need to be running the console
         * to have access to theses features
         */
        $this->commands([
            DisableModule::class,
            EnableModule::class,
            GenerateModule::class,
            ModuleController::class,
            ModuleMigrations::class,
            ModuleModels::class,
            ModuleSymlinkCommand::class,
            OptionDelete::class,
            OptionSet::class,
            OptionGet::class,
            RefreshCommand::class,
            ResetCommand::class,
            MakeModuleServiceProvider::class,
            EnvEditorSetCommand::class,
            EnvEditorGetCommand::class,
            ModuleCrudGeneratorCommand::class,
            PublishCommand::class,
            DeleteExpiredOptionsCommand::class,
        ]);

        /**
         * Load Migrations
         */
        $this->loadMigrationsFrom( __DIR__ . '/database/migrations');

        /**
         * Publish Views
         */
        $this->loadViewsFrom( __DIR__ . '/resources/views', 'tendoo' );
        
        $this->publishes([
            __DIR__ . '/config/tendoo.php'   =>  $configPath . '/tendoo.php'
        ], 'tendoo-config' );
        
        /**
         * register observer for 
         * role model
         */
        Role::observe( RoleObserver::class );
    }

    /**
     * Register Stuff
     */
    public function register()
    {
        /**
         * database update location path
         * @var constant
         */
        if ( ! defined( 'DATABASE_UPDATES_PATH' ) ): define( 'DATABASE_UPDATES_PATH', dirname( __FILE__ ) . '/database/updates/' ); endif;
        if ( ! defined( 'DATABASE_MIGRATIONS_PATH' ) ): define( 'DATABASE_MIGRATIONS_PATH', dirname( __FILE__ ) . '/database/migrations/' ); endif;
        if ( ! defined( 'TENDOO_CONFIG_PATH' ) ): define( 'TENDOO_CONFIG_PATH', dirname( __FILE__ ) . '/config/' ); endif;
        if ( ! defined( 'TENDOO_ASSETS_PATH' ) ): define( 'TENDOO_ASSETS_PATH', dirname( __FILE__ ) . '/public/' ); endif;
        if ( ! defined( 'TENDOO_DIST_PATH' ) ): define( 'TENDOO_DIST_PATH', dirname( __FILE__ ) . '/public/dist/' ); endif;
        if ( ! defined( 'TENDOO_ROOT_PATH' ) ): define( 'TENDOO_ROOT_PATH', dirname( __FILE__ ) ); endif;
        if ( ! defined( 'TENDOO_ROUTES_PATH' ) ): define( 'TENDOO_ROUTES_PATH', TENDOO_ROOT_PATH . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR ); endif;
        if ( ! defined( 'TENDOO_MODULES_PATH' ) ): define( 'TENDOO_MODULES_PATH', base_path() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR ); endif;

        /**
         * Define Storage Location Path
         */
        include_once( dirname( __FILE__ ) . '/paths.php' );

        $this->app->singleton( 'XmlParser', function ($app) {
            return new XmlReader(new XmlDocument($app));
        });

        // $this->app->singleton(
        //     \App\Http\Kernel::class,
        //     TendooKernel::class
        // );

        // $this->app->router->prependMiddleware( SafeURLMiddleware::class );
        // app()->make( \Illuminate\Foundation\Http\Kernel::class )->prependMiddleware( SafeURLMiddleware::class );

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Tendoo\Core\Exceptions\TendooHandler::class
        );
    }

    /**
     * Provide Tendoo Instance
     */
    public function provides()
    {
        return [ 'tendoo' ];
    }
}  