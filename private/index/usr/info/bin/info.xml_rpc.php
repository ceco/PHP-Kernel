<?

    function info_warning () {
        global $main;

        return new XML_RPC_Response(new XML_RPC_Value($main->templ->fetch("warning.tpl"), "string"));
    } // end info_warning

    function info_info () {
        global $main;

        return new XML_RPC_Response(new XML_RPC_Value($main->templ->fetch("info.tpl"), "string"));
    } // end info_info

?>