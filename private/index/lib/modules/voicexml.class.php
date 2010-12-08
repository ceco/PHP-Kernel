<?

    class voicexml {

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {
            global $HTTP_RAW_POST_DATA;

            $this->kernel = $kernel;

            if( $_SERVER["REQUEST_METHOD"] == "POST" ){
                if( preg_match("|<vxml( +)version=\"([\d\\.]+)\"|s", $HTTP_RAW_POST_DATA, $voiceXML )){
                    define("VOICEXML_VERSION", $voiceXML[2] );
                    $this->kernel->set_environment(ENVIRONMENT_VOICE);
                }
            }

        } // end __construct

    } // end class voicexml

?>