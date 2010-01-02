<?

    /**
    * Main workflow
    *
    * @name workflow_main
    *
    */
    function workflow_main (){
        global $kernel;

        if( defined("DEBUG") ) echo " [1] main";

        workflow_input();
        workflow_check();
        workflow_execute();
        workflow_output ();

        if( defined("DEBUG") ) echo "<hr /> [1] end main";

    } // end workflow_main

    /**
    * Input methods
    *
    * @name workflow_input
    *
    */
    function workflow_input (){
        global $kernel;

        if( defined("DEBUG") ) echo "<hr /> [2] input";
        $kernel->extract_params();

    } // end workflow_input

    /**
    * Check methods
    *
    * @name workflow_check
    *
    */
    function workflow_check () {
        global $kernel;

        if( defined("DEBUG") ) echo "<hr /> [2] check";
        workflow_auth();
        workflow_acl();
        $kernel->determine_request();

    } // end workflow_check

    /**
    * Authenticate according environment
    *
    * @name workflow_auth
    * @global const ENVIRONMENT
    *
    */
    function workflow_auth () {
        global $kernel;

        if( defined("DEBUG") ) echo "<div style='color:red'> 3. auth</div>";

    } // end auth

    /**
    * Checks access control privilages
    *
    * @name workflow_acl
    *
    */
    function workflow_acl () {
        global $kernel;

        if( defined("DEBUG") ) echo "<div style='color:red'> 3. check_acl</div>";

    } // end workflow_acl

    /**
    * Execute methods
    *
    * @name execute
    *
    */
    function workflow_execute () {
        global $kernel;

        if( defined("DEBUG") ) echo "<hr /> [2] execute";
        if( defined("CACHE_OUTPUT") ) ob_start();
        if( !$kernel->params["content_only"] ) $kernel->call_from("frames","header",APP_ECHO_ON,null,DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR);

        // Use cache if exists
        if( $contents = $kernel->cache_data( null, "{$kernel->app}_{$kernel->object}.txt", 300) )
            echo $contents;
        else {
            if( defined("DEBUG") ) echo "<div style='color:blue'> 4. default_server </div>";
            $full_object_name = "{$kernel->app}_{$kernel->object}";
            if( class_exists('lookup')) $kernel->lookup_lookup( $full_object_name );
            elseif( function_exists( $full_object_name ) ) $full_object_name();
            else $kernel->warning("No such function - $full_object_name");
        }

        if( !$kernel->params["content_only"] ) $kernel->call_from("frames","footer",APP_ECHO_ON,null,DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR);
        if( defined("CACHE_OUTPUT") ) $kernel->contents = ob_get_clean();

    } // end execute

    /**
    * Output methods
    *
    * @name workflow_output
    *
    */
    function workflow_output (){
        global $kernel;

        if( defined("DEBUG") ) echo "<hr /> [2] output";
        if( defined("CACHE_OUTPUT") ){
            $kernel->contents = str_replace("Footer","End", $kernel->contents );
            echo $kernel->contents;
        }

    } // end workflow_output

?>