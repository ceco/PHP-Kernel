<?

    function index_test(){
        global $main;
        extract( $main->params );

        return new XML_RPC_Response(new XML_RPC_Value("XML RPC!", "string"));

    } // end index_index

?>