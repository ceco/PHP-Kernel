<?

    /**
    * @name kernel.class.php
    * @date 2007/05/24
    * @version 5.0
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */

    /**
    * Main class which runs the project.
    * Creates resources: database, templates, acl, clients, db cache, statistics.
    * Detects automaticaly different environments and reacts according
    * current one.
    * Requires given app and executes requested object.
    * Environments supported: WWW, Shell, PDA, Smart, XML_RPC, SOAP, WAP, voice, RSS
    *
    * @name kernel
    *
    */
    class kernel {

        ////// GENERAL VARIABLE //////
        /**
        * @var string
        */
        protected $include_path;
        /**
        * @var array
        */
        public $params = array();
        public $params_html = array();
        public $params_sql = array();
        /**
        * @var string
        */
        public $host = null;
        /**
        * @var string
        */
        public $url = null;
        /**
        * @var string
        */
        public $app = null;
        /**
        * @var string
        */
        public $object = null;
        /**
        * @var string
        */
        public $prefix = null;
        /**
        * @var string
        */
        public $app_prefix = null;
        /**
        * @var array
        */
        public $user_info = array();
        /**
        * @var string
        */
        public $environment = ENVIRONMENT_NO_ENV;
        /**
        * @var string
        */
        public $imported_from = null;
        /**
        * @var string
        */
        public $target_div = null;
        /**
        * @var string
        */
        public $contents = null;

        /**
        * Global objects construction: acl, db connection, template obj
        * Creates smarty template, db connection and acl object
        * @name __construct
        *
        */
        public function __construct () {

            if( defined("DEBUG") ) echo "<b>kernel workflow:</b><br />";
            // Set error handling

            if( class_exists('PEAR') )
                PEAR::setErrorHandling( PEAR_ERROR_CALLBACK, array( &$this, "error") );

            // Create resources
            if( class_exists('DB') ){
                $this->db = DB::connect( DB_ACCESS );
                $this->db->setFetchMode( DB_FETCHMODE_ASSOC );
            }
            if( class_exists('Smarty') ){
                $this->templ = new Smarty();
                $this->templ->config_dir = SMARTY_LANGUAGE_DIR;
                $this->templ->compile_dir = SMARTY_COMPILE_DIR;
                $this->templ->assign("SMARTY_GLOBAL_LANGUAGE_DIR", SMARTY_GLOBAL_LANGUAGE_DIR );
            }

            if( class_exists('acl') )       $this->acl = new acl( $this->db );
            if( class_exists('statistics')) $this->statistics = new statistics($this);
            if( class_exists('dbcache') )   $this->dbcache = new dbcache($this);
            if( class_exists('soap') )      $this->soap = new soap($this);
            if( class_exists('xmlrpc') )    $this->xmlrpc = new xmlrpc($this);
            if( class_exists('rss') )       $this->rss = new rss($this);
            if( class_exists('lookup') )    $this->lookup = new lookup($this);

            // Save include path
            $this->include_path = get_include_path();

            $this->apps_base = PRIVATE_DIRECTORY;
            if( file_exists( PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR . DEFAULT_APPLICATION ) )
                 $this->apps_base .= DIRECTORY_SEPARATOR;

        } // end __construct

        function __call($funcname, $args = array()) {

            $tmp = split("_", $funcname );
            $class = array_shift( $tmp );
            $funcname = join("_", $tmp );

            if( method_exists( $this->$class, $funcname ) )
                $return = call_user_func_array(array(&$this->$class, $funcname), $args);

            return $return;
        }

        /////////////////  APPS METHODS  /////////////////////////////

        /**
        * Sets the active app and object to work with
        *
        * @name set
        * @param string $name app name
        * @param string $app_prefix enables the use of sub applications
        *
        */
        public function set ( $app, $app_prefix = DIRECTORY_SEPARATOR ){

            if( defined( "APP_DEBUG" ) ) echo "{Set $app}<br/>";

            $this->app = $app;
            $this->templ->assign( "app", $app );

            if( $this->apps_base == PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR ){

                $this->app_prefix = $app_prefix;
                $app_prefix = PRIVATE_DIRECTORY . $app_prefix . $app;

                // Set include paths
                $this->templ->template_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $this->environment;
                $this->templ->compile_id = "{$app}_{$this->environment}_";
                $this->templ->config_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "lang";
                set_include_path( $this->include_path . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "lib" . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "bin" );

                // Include config file if any
                if( file_exists( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php" ) )
                    require_once( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php");

            } else {
                echo "1";
            }

        } // end set

        /**
        * Includes given resource from a given app
        *
        * @name include_resource
        *
        * @return mixed result from include operation
        *
        */
        public function include_resource ( $resource = APP_CODE, $app = null, $object = null ){
            $result = null;
            $app = $app ? $app : $this->app;
            $object = $object ? $object : $this->object;

            if( !$app ) return null;

            if( $this->apps_base == PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR ){

                $app_prefix = PRIVATE_DIRECTORY . $this->app_prefix . $app;

                switch( $resource ){
                    case APP_API:
                        if( file_exists( $app_prefix . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . $object ) )
                            require_once( $app_prefix . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . $object );
                        break;
                    case APP_CODE:
                        if( file_exists( $app_prefix . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "$app.{$this->environment}.php" ) )
                            require_once( $app_prefix . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "$app.{$this->environment}.php" );
                        break;
                    case APP_TEMPLATE:
                        $result = $this->templ->fetch( "$object.tpl" );
                        break;
                } // end switch

            } else {
                $app_prefix = $this->app_prefix . $app;
                require_once ( $app_prefix."/$app.{$this->environment}.php" );
                echo "2";
            }

            return $result;

        } // end include_resource

        /**
        * Executes object from a given app
        *
        * @name call
        * @param string $app app name
        * @param string $object name of object
        * @param integer $echo echo on or off (default off)
        *
        * @return mixed result from function execution
        *
        */
        public function call ( $app = DEFAULT_APPLICATION, $object = DEFAULT_OBJECT, $echo = APP_ECHO_OFF ){

            if( defined( "APP_DEBUG" ) ) echo "{Call $app::$object}<br/>";

            $function = "{$app}_{$object}"; // build function name
            if( !$echo ) ob_start();  // If echo off (by default) stop output
            $this->include_resource( APP_CODE, $app );
            if( function_exists( $function ) ) $result = $function(); // Call function
            if( !$echo ) $result = ob_get_clean();  // Echo on

            return $result;
        } // end call

        /**
        * Executes object from given app preserving the current object and app
        *
        * @name call_from
        * @param string $app app name
        * @param string $object name of object
        * @param integer $echo echo on or off (default off)
        * @param string $environment change environment
        * @param string $app_prefix change application prefix
        *
        * @return mixed result from function execution
        *
        */
        public function call_from ( $app = DEFAULT_APPLICATION, $object = DEFAULT_OBJECT, $echo = APP_ECHO_OFF, $environment = null, $app_prefix = DIRECTORY_SEPARATOR ){

            // Save variables
            $_object = $this->object;
            $this->object = $object;
            $this->templ->assign("object", $object );
            $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();
            $this->templ->assign("prefix", $this->prefix );
            $_app = $this->app;
            $_app_prefix = $this->app_prefix;
 
            if( $environment ){
                $_environment = $environment;
                $this->environment = $environment;
                $this->templ->assign("environment", $environment );
            }

            // Set calling app
            $_imported_from = $this->imported_from;
            $this->imported_from = $this->app;
            $this->templ->assign("imported_from", $this->app );
            // Set target div
            if( $this->params["target_div"] ){
                $_target_div = $this->target_div;
                $this->target_div = $this->params["target_div"];
                $this->templ->assign("target_div", $this->params["target_div"] );
            }

            // Call function
            $this->set( $app, $app_prefix );
            $result = $this->call( $app, $object, $echo );
            $this->set( $_app, $_app_prefix );

            // Restore variables
            $this->object = $_object;
            $this->templ->assign("object", $_object );
            $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();
            $this->templ->assign("prefix", $this->prefix );

            if( $environment ){
                $this->environment = $_environment;
                $this->templ->assign("environment", $_environment );
            }
            // Restore target div
            if( $this->params["target_div"] ){
                $this->target_div = $_target_div;
                $this->templ->assign("target_div", $_target_div );
            }
            // Restore calling app
            $this->imported_from = $_imported_from;
            $this->templ->assign("imported_from", $_imported_from );

            return $result;
        } // end call_from

        /**
        * Bulds a list with existing kernel apps
        *
        * @name list_apps
        *
        * @return array list with app names
        *
        */
        public function list_apps (){
            $dirs = scandir(PRIVATE_DIRECTORY);
            $all_apps = array();

            foreach( $dirs as $dir )
                if ( is_dir(PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR."$dir") and $dir{0} != "." )
                    $all_apps[] = strtolower($dir);

            sort( $all_apps, SORT_STRING );

            return $all_apps;
        } // end list_apps

        /**
        * Bulds a list with all existing objects for given app
        *
        * @name list_objects
        * @param string $app_name
        *
        * @return array list with app names
        *
        */
        public function list_objects ( $app_name ){
            $file = PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR . $app_name . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "$app_name.{$this->environment}.php";
            if( file_exists( $file ) ) $content = file_get_contents($file);

            preg_match_all("|function( +){$app_name}_(.*?)\\(|", $content, $m );

            sort($m[2]);

            return $m[2];
        } // end list_objects

        /////////////////  END APPS METHODS  /////////////////////////////

        /**
        * Main function called from main program
        *
        * @name main
        *
        */
        public function main () {

            // Set error handling here because 
            set_error_handler( array($this, "error"), error_reporting() );

            // Detect environment
            $this->detect_environment();

            // Execute main workflow
            $this->call_from("workflow", "main", APP_ECHO_ON, null, DIRECTORY_SEPARATOR . DEFAULT_APPLICATION . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR);

        } // end main

        /////////////////   INPUT  ///////////////////////////////////
        /**
        * Detect if script is called from shell, www or web service request
        *
        * @name detect_environment
        * @global string $HTTP_RAW_POST_DATA
        * @global const ENVIRONMENT
        *
        * @return string environment type
        *
        */
        private function _detect_environment(){
            global $HTTP_RAW_POST_DATA;

            if( defined( 'STDIN' ) )  return ENVIRONMENT_SHELL;

            $headers = getallheaders();

            if( $_SERVER["REQUEST_METHOD"] == "POST" ){

                if( getenv("HTTP_SOAPACTION") ){
//                     require_once ("SOAP".DIRECTORY_SEPARATOR."Server.php");
                    return ENVIRONMENT_SOAP;
                }

                if( $headers["Content-Type"] == "text/xml" and
                    preg_match("|<methodCall>(.*)</methodCall>|s", $HTTP_RAW_POST_DATA )
                )  return ENVIRONMENT_XML_RPC;

                if( preg_match("|<vxml( +)version=\"([\d\\.]+)\"|s", $HTTP_RAW_POST_DATA, $voiceXML )){
                    define("VOICEXML_VERSION", $voiceXML[2] );
                    return ENVIRONMENT_VOICE;
                }

            } // End if POST

            if( $headers["RSS-Feed"] == "kernel" )
                return ENVIRONMENT_RSS;

            if( strstr( $_SERVER["HTTP_ACCEPT"], "vnd.wap.wml" ) )
                return ENVIRONMENT_WWW_WAP;

            if( strstr(strtolower($_SERVER["HTTP_USER_AGENT"]),"symbian os") ||  strstr(strtolower($_SERVER["HTTP_USER_AGENT"]),"midp")   )
                return ENVIRONMENT_WWW_SMART;

            if( strstr(strtolower($_SERVER["HTTP_UA_OS"]),"pocket pc") or strstr(strtolower($_SERVER["HTTP_USER_AGENT"]),"armv") )
                return ENVIRONMENT_WWW_PDA;

            return ENVIRONMENT_WWW;

        } // end _detect_environment

        public function detect_environment(){

            $this->environment = $this->_detect_environment();
            $this->templ->assign("environment", $this->environment );
            define("ENVIRONMENT", $this->environment );
            if( defined("DEBUG") ) echo "<div style='color:red'> 3. detect_environment: {$this->environment}</div>";

        } // end detect_environment

        /**
        * Extract input parameteres depending on environment
        *
        * @name extract_params
        * @global string $HTTP_RAW_POST_DATA
        * @global const ENVIRONMENT
        *
        */
        public function extract_params () {
            global $HTTP_RAW_POST_DATA;

            if( defined("DEBUG") ) echo "<div style='color:red'> 3. extract_params</div>";

            switch( $this->environment ){

                case ENVIRONMENT_SHELL:
                    for ($i = 1; $i < $_SERVER["argc"]; $i++){
                        if( preg_match("|-(.*)|",$_SERVER["argv"][$i]) ){
                         if( !preg_match("|-(.*)|",$_SERVER["argv"][$i+1]) ){
                            $arg = str_replace("-","",$_SERVER["argv"][$i]);
                            $this->params[$arg] = $_SERVER["argv"][++$i];
                         }
                         else $this->params[str_replace("-","",$_SERVER["argv"][$i])] = "";
                        }
                    } // end for argc
                    break;

                case ENVIRONMENT_RSS:
                case ENVIRONMENT_WWW_SMART:
                case ENVIRONMENT_WWW_PDA:
                case ENVIRONMENT_WWW_WAP:
                case ENVIRONMENT_WWW:
                    $this->params = $_REQUEST;
                    break;

                case ENVIRONMENT_XML_RPC:
                    $this->params = $_REQUEST;
                    $this->params["HTTP_RAW_POST_DATA"] = $HTTP_RAW_POST_DATA;
                    break;

                case ENVIRONMENT_SOAP:
                    $this->params = $_REQUEST;
                    $this->params["HTTP_RAW_POST_DATA"] = $HTTP_RAW_POST_DATA;
//                     $parsed = new SOAP_Parser( $HTTP_RAW_POST_DATA );
//                     $values = $parsed->getResponse();
//                     if( $values->value )
//                         foreach( $values->value as $value )
//                             $this->params[$value->name] = $value->value;
                    break;

            } // end switch environment

            $this->process_params();

            if( defined("PARAMS_DEBUG") ) print_r( $this->params );

        } // end extract_params

        public function process_params () {

            if ( get_magic_quotes_gpc() == 1 )  $this->params = $this->strip_slashes( $this->params );
            $this->params_html = $this->html_special_chars( $this->params );
            $this->params_sql  = $this->escape_simple( $this->params );

        } // end process_params

        public function strip_slashes ( $element ){

            if (is_array($element)) return array_map(array($this,'strip_slashes'), $element);
            else return stripslashes($element);

        } // end strip_slashes

        public function html_special_chars ( $element ){

            if (is_array($element)) return array_map(array($this,'html_special_chars'), $element);
            else return htmlspecialchars($element);

        } // end html_special_chars

        public function escape_simple ( $element ){

            if (is_array($element)) return array_map(array($this,'escape_simple'), $element);
            else return $this->db->escapeSimple($element);

        } // end escape_simple

        /**
        * Returns the incoming params array but stripped
        *
        * @name stripped_params
        *
        * @return array params
        *
        */
        public function stripped_params () {
            return $this->params;
        } // end stripped_params

        /**
        * Returns the incoming params array but with escaped
        *
        * @name escape_params
        *
        * @return array params
        *
        */
        public function escape_params () {
            return $this->params_sql;
        } // end escape_params

        /////////////////   END INPUT  ///////////////////////////////////

        /////////////////   CHECK  ///////////////////////////////////

        /**
        * Determine requested host, app and object
        *
        * @name determine_request
        *
        */
        public function determine_request () {
            if( defined("DEBUG") ) echo "<div style='color:red'> 3. determine_request</div> ";

            // Determine app
            $app = basename($this->params["app"]);
            $this->app = $app ? $app : DEFAULT_APPLICATION;
            $this->params["app"] = $app;

            // Determine object
            $object = $this->params["object"];
            $this->object = $object ? $object : DEFAULT_OBJECT;
            $this->params["object"] = $object;

            // Generate prefix
            $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();

            $this->target_div = $this->params["target_div"] ? $this->params["target_div"] : DEFAULT_TARGET_DIV;

            $this->user_info["lang"] = $this->user_info["lang"] ? $this->user_info["lang"] : DEFAULT_LANGUAGE;

            $this->templ->assign("object", $this->object );
            $this->templ->assign("prefix", $this->prefix );
            $this->templ->assign("lang", $this->user_info["lang"] );
            $this->templ->assign("target_div", $this->target_div );
            $this->templ->assign("user_info", $this->user_info );
            $this->templ->assign("params", $this->params_html );

            $this->set( $this->app );
            $this->include_resource( APP_CODE );

        } // end determine_request

        /////////////////  END CHECK  ///////////////////////////////////

        /////////////////   SERVER  ///////////////////////////////////
        /**
        * Server - calls the requested object for a given environment
        *
        * @name server
        *
        */
        public function server () {
            if( defined("DEBUG") ) echo "<div style='color:red'> 3. server </div>";

            $real_object = "{$this->app}_{$this->object}";

            // call function
            switch( $this->environment ){

                case ENVIRONMENT_XML_RPC: $this->xmlrpc_server( $real_object );  break;
                case ENVIRONMENT_SOAP:    $this->soap_server( $real_object );    break;
                case ENVIRONMENT_RSS:     $this->lookup_server( $real_object );  break;
                default:
              if( class_exists('lookup')) $this->lookup_lookup( $real_object );
              else                        $this->server_default( $real_object ); break;

            } // end switch

        } // end server

        /**
        * Call requested function
        *
        * @name server_default
        * @param string $object full function name
        *
        */
        public function server_default ( $object ) {
            if( defined("DEBUG") ) echo "<div style='color:blue'> 4. server_default </div>";

            if( function_exists( $object ) ) $object();
            else $this->warning("No such function - $object");

        } // end server_default

        /////////////////   END SERVER  ///////////////////////////////////

        /////////////////   INFORMATION  ///////////////////////////////////
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

            //$this->templ->assign("_MESSAGE_", $errmsg );
            //$this->call_from("info","error",APP_ECHO_ON, $this->environment, "/index/" );

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

            exit;

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

            $this->templ->assign("_MESSAGE_", $message );
            $this->call_from("info","warning",APP_ECHO_ON, $this->environment, DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR );

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

            $this->templ->assign("_MESSAGE_", $message );
            $this->call_from("info","info",APP_ECHO_ON, $this->environment, DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR );

        } // end info

        /////////////////  END INFORMATION  ///////////////////////////////////

        /**
        * Destructor
        *
        * @name __destruct
        *
        */
        //public function __destruct (){
        //    if( defined("DEBUG") ) echo "<hr /><b>End kernel workflow</b>";
        //} // end __destruct

    } // end class kernel

?>