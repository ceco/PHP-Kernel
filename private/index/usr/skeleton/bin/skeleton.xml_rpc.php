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
    * @global object $kernel
    *
    */
    function skeleton_index (){
        global $kernel;

        return new XML_RPC_Response(new XML_RPC_Value($kernel->templ->fetch("index.tpl");, "string"));
    } // end skeleton_index

?>