<?

    function index_soap (){
        global $main;
        extract( $main->params );

        return $main->call_from("test","index",APP_ECHO_OFF,ENVIRONMENT_WWW)." SOAP";

        return "<div>SOAP: $test</div>";
    } // end  index_index_soap

    function index_list_apps (){
        global $main;

        $blocks = $main->list_apps();
        //foreach( $modiles as $m ) print_r( $main->list_objects($m) );

        //return "test";
        return join("<br />", $blocks );
    } // end index_list_apps

?>