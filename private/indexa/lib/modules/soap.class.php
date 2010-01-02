<?

    class soap {

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {

            $this->kernel = $kernel;

        } // end __construct

        /**
        * soap client for calling remote procedures
        *
        * @name client
        * @param string $host remote hostname
        * @param string $url to call
        * @param string $object function name to call
        * @param mixed $params arguments to pass
        *
        * @return string function result
        *
        *
        public function clien ( $host, $url, $object, $params = null, $user = null, $pass = null, $proto = "http" ){

            require_once ("SOAP".DIRECTORY_SEPARATOR."Client.php");

            $user = $user ? $user : $_SERVER["PHP_AUTH_USER"];
            $pass = $pass ? $pass : $_SERVER["PHP_AUTH_PW"];

            $client = new SOAP_Client($proto."://$host$url");
            $options = array( "namespace" => SOAP_URN, "headers" => array( "Authorization" => "Basic ".base64_encode("$user:$pass" ) ) );

            $response = $client->call( $object, $params, $options );
            if( PEAR::isError($response) ) $this->kernel->error( $response->message );

            return $response;

        } // end client
*/
        public function client ($url, $object){

            $client = new SoapClient(null, array('location' => $url, 'uri' => "http://test-uri/"));
            return $client->$object();

        } // end client

        public function server ( $object ){

            $server = new SoapServer(null, array('uri' => "http://test-uri/"));
            $server->addFunction( $object );
            $server->handle();

        } // end server

        /**
        * Call requested function for soap environment
        *
        * @name server
        * @param string $object full function name
        *
        *
        public function serve ( $object ) {
            if( defined("DEBUG") ) echo "<div style='color:blue'> 4. server_soap </div>";

            // Create server class
            $call = function_exists($object) ? " return {$object}(); " : " return \" ".$this->kernel->warning("No such function - $object!")."\"; ";
            $soap_class = "class SOAP_kernel_Server { function {$object}() { $call } }";
            eval( $soap_class );

            // Execute soap server
            $server = new SOAP_Server();
            $soapkernelserver = new SOAP_kernel_Server();
            $server->addObjectMap($soapkernelserver, SOAP_URN);
            $server->service( $this->kernel->params["HTTP_RAW_POST_DATA"] );

        } // end server
*/
    } // end class soap

?>