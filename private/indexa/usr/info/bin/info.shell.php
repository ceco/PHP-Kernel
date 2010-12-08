<?

    function info_warning () {
        global $kernel;

        $kernel->templ->display("warning.tpl");
    } // end info_warning

    function info_info () {
        global $kernel;

        $kernel->templ->display("info.tpl");
    } // end info_info

?>