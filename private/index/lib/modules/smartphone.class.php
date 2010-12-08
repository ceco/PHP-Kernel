<?

    /**
    * Kernel module smartphone
    * @name smartphone
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @date 06/03/2010
    *
    */

    class smartphone {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;
            if( stristr($_SERVER["HTTP_USER_AGENT"],"symbian os") or
                stristr($_SERVER["HTTP_USER_AGENT"],"midp")   
              ) {
                $this->kernel->set_environemnt(ENVIRONMENT_WWW_SMART);
                // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
                header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
                header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
            }
        } // end __construct

    } // end class smartphone

?>