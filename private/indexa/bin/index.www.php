<?

    function index_index (){
        global $main;

        $main->templ->display("index.tpl");
    } // end  index_index

    function index_soap_client () {
        global $main;
        extract( $main->params );

        if( $hostname ){
            $main->host = $hostname; //"localhost";
            $main->url = $url; //"/apps/?object=soap";
            $main->call_from($appname,$objectname,APP_ECHO_ON);
            $main->host = null;
            $main->url = null;
        }

        $main->templ->display("soap_client.tpl");
    } // end index_soap_client

?>