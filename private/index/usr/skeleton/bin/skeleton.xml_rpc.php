<?

    /**
    * @name skeleton
    * @date
    * @author
    * @expires
    *
    */

    /**
    * @name skeleton_index
    * @global object $main
    *
    */
    function skeleton_index (){
        global $main;

        return new XML_RPC_Response(new XML_RPC_Value($main->templ->fetch("index.tpl");, "string"));
    } // end skeleton_index

?>