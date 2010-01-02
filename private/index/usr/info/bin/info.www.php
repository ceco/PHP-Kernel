<?

    function info_error () {
        global $main;

        $main->templ->display("error.tpl");
    } // end info_error

    function info_warning () {
        global $main;

        $main->templ->display("warning.tpl");
    } // end info_warning

    function info_info () {
        global $main;

        $main->templ->display("info.tpl");
    } // end info_info

?>