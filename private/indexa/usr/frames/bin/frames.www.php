<?

    /**
    * Module: frames
    *
    */

    if( !function_exists("frames_header") ){

        /**
        * Display header depending on the environment
        *
        * @name frames_header
        * @global object $main
        * @global const ENVIRONMENT
        *
        */
        function frames_header(){
            global $main;

            $main->templ->display("header.tpl");
       } // end frames_header

    }

    if( !function_exists("frames_footer") ){

        /**
        * Display footer depending on the environment
        *
        * @name frames_footer
        * @global object $main
        * @global const ENVIRONMENT
        *
        */
        function frames_footer(){
            global $main;

            $main->templ->display("footer.tpl");
        } // end frames_footer

    }

?> 
