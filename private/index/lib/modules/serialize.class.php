<?

    /**
    * Kernel module template
    * @name module_template
    * @author
    * @date
    *
    */

    class serialize {

        public $kernel;

        public function __construct( $kernel ) {

            $this->kernel = $kernel;
            $headers = $this->get_all_headers();
            if( $headers[ENVIRONMENT_SERIALIZE] )
                $this->kernel->set_environment(ENVIRONMENT_SERIALIZE);

        } // end __construct

        /**
        * Get all http headers and server variables
        *
        * @name get_all_headers
        *
        * @return array headers (lowercase separated with -)
        *
        */
        public function get_all_headers() {
            $headers = array();
            foreach ($_SERVER as $key => $val){
                if (substr($key, 0, 5) == "HTTP_"){
                    $key = str_replace('_', ' ', substr($key, 5));
                    $key = str_replace(' ', '-', strtolower($key));
                    $headers[$key] = $val;
                } else
                    $headers[strtolower(str_replace('_', '-', $key))] = $val;
            } // end foreach
            return $headers;
        } // end get_all_headers

    } // end class serialize

?>