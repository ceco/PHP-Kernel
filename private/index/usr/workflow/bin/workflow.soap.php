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

        extract_params();
        workflow_check();
        workflow_execute();

        if( defined("DEBUG") ) echo "<hr /> [1] end main";

    } // end workflow_main

    function extract_params () {
        global $kernel, $HTTP_RAW_POST_DATA;

        if( defined("DEBUG") ) echo "<div style='color:red'> 3. extract_params</div>";

        $kernel->params = $_REQUEST;
        $kernel->params["HTTP_RAW_POST_DATA"] = $HTTP_RAW_POST_DATA;
//        $parsed = new SOAP_Parser( $HTTP_RAW_POST_DATA );
//        $values = $parsed->getResponse();
//        if( $values->value )
//            foreach( $values->value as $value )
//                $this->params[$value->name] = $value->value;

        // Protect main variable
        unset($kernel->params['main']);
        unset($kernel->params['kernel']);
        $kernel->process_params();

        if( defined("PARAMS_DEBUG") ) print_r( $kernel->params );

    } // end extract_params

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

        $full_object_name = "{$kernel->app}_{$kernel->object}";
        $kernel->soap_server( $full_object_name );

    } // end execute

?>