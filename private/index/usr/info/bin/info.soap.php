<?

    function info_warning () {
        global $main;

        return $main->templ->fetch("warning.tpl");
    } // end info_warning

    function info_info () {
        global $main;

        return $main->templ->fetch("info.tpl");
    } // end info_info

?>