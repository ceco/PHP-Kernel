<?

    /**
    * Kernel module for wap environment
    * @name wap
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 06/03/2010
    *
    */

    class wap {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;
            if( stristr( $_SERVER["HTTP_ACCEPT"], 'vnd.wap.wml' ) or
                stristr( $_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml' ) or
                isset  ( $_SERVER['HTTP_X_WAP_PROFILE'] ) or
                stristr( $_SERVER['HTTP_USER_AGENT'], 'wap' )
              ) {
                $this->kernel->set_environemnt(ENVIRONMENT_WWW_WAP);
                // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
                header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
                header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
             }

        } // end __construct

    } // end class wap

?>