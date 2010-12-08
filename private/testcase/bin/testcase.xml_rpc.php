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
    * @global object $kernel
    *
    */
    function testcase_index (){
        global $kernel;

        return new XML_RPC_Response(new XML_RPC_Value($kernel->templ->fetch("index.tpl");, "string"));
    } // end testcase_index

?>