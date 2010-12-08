<?

    function info_error () {
        global $kernel;

        echo serialize($kernel->templ->fetch("error.tpl"));
    } // end info_error

    function info_warning () {
        global $kernel;

        echo serialize($kernel->templ->fetch("warning.tpl"));
    } // end info_warning

    function info_info () {
        global $kernel;

        echo serialize($kernel->templ->fetch("info.tpl"));
    } // end info_info

?>