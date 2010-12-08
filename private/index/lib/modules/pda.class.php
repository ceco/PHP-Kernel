<?

    /**
    * Kernel module pda
    * @name pda
    * @author
    * @date
    *
    */

    class pda {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;

            if( stristr(strtolower($_SERVER["HTTP_UA_OS"]),"pocket pc") or
                stristr(strtolower($_SERVER["HTTP_USER_AGENT"]),"armv") or
                stristr(strtolower($_SERVER["HTTP_USER_AGENT"]),"iphone") 
              ) {
                $this->kernel->set_environemnt(ENVIRONMENT_WWW_PDA);
                // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
                header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
                header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
                }

        } // end __construct

    } // end class pda

?>