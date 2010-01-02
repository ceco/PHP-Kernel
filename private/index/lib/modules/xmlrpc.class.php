<?

    class xmlrpc {

        /**
        * @var object
        */
        private $kernel;

        public function __construct ( $kernel ) {

            $this->kernel = $kernel;

        } // end __construct

        /**
        * xml rpc client for calling remote procedures
        *
        * @name client
        * @param string $host remote hostname
        * @param string $url to call
        * @param string $object function name to call
        * @param mixed $values arguments to pass
        *
        * @return string function result
        *
        */
        public function client( $host, $url, $object, $values = null, $user = null, $pass = null ){

            require_once ("XML".DIRECTORY_SEPARATOR."RPC.php");

            $user = $user ? $user : $_SERVER["PHP_AUTH_USER"];
            $pass = $pass ? $pass : $_SERVER["PHP_AUTH_PW"];

            $client = new XML_RPC_Client($url, $host);
            $client->setCredentials($user,$pass);
            $client->setDebug(0);

            if( $values ){

                if( !is_array( $values ) )
                    $msg = @(new XML_RPC_Message( $object, array(new XML_RPC_Value($values))));
                else {
                    foreach( $values as $v ) $vals[] = new XML_RPC_Value( $v );
                    $msg = @(new XML_RPC_Message( $object, array(new XML_RPC_Value($vals, 'struct'))));
                }
            } else {
                $msg = @(new XML_RPC_Message( $object ));
            }

            // send the request message
            $resp = $client->send($msg);

            if (!$resp) $this->kernel->error($client->errstr);

            if (!$resp->faultCode()) {
                $val = $resp->value();
                return $val->scalarval();
            } else
                $this->kernel->error("Fault Code: " . $resp->faultCode() . "\n"."Fault Reason: " . $resp->faultString() . "\n");

            return "";

        } // end client


        /**
        * Call requested function for xml-rpc environment
        *
        * @name server
        * @param string $object full function name
        *
        */
        public function server ( $object ) {
            if( defined("DEBUG") ) echo "<div style='color:blue'> 4. server_xmlrpc </div>";

            require_once ("XML".DIRECTORY_SEPARATOR."RPC".DIRECTORY_SEPARATOR."Server.php");

            if( function_exists( $object ) ){
                $functions = array( $object => array("function" => $object ));

                $args = $this->function_description();
                $values = xmlrpc_decode_request( $this->kernel->params["HTTP_RAW_POST_DATA"],$object );
                for( $c = 0; $c < count( $values ); $c++ )
                    $this->kernel->params[$args[$c]] = $values[$c];
            }

            $server = new XML_RPC_Server( $functions );

        } // end server

        /**
        * Extracts information about method requested
        *
        * @name function_description
        *
        * @return array args arguments extracted from phpdoc information
        *
        */
        public function function_description(){
            $args = array();

            $file = file_get_contents(PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.$this->kernel->app.DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."{$this->kernel->app}.{$this->kernel->environment}.php");
            preg_match("|/\\*\\*(.*)@name( *){$this->kernel->app}_{$this->kernel->object}(.*)\\*\\/|s", $file, $matches );
            $lines = explode("\n", $matches[3] );
            foreach( $lines as $line ){
                if( strstr($line, "@param") ){
                    $line = preg_replace("|( +)|", " ", trim($line) );
                    $arr = explode(" ", $line);
                    $args[] = str_replace('$',"",$arr[3]);
                }
            }
            return $args;

        } // end function_description

    } // end class xmlrpc

?>