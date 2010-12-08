<?

    function index_index (){
        global $kernel;

        $kernel->templ->display("index.tpl");
    } // end  index_index

    function index_soap_client () {
        global $kernel;
        extract( $kernel->params );

        if( $hostname ){
            $kernel->host = $hostname; //"localhost";
            $kernel->url = $url; //"/apps/?object=soap";
            $kernel->call_from($appname,$objectname,APP_ECHO_ON);
            $kernel->host = null;
            $kernel->url = null;
        }

        $kernel->templ->display("soap_client.tpl");
    } // end index_soap_client

?>