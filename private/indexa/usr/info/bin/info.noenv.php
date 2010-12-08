<?

    function info_error () {
        global $kernel;

        echo "Error: $errmsg\n";
        if( $filename ) echo "File: $filename\n";
        if( $linenum ) echo "Line: $linenum\n";
        echo "\n";
    } // end info_error

    function info_warning () {
        global $kernel;

        $kernel->templ->display("warning.tpl");
    } // end info_warning

    function info_info () {
        global $kernel;

        $kernel->templ->display("info.tpl");
    } // end info_info

?>