<?php
use Illuminate\Http\Request;

/**
 * This route is used to proceed to an authentication
 * of the user using Application Credentials and the username, password.
 */
Route::prefix( 'api' )->group( function() {
    Route::middleware([ 'tendoo.cors', 'tendoo.prevent.not-installed', 'tendoo.prevent.flood' ])->group( function() {
        include_once( dirname( __FILE__ ) . '/api-routes/auth.php' );
        Route::middleware([ 'tendoo.silent-auth' ])->group( function() {
            include_once( dirname( __FILE__ ) . '/api-routes/options.php' );
            include_once( dirname( __FILE__ ) . '/api-routes/public-forms.php' );
        });
    });
    
    Route::middleware([ 'tendoo.cors', 'tendoo.prevent.installed', 'tendoo.prevent.flood' ])->group( function() {
        include_once( dirname( __FILE__ ) . '/api-routes/setup.php' );
    });
    
    Route::middleware([ 'tendoo.cors', 'tendoo.prevent.not-installed', 'tendoo.prevent.flood', 'tendoo.auth' ])->group( function() {    
        include_once( dirname( __FILE__ ) . '/api-routes/users.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/applications.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/crud.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/link.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/forms.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/modules.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/table.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/tabs.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/menus.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/medias.php' );
        include_once( dirname( __FILE__ ) . '/api-routes/settings.php' );
    });
    
    Route::middleware([ 'check.migrations' ])->group( function() {
        Route::get( 'tendoo/ping', 'HomeController@ping' );
    });

    Route::middleware([ 'tendoo.cors' ])->group( function() {
        Route::get( 'tendoo/update/database', 'UpdateController@postUpdate' );
        Route::get( 'tendoo/update/assets', 'UpdateController@postFiles' );
        Route::post( 'tendoo/modules/{namespace}/download', 'HomeController@extractModule' )
            ->name( 'tendoo.modules.download' );
    });
});
