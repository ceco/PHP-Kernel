<?

    /**
    * Main workflow
    *
    * @name workflow_main
    *
    */
    function workflow_main (){
        global $main;

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
        global $main;

        if( defined("DEBUG") ) echo "<hr /> [2] input";
        $main->extract_params();
        $main->process_params();

    } // end workflow_input

    /**
    * Check methods
    *
    * @name workflow_check
    *
    */
    function workflow_check () {
        global $main;

        if( defined("DEBUG") ) echo "<hr /> [2] check";
        workflow_auth();
        workflow_acl();
        $main->determine_request();

    } // end workflow_check

    /**
    * Authenticate according environment
    *
    * @name workflow_auth
    * @global const ENVIRONMENT
    *
    */
    function workflow_auth () {
        global $main;

        if( defined("DEBUG") ) echo "<div style='color:red'> 3. auth</div>";

    } // end auth

    /**
    * Checks access control privilages
    *
    * @name workflow_acl
    *
    */
    function workflow_acl () {
        global $main;

        if( defined("DEBUG") ) echo "<div style='color:red'> 3. check_acl</div>";

    } // end workflow_acl

    /**
    * Execute methods
    *
    * @name execute
    *
    */
    function workflow_execute () {
        global $main;

        if( defined("DEBUG") ) echo "<hr /> [2] execute";
        if( defined("CACHE_OUTPUT") ) ob_start();
        if( !$main->params["content_only"] ) $main->call_from("frames","header",APP_ECHO_ON,null,DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR);
        $main->server();
        if( !$main->params["content_only"] ) $main->call_from("frames","footer",APP_ECHO_ON,null,DIRECTORY_SEPARATOR.DEFAULT_APPLICATION.DIRECTORY_SEPARATOR."usr".DIRECTORY_SEPARATOR);
        if( defined("CACHE_OUTPUT") ) $main->contents = ob_get_clean();

    } // end execute

    /**
    * Output methods
    *
    * @name workflow_output
    *
    */
    function workflow_output (){
        global $main;

        if( defined("DEBUG") ) echo "<hr /> [2] output";
        if( defined("CACHE_OUTPUT") ){
            $main->contents = str_replace("Footer","End", $main->contents );
            echo $main->contents;
        }

    } // end workflow_output

?>