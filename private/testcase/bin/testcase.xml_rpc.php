<?

    /**
    * @name testcase
    * @date
    * @author
    * @expires
    *
    */

    /**
    * @name testcase_index
    * @global object $main
    *
    */
    function testcase_index (){
        global $main;

        return new XML_RPC_Response(new XML_RPC_Value($main->templ->fetch("index.tpl");, "string"));
    } // end testcase_index

?>