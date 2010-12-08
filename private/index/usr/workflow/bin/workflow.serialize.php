<?

    /**
    * Main workflow
    *
    * @name workflow_main
    *
    */
    function workflow_main (){
        global $kernel;

        workflow_input();
        workflow_check();
        workflow_execute();

    } // end workflow_main

    /**
    * Input methods
    *
    * @name workflow_input
    *
    */
    function workflow_input (){
        global $kernel;

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

    } // end auth

    /**
    * Checks access control privilages
    *
    * @name workflow_acl
    *
    */
    function workflow_acl () {
        global $kernel;

    } // end workflow_acl

    /**
    * Execute methods
    *
    * @name execute
    *
    */
    function workflow_execute () {
        global $kernel;

        $full_object_name = "{$kernel->app}_{$kernel->object}";
        if( class_exists('lookup')) $kernel->lookup_lookup( $full_object_name );
        elseif( function_exists( $full_object_name ) ){
            array_push($kernel->call_stack, array($kernel->app,$kernel->object) );
            $full_object_name();
            array_pop($kernel->call_stack);
        }
        else $kernel->warning("No such function - $full_object_name");

    } // end execute

?>