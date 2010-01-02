<?

    class info {

        protected $main;

        public function __construct ( $main ) {

            $this->main = $main;

        } // end __construct

        /**
        * Function used to show error message to the user and exit. Does not
        * exits in SOA environment.
        *
        * @name error
        * @global const ENVIRONMENT
        * @param string $errno error number
        * @param string $errmsg variable to display
        * @param string $filename in which file
        * @param string $linenum on which line
        * @param string $vars object with all variables
        *
        */
        public function error( $errno, $errmsg = null, $filename = null, $linenum = null, $vars = null ){

            if( is_object( $errno ) ) $errmsg = defined("DEBUG") ? $errno->getMessage() : $errno->getUserInfo();
            if( is_string( $errno ) ) $errmsg = $errno;

            //$this->main->templ->assign("_MESSAGE_", $errmsg );
            //$this->main->call_from("info","error",APP_ECHO_ON, $this->main->environment, "/index/" );

            switch( ENVIRONMENT ){

                case ENVIRONMENT_SHELL:
                    echo "Error: $errmsg\n";
                    if( $filename ) echo "File: $filename\n";
                    if( $linenum ) echo "Line: $linenum\n";
                    echo "\n";
                    break;

                case ENVIRONMENT_WWW_WAP:
                    echo "Error: $errmsg";
                    break;
                case ENVIRONMENT_WWW_SMART:
                case ENVIRONMENT_WWW_PDA:
                case ENVIRONMENT_WWW:
                    echo "<table cellspacing=\"0\" border=\"0\" style=\"font-size: 12px; border: 1px solid black\">\n<tr><td align=\"right\"><b>Error:</b></td><td>$errmsg</td></tr>\n";
                    if( $filename ) echo "<tr><td align=\"right\"><b>File:</b></td><td>$filename</td></tr>\n";
                    if( $linenum ) echo "<tr><td align=\"right\"><b>Line:</b></td><td>$linenum</td></tr>\n";
                    echo "</table>\n";
                    break;

                case ENVIRONMENT_XML_RPC:
                    $response = "Error: $errmsg";
                    if( $filename ) $response .= "File: $filename ";
                    if( $linenum ) $response .= "Line: $linenum ";
                    return new XML_RPC_Response(new XML_RPC_Value($response, "string"));

                case ENVIRONMENT_SOAP:
                    $response = "Error: $errmsg";
                    if( $filename ) $response .= "File: $filename ";
                    if( $linenum ) $response .= "Line: $linenum ";
                    return $response;

                default:
                    echo "Error: $errmsg\n";
                    if( $filename ) echo "File: $filename\n";
                    if( $linenum ) echo "Line: $linenum\n";
                    echo "\n";
                    break;

            } // end switch

            if( is_integer($errno) and $errno == E_USER_ERROR ) exit;
            if( is_object( $errno ) ) exit;

        } // end error

        /**
        * Function used to show warning message to the user.
        *
        * @name warning
        * @global const ENVIRONMENT
        * @param string $message variable to display
        *
        */
        public function warning( $message ){

            $this->main->templ->assign("_MESSAGE_", $message );
            echo $this->main->call_from("info","warning",APP_ECHO_ON, $this->main->environment, DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR );

        } // end warning

        /**
        * Function used to show information message to the user.
        *
        * @name info
        * @global const ENVIRONMENT
        * @param string $message variable to display
        *
        */
        public function info( $message ){

            $this->main->templ->assign("_MESSAGE_", $message );
            $this->main->call_from("info","info",APP_ECHO_ON, $this->main->environment, DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR );

        } // end info

    } // end class info

?>