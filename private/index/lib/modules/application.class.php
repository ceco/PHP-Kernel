<?

    /**
    * This class is kerenl module that deals with components (applications)
    *
    * @name application.class.php
    * @date 2007/12/04
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    *
    */
    class application {

        /**
        * @var object
        */
        protected $kernel;
        /**
        * @var string
        */
        protected $app;
        /**
        * @var string
        */
        protected $object;
        /**
        * @var string
        */
        protected $host;
        /**
        * @var string
        */
        public $app_prefix = null;
        /**
        * @var string  (old name $imported_from)
        */
        public $calling_app = null;
        /**
        * @var string
        */
        public $target_div = null;
        /**
        * @var string
        */
        public $contents = null;

        /**
        *
        * @name __construct
        *
        */
        public function __construct ( $kernel ){

            $this->kernel = $kernel;

        } // end __construct

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

//             if( $this->apps_base == PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR ){

                $this->app_prefix = $app_prefix;
                $app_prefix = PRIVATE_DIRECTORY . $app_prefix . $app;

                // Set include paths
                $this->templ->template_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $this->environment;
                $this->templ->compile_id = "{$app}_{$this->environment}_";
                $this->templ->config_dir = $app_prefix . DIRECTORY_SEPARATOR . "usr" . DIRECTORY_SEPARATOR . "lang";
                set_include_path( $this->include_path . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "lib" . PATH_SEPARATOR . $app_prefix . DIRECTORY_SEPARATOR . "bin" );

                // Include config file if any
                if( $app != DEFAULT_APPLICATION and file_exists( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php" ) )
                    $this->requireonce( $app_prefix . DIRECTORY_SEPARATOR . "etc" . DIRECTORY_SEPARATOR . "config.php");

//             } else  echo "1"; //

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

//             if( $this->apps_base == PRIVATE_DIRECTORY . DIRECTORY_SEPARATOR ){

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

//             } else {
//                 $app_prefix = $this->app_prefix . $app;
//                 $this->requireonce ( $app_prefix."/$app.{$this->environment}.php" );
//                 echo "2";
//             }

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

            // if( class_exists('acl') and !$this->acl->can_read() ) return;

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
                    $all_apps[] = $dir;

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

    } // end class application

?>