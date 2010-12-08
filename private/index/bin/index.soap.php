<?

    function index_soap (){
        global $kernel;
        extract( $kernel->params );

        //return $kernel->call_from("index","test",APP_ECHO_OFF,ENVIRONMENT_WWW)." SOAP";

        return "<div>SOAP: $test</div>";
    } // end  index_index_soap

    function index_list_apps (){
        global $kernel;

        $apps = $kernel->list_apps();
        //foreach( $modiles as $m ) print_r( $kernel->list_objects($m) );

        //return "test";
        return join("<br />", $apps );
    } // end index_list_apps

?>