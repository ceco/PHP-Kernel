<?

        class KernelTest extends UnitTestCase {

            function testKernel() {
                global $kernel;

                $kernel = new kernel();

                // public function __construct () {
                $this->assertFalse( PEAR::isError($kernel->db) != false );
                $this->assertFalse( is_object($kernel->templ) == false );
                $this->assertFalse( is_object($kernel->acl) == false );
                $this->assertFalse( is_object($kernel->statistics) == false );
                //$this->assertFalse( is_object($kernel->dbcache) == false );
                $this->assertFalse( is_object($kernel->soap) == false );
                $this->assertFalse( is_object($kernel->xmlrpc) == false );
                $this->assertFalse( is_object($kernel->rss) == false );
                //$this->assertFalse( is_object($kernel->lookup) == false );

                $this->assertFalse( is_object($kernel->manage_record) != false );
                $kernel->modprobe("manage_record");
                $this->assertFalse( is_object($kernel->manage_record) == false );

                //public function set ( $app, $app_prefix = DIRECTORY_SEPARATOR ){
                $kernel->set( "index" );
                $this->assertFalse( $kernel->app != "index" );

                ob_start();
                $kernel->error( "test" );
                $err_str = ob_get_clean();
                $this->assertFalse( !preg_match("|Error.*test|", $err_str) );

//                 $kernel->warning( "warn" );
//                 $kernel->info( "info" );

//                 $this->assertFalse( $kernel->params["app"] != "index" );

//                 include_resource ( $resource = APP_CODE, $app = null, $object = null )
//                 call ( $app = "index", $object = "index", $echo = APP_ECHO_OFF )
//                 call_from ( $app = "index", $object = "index", $echo = APP_ECHO_OFF, $environment = null, $app_prefix = DIRECTORY_SEPARATOR )
//                 list_apps ( $all = false )
//                 list_objects ( $app_name )
//                 main ()

                // public function detect_environment(){
                $kernel->detect_environment();
                $this->assertFalse( $kernel->environment != $kernel->environment );

                // extract_params ();
                $_REQUEST["app"] = "index";
                $kernel->extract_params();
                $this->assertFalse( $kernel->params["app"] != $_REQUEST["app"] );




//                 stripped_params ()
//                 fixValues ( $element )
//                 escape_params ()
//                 escapeValues ( $element )
//                 process_params ()

                $_REQUEST["app"] = "dd";
                $_REQUEST["object"] = "dd1";
                $kernel->extract_params();

                $kernel->determine_request ();
                $this->assertFalse( $kernel->app != $_REQUEST["app"] );
                $this->assertFalse( $kernel->object != $_REQUEST["object"] );
                $this->assertFalse( $kernel->target_div != DEFAULT_TARGET_DIV );

//                 server ()
//                 server_soap ( $object )
//                 server_xmlrpc ( $object )
//                 server_default ( $object )
//                 function_description()
//                 xml_rpc_call( $host, $url, $object, $values = null, $user = null, $pass = null )
//                 soap_call ( $host, $url, $object, $params = null, $user = null, $pass = null )
//                 error( $errno, $errmsg = null, $filename = null, $linenum = null, $vars = null )
//                 warning( $message )
//                 info( $message )

                // Test cache functions
/*                $kernel->dbcache_db_cache("SELECT * FROM ".LOOKUP_TABLE, "lookup.txt" );
                $this->assertFalse( file_exists(DB_CACHE_DIR.DIRECTORY_SEPARATOR."lookup.txt") != 1 );
                $kernel->dbcache_db_cache_clear("lookup.txt" );
                $this->assertFalse( file_exists(DB_CACHE_DIR.DIRECTORY_SEPARATOR."lookup.txt") == 1 );
                $kernel->dbcache_db_cache("SELECT * FROM ".LOOKUP_TABLE, "lookup.txt" );
                $this->assertFalse( file_exists(DB_CACHE_DIR.DIRECTORY_SEPARATOR."lookup.txt") != 1 );
                $kernel->dbcache_db_cache_clear( DB_CACHE_DIR.DIRECTORY_SEPARATOR."lookup.txt" );
                $this->assertFalse( file_exists(DB_CACHE_DIR.DIRECTORY_SEPARATOR."lookup.txt") == 1 );
*/
            }
        } // end class KernelTest

?>