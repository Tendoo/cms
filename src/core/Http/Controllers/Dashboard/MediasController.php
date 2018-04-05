<?php
namespace Tendoo\Core\Http\Controllers\dashboard;

use Tendoo\Core\Http\Controllers\TendooController;
use Tendoo\Core\Services\Page;
use Illuminate\Http\Request;
use tendoo\Core\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Tendoo\Core\Services\Date;
use Tendoo\Core\Services\MediaService;

class MediasController extends TendooController
{
    /**
     * Media Service
     * @var service
     */
    private $service;

    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all media on the system
     */
    public function list()
    {
        Page::setTitle( 'Media' );

        /**
         * Define used tabs
         */
        $tabs   =   [
            [
                'namespace' =>  'list',
                'text'      =>  __( 'Media' ),
                'active'    =>  true
            ],
            [
                'namespace' =>  'upload',
                'text'      =>  __( 'Upload' ),
                'active'    =>  false
            ]
        ];

        /**
         * define upload url
         */
        $uploadUrl  =   route( 'dashboard.medias.upload' );
        
        return view( 'tendoo::components.backend.media', compact( 'tabs', 'uploadUrl' ) );
    }

    /**
     * handle uploaded file
     * @param void
     */
    public function upload( Request $request )
    {
        /**
         * Supported file extension
         * @var array<String>
         */
        $extensions     =   [ 'jpeg', 'png'  ];

        /**
         * Launching the media Service
         */
        $this->mediaService  =   new MediaService( compact( 'extensions' ) );

        /**
         * uploading a file
         */
        $file = $request->file('file');
        $this->mediaService->upload( $file );
    }
}