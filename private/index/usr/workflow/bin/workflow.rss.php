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

        $full_object_name = "{$kernel->app}_{$kernel->object}";
        $kernel->lookup_server( $full_object_name );

    } // end execute

?>