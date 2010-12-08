<?
    /**
    * @name kernel.class.php
    * @date 2009/12/12
    * @version 7.0
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */

    /**
    * Main class which runs the project.
    * - Creates resources and loads modules from INITRD
    * - Runs main workflow
    * - Provides core functions
    *
    * @name kernel
    *
    */
    class kernel {
        /**
        * Holds php include path
        * @var string
        */
        protected $include_path;
        /**
        * Current input parameters (raw, html safe, sql safe)
        * @var array
        */
        public $params = array();
        public $params_html = array();
        public $params_sql = array();
        /**
        * Current host that holds applications
        * @var string
        */
        public $host = null;
        /**
        * @var string
        */
        public $url = null;
        /**
        * Current application
        * @var string
        */
        public $app = null;
        /**
        * Current object
        * @var string
        */
        public $object = null;
        /**
        * Current process id
        * @var string
        */
        public $pid = null,$prefix = null;
        /**
        * @var string
        */
        public $app_prefix = null;
        /**
        * Current user session information
        * @var array
        */
        public $user_info = array();
        /**
        * Current working environment
        * @var string
        */
        public $environment = ENVIRONMENT_WWW;
        /**
        * @var array
        */
        public $call_stack = array();
        /**
        * @var string
        */
        public $target_div = null;
        /**
        * @var string
        */
        public $contents = null;
        /**
        * List with included files
        * @var string
        */
        private static $paths = array();
        /**
        * List with kernel modules to include
        * @var array
        */
        public $INITRD = array();

        /**
        * Initialization of the kernel
        * @name __construct
        * @global array $INITRD list with modules to load in kernel and module configurations
        *
        */
        public function __construct () {

            if( defined("DEBUG") ) echo "<b>kernel workflow:</b><br />";

            // Make modules list and arguments visible to kernel
            $this->INITRD = $GLOBALS["INITRD"];

            // Set PEAR error handling
            if( class_exists('PEAR') )
                PEAR::setErrorHandling( PEAR_ERROR_CALLBACK, array( &$this, "error") );

            // Load kernel modules
            foreach( $this->INITRD["MODPROBE"] as $mod ) $this->modprobe( $mod );

            // Save include path
            $this->include_path = get_include_path();

            // Add config file to cached files
            self::$paths[] = PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR."index".DIRECTORY_SEPARATOR."etc".DIRECTORY_SEPARATOR."config.php";

        } // end __construct

        /**
        * Main function called from main program
        *
        * @name main
        *
        */
        public function main () {

            // Set default error handling 
            set_error_handler( array($this, "error"), error_reporting() );

            // Set environment
            if( defined( 'STDIN' ) ) $this->set_environment(ENVIRONMENT_SHELL);
            else $this->set_environment($this->environment);

            if( defined("DEBUG") ) echo "<div style='color:red'> environment is: {$this->environment}</div>";

            // Execute main workflow
            $this->call_from("workflow", "main", APP_ECHO_ON, null, DIRECTORY_SEPARATOR . DEFAULT_APPLICATION . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR);

        } // end main

        /////////////////  APPS METHODS  /////////////////////////////

        /**
        * Sets the current active app and object to use
        *
        * @name set
        * @param string $name app name
        * @param string $app_prefix enables the use of sub applications
        *
        */
        public function set ( $app, $app_prefix = DIRECTORY_SEPARATOR ){

            if( defined( "APP_DEBUG" ) ) echo "{Set $app}<br/>";

            $this->app = $app;
            $this->app_prefix = $app_prefix;
            $app_prefix = PRIVATE_DIRECTORY . $app_prefix . $app;

            // Set include path
            set_include_path( $this->include_path . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "lib" . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "bin" );

            // Include config file if exists
            if( $app != DEFAULT_APPLICATION and file_exists( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php" ) )
                $this->requireonce( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php");

            // Initialize template if module loaded
            if( $this->template ) $this->template->init($app, $app_prefix);

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

            $app_prefix = PRIVATE_DIRECTORY . $this->app_prefix . $app;

            switch( $resource ){
                case APP_API:
                case APP_LIB:
                    if( file_exists( $app_prefix . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . $object ) )
                        $this->requireonce( $app_prefix . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . $object );
                    break;
                case APP_CODE:
                    if( file_exists( $app_prefix . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "$app.{$this->environment}.php" ) )
                    if( !function_exists( "{$app}_{$object}" ))
                        $this->requireonce( $app_prefix . DIRECTORY_SEPARATOR . "bin" . DIRECTORY_SEPARATOR . "$app.{$this->environment}.php" );
                    break;
                case APP_TEMPLATE:
                    $result = $this->templ->fetch( "$object.tpl" );
                    break;
            } // end switch

            return $result;

        } // end include_resource

        /**
        * Require class from given application
        * @name requireonce_from
        * @param string $app application name
        * @param string $class class name
        */
        public function requireonce_from( $app, $class = null ){

            if( !$class ) $class = $app;
            self::requireonce( PRIVATE_DIRECTORY.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."$class.class.php" );

        } // end requireonce_from

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

            // if( $this->acl and !$this->acl->can_read() ) return;
            $function = "{$app}_{$object}"; // build function name
            if( !$echo ) ob_start();  // If echo off (by default) stop output
            $this->include_resource( APP_CODE, $app );
            array_push($this->call_stack, array($app,$object) );
            if( function_exists( $function ) ) $result = $function(); // Call function
            array_pop($this->call_stack);
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
            $this->pid = $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();
            $_app = $this->app;
            $_app_prefix = $this->app_prefix;

            if( $this->templ ){
                $this->templ->assign("object", $object );
                $this->templ->assign("prefix", $this->prefix );
                $this->templ->assign("pid", $this->prefix );
            }
 
            if( $environment ){
                $_environment = $environment;
                $this->environment = $environment;
                if( $this->templ )
                    $this->templ->assign("environment", $environment );
            }

            // Set target div
            if( $this->params["target_div"] ){
                $_target_div = $this->target_div;
                $this->target_div = $this->params["target_div"];
                if( $this->templ )
                    $this->templ->assign("target_div", $this->params["target_div"] );
            }

            // Call function
            $this->set( $app, $app_prefix );
            $result = $this->call( $app, $object, $echo );
            $this->set( $_app, $_app_prefix );

            // Restore variables
            $this->object = $_object;
            $this->pid = $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();
            if( $this->templ ){
                $this->templ->assign("object", $_object );
                $this->templ->assign("prefix", $this->prefix );
                $this->templ->assign("pid", $this->prefix );
            }

            if( $environment ){
                $this->environment = $_environment;
                if( $this->templ )
                    $this->templ->assign("environment", $_environment );
            }
            // Restore target div
            if( $this->params["target_div"] ){
                $this->target_div = $_target_div;
                if( $this->templ )
                    $this->templ->assign("target_div", $_target_div );
            }

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
            foreach ( new DirectoryIterator(PRIVATE_DIRECTORY) as $Item )
                if( $file = $Item->getFilename() and $Item->isDir() and $file[0] != '.' )
                    $all_apps[] = $file;

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

            preg_match_all("|function( +){$app_name}_(.*?)( *)\\(|", $content, $m );

            sort($m[2]);

            return $m[2];
        } // end list_objects

        ///////////////// INPUT METHODS ///////////////////////////////////
        /**
        * Extract input parameters depending on environment
        *
        * @name extract_params
        *
        */
        public function extract_params () {

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

                default:
                    $this->params = $_REQUEST;
                    if( count($_FILES) ) $this->params = array_merge( $this->params, $_FILES );
                    break;

            } // end switch environment

            // Protect main variable
            unset($this->params['main']);
            unset($this->params['kernel']);
            // Process params and fill all arrays
            $this->process_params();

            if( defined("PARAMS_DEBUG") ) print_r( $this->params );

        } // end extract_params

        /**
        * Sets the arrays params, params_html and params_sql using $params
        * Assigns params_html to template
        *
        * @name set_params
        * @param array $params
        *
        * Very usefull to call prior call_from() to set given parameters
        *
        */
        public function set_params( $params = array() ){

            $this->params = array_merge( $this->params, $params );
            $this->params_html = array_merge( $this->params_html, $this->html_special_chars( $params ) );
            $this->params_sql = array_merge( $this->params_sql, $this->escape_simple( $params ) );
            if( $this->templ )
                $this->templ->assign("params", $this->params_html );

        } // end set_params

        /**
        * Sets the arrays params, params_html and params_sql
        *
        * @name process_params
        *
        */
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

        ///////////////// CHECK METHODS ///////////////////////////////////

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
            $this->params["app"] = $this->app = $app ? $app : DEFAULT_APPLICATION;

            // Determine object
            $object = $this->params["object"];
            $this->params["object"] = $this->object = $object ? $object : DEFAULT_OBJECT;

            // Generate prefix
            $this->pid = $this->prefix = $this->params["prefix"] ? $this->params["prefix"] : rand();

            $this->target_div = $this->params["target_div"] ? $this->params["target_div"] : DEFAULT_TARGET_DIV;

            $this->user_info["lang"] = $this->user_info["lang"] ? $this->user_info["lang"] : DEFAULT_LANGUAGE;

            if( $this->templ ){
                $this->templ->assign("object", $this->object );
                $this->templ->assign("prefix", $this->pid );
                $this->templ->assign("pid", $this->pid );
                $this->templ->assign("lang", $this->user_info["lang"] );
                $this->templ->assign("target_div", $this->target_div );
                $this->templ->assign("user_info", $this->user_info );
                $this->templ->assign("params", $this->params_html );
            }

            $this->set( $this->app );
            $this->include_resource( APP_CODE );

        } // end determine_request

        /////////////////  HELPER METHODS  /////////////////////////////

        function __call($funcname, $args = array()) {
            $return = null;
            $tmp = explode("_", $funcname );
            $class = array_shift( $tmp );
            $funcname = join("_", $tmp );

            if( method_exists( $this->$class, $funcname ) )
                $return = call_user_func_array(array(&$this->$class, $funcname), $args);

            return $return;
        } // end __call

        public function modprobe ( $module ){
            if( !class_exists( $module ) )
                $this->requireonce( "modules" . DIRECTORY_SEPARATOR . basename($module) . ".class.php" );

            if( !$this->$module )
                $this->$module = new $module( $this );

        } // end modprobe

        public static function includeonce($path_file){
            if(!in_array($path_file,self::$paths)){
                include($path_file);
                self::$paths[] = $path_file;
            }
        } // end includeonce

        public static function requireonce($path_file){
            if(!in_array($path_file,self::$paths)){
                require($path_file);
                self::$paths[] = $path_file;
            }
        } // end requireonce

        public function set_environment($environment){
            $this->environment = $environment;
            if( $this->templ )
                $this->templ->assign("environment", $this->environment );
            define("ENVIRONMENT", $this->environment );
        } // end set_environment

        /**
        * Transforms numeric N dimensional array to the form $key_name => $value_name
        *
        * @name transform
        * @param array $array
        * @param string $key_name
        * @param string $value_name
        *
        * @return array transformed array
        *
        */
        public function transform ( $array, $key_name, $value_name ){
            $result = array();
            foreach( $array as $val )
                $result[$val[$key_name]] = $val[$value_name];
            return $result;
        } // end transform

        /////////////////  INFORMATION METHODS ///////////////////////////////////
        /**
        * Function used to show error message to the user and exit. Does not
        * exits in SOA environment.
        *
        * @name error
        * @param string $errno error number
        * @param string $errmsg variable to display
        * @param string $filename in which file
        * @param string $linenum on which line
        * @param string $vars object with all variables
        *
        */
        public function error( $errno, $errmsg = null, $filename = null, $linenum = null, $vars = null ){

            $this->modprobe("info");
            $this->info_error( $errno, $errmsg, $filename, $linenum, $vars );

        } // end error

        /**
        * Function used to show warning message to the user.
        *
        * @name warning
        * @param string $message variable to display
        *
        */
        public function warning( $message ){

            $this->modprobe("info");
            $this->info_warning( $message );

        } // end warning

        /**
        * Function used to show information message to the user.
        *
        * @name info
        * @param string $message variable to display
        *
        */
        public function info( $message ){

            $this->modprobe("info");
            $this->info_info( $message );

        } // end info

    } // end class kernel

?>