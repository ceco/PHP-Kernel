<?

    function info_warning () {
        global $kernel;

        return new XML_RPC_Response(new XML_RPC_Value($kernel->templ->fetch("warning.tpl"), "string"));
    } // end info_warning

    function info_info () {
        global $kernel;

        return new XML_RPC_Response(new XML_RPC_Value($kernel->templ->fetch("info.tpl"), "string"));
    } // end info_info

?>