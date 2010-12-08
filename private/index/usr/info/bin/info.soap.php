<?

    function info_warning () {
        global $kernel;

        return $kernel->templ->fetch("warning.tpl");
    } // end info_warning

    function info_info () {
        global $kernel;

        return $kernel->templ->fetch("info.tpl");
    } // end info_info

?>