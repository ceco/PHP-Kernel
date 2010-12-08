<?

    function index_soap (){
        global $kernel;
        extract( $kernel->params );

        return $kernel->call_from("test","index",APP_ECHO_OFF,ENVIRONMENT_WWW)." SOAP";

        return "<div>SOAP: $test</div>";
    } // end  index_index_soap

    function index_list_apps (){
        global $kernel;

        $blocks = $kernel->list_apps();
        //foreach( $modiles as $m ) print_r( $kernel->list_objects($m) );

        //return "test";
        return join("<br />", $blocks );
    } // end index_list_apps

?>